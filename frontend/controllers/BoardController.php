<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;
use yii\web\HttpException;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use app\models\City;
use app\models\SchoolCategory;
use app\models\Board;
use common\models\Photo;
use app\models\Video;
use app\models\Likes;
use app\models\Comments;
use app\models\Newsfeed;
use app\models\Attachments;
use app\models\AuthAssignment;
use app\models\UserDescription;
use app\models\UserPrivacy;
use app\models\UserAvatar;

use frontend\components\ParentController;

class BoardController extends ParentController
{
    public $layout = 'social';

    /**
     * @inheritdoc
     */
    public function beforeAction($event)
    {

        /*
         * Check if it's an Intercooler request, and if so - using ajaxed layout
         * We also need to disable CSRF validation here to prevent token regeneration
         * Count this as a 'barebone' callback, without scripts, js and etc. - only content
         */
        if (Yii::$app->request->get('ic-request') == 'true') {
            $this->layout = 'ajax/social';
        }

        return parent::beforeAction($event);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['*'],
                    ],
                ],
            ],
        ];
    }

    /**
     * User board view: profile itself
     * @param  int $user 		User ID
     * @return array
     */
    public function actionView($userId)
    {
        $model = Board::getByUserId($userId);

        if (!$model) {
            throw new HttpException(404, Yii::t('app', 'User not found!'));
        }

        /* Open Graph: https://github.com/dragonjet/yii2-opengraph */
        /* TODO: https://github.com/niallkennedy/open-graph-protocol-examples/blob/master/profile.html */
        Yii::$app->opengraph->set([
            'title' => $model->userDescription->name. ' '.$model->userDescription->last_name,
            'description' => ArrayHelper::getValue(UserDescription::cultureList(true), $model->userDescription->culture),
            'image' => Url::toRoute([UserAvatar::getAvatarPath($model->id)], true),
            'type' => 'profile'
        ]);

        return $this->render('view', [
            'user' => $model,
        ]);
    }

    public function actionAddboard()
    {
        $data = Yii::$app->request->post();

        $id = (int)$data['idOwnerBoard'];
        $page = (int)$data['page'];
        $text = (!empty($data['text'])) ? $data['text'] : '' ;

        ///для вложений
        if (!empty($data['atype'])) {
            $type = 'board';
            $atype = $data['atype'];
            $aid = $data['aid'];
        }

        if (empty($atype) && empty($text)) {
            echo 'no text';
            return false;
        }

        // Настройки приватности
        $modelPrivacy = UserPrivacy::find()->where("`id` = :id", [':id' => $id])->asArray()->one();
        $commentAbility = UserPrivacy::getPrivacy($modelPrivacy['board_comment'], $id);

        $newBoard = new Board();
        $newBoard->user = $id;
        $newBoard->owner = Yii::$app->user->id;
        $newBoard->created = date("Y-m-d H:i:s");
        $newBoard->text = strip_tags($text);

        if ($newBoard->validate() && ($commentAbility || ($id == Yii::$app->user->id))) {
            $newBoard->save();
            if (!empty($atype)) {
                $res = Attachments::addAttachment($type, $newBoard->id, $atype, $aid);
            }

            $modelNewsFeed = new Newsfeed();
            $modelNewsFeed->elem_type = "board";
            $modelNewsFeed->elem_id = $newBoard->id;
            $modelNewsFeed->user_id = $newBoard->user;
            if ($modelNewsFeed->validate()) {
                $modelNewsFeed->save();
            }
        } else {
            echo 'invalid post';
            return false;
        }

        $record = self::getBoard($id, $page)[0];
        $records = array(
            'id' => $record->id,
            'idOwner' => $record->owner,
            'nameOwner' => $record->getOwnerDescription()->one()->nickname,
            'timeRecord' => self::getTimeRecord(strtotime($record->created)),
            'text' => $record->text,
            'repost' => $record->repost,
            'type' => $record->repost_type,
            'comments'=> $record->comments()->all(),
            'commentsCount'=>$record->comments()->count(),
            'likeCount' => 0,
            'myLike' => Likes::findMyLike("board", $record->id),
            );

        for ($i=0; $i<$records['commentsCount']; $i++) {
            $records['comments'][$i]['created'] = BoardController::getTimeRecord(strtotime($records['comments'][$i]['created']));
        }
        $records['attachment'] = self::getAttachment($record->id);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $records;
    }

    public function actionDelboard()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();

            $idOwnerBoard = $data['idOwnerBoard'];
            $page = $data['page'];
            $idRecord = $data['idRecord'];

            // Выбираем и удаляемя сам пост
            $delMessage = Board::find()->where("id = :id", [':id' => $idRecord])->one();
            $delMessage->delete();

            // Выбираем и удаляем этот пост в ленте
            $delNewsFeed = Newsfeed::find()->where(['elem_type' => 'board', 'elem_id' => $idRecord])->one();
            if (!empty($delNewsFeed)) {
                $delNewsFeed->delete();
            }

            // Чистим упоминание на удаляемый пост во всех репостах
            \Yii::$app->db->createCommand()
                    ->update(Board::tableName(), ['repost' => 0, 'repost_type' => ''], 'repost=' . $idRecord)
                    ->execute();

            $ok = 1;
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'ok' => $ok,
            ];
    }

    public function actionEditboard()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            $idRecord = $data['idRecord'];
            $editText = $data['editText'];

            $editBoard = Board::find()->where("id = :id", [':id' => $idRecord])->one();

            $editBoard->text = $editText;
            $editBoard->created = date("Y-m-d H:i:s");

            if ($editBoard->validate()) {
                $editBoard->save();
            }

            $ok = 1;
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'ok' => $ok,
            ];
    }

    public static function getBoard($idOwnerBoard, $page = 1)
    {
        $query = Board::find()->where("user = :user", [':user' => $idOwnerBoard]);

        $pagination = new Pagination([
                        'defaultPageSize' => 10,
                        'totalCount' => $query->count(),
                        'page' => $page-1,
                    ]);

        return $query->orderBy("id desc")
                         ->offset($pagination->offset)
                         ->limit($pagination->limit)
                         ->all();
    }
}
