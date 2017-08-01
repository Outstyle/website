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
     * User board: profile itself
     * @param  int $user 		User ID
     * @return array
     */
    public function actionView($userId)
    {
        /* Exceptions */
        if (!is_int($userId) && empty($userId)) {
            throw new HttpException(404, Yii::t('app', 'User not found!'));
        }

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

    public function actionLoadboard()
    {
        $data = Yii::$app->request->get();

        $idOwnerBoard = $data['idOwnerBoard'];
        $page = $data['page'];
        $boards = self::getBoard($idOwnerBoard, $page);
        $loadMore = true;

        $check = BoardController::getBoard($idOwnerBoard, $page+1);
        if (empty($check)) {
            $loadMore = false;
        }

        function makePost($post, $isRepost = false)
        {
            $comentPost = $post->comments()->all();
            $commentsCount = $post->comments()->count();
            $comments = array();

            for ($i=0; $i<$commentsCount; $i++) {
                $comments[$i] = array(
                    'id' => $comentPost[$i]->id,
                    'elem_type' => $comentPost[$i]->elem_type,
                    'elem_id' => $comentPost[$i]->elem_id,
                    'user_id' => $comentPost[$i]->user_id,
                    'user_name' => 0,
                    'created' => BoardController::getTimeRecord(strtotime($comentPost[$i]->created)),
                    'comment' => $comentPost[$i]->comment,
                    'likeCount' => 0,
                    'myLike' => Likes::findMyLike("comments", $comentPost[$i]->id),
                );
            }

            $newPost =  array(
                'id' => $post->id,
                'idOwner' => $post->owner,
                'nameOwner' => $post->getOwnerDescription()->one()->nickname,
                'timeRecord' => BoardController::getTimeRecord(strtotime($post->created)),
                'text' => $post->text,
                'repost' => $post->repost,
                'type' => $post->repost_type,
                'comments'=> $comments,
                'commentsCount'=>$commentsCount,
                'likeCount' => 0,
                'myLike' => Likes::findMyLike("board", $post->id),
                'attachment' => BoardController::getAttachment($post->id),
            );
            if (isset($post->repost) && ($post->repost > 0)) {
                $newPost['repost'] = makePost(Board::findOne(['id' => $post->repost]), true);
            } else {
                $newPost['repost'] = null;
                $newPost['type'] = null;
            }
            if ($isRepost) {
                $newPost['comments'] = [];
                $newPost['commentsCount'] = 0;
            }
            return $newPost;
        }

        foreach ($boards as &$post) {
            $post = makePost($post);
        }

        $isAdmin = AuthAssignment::isAdmin(Yii::$app->user->id);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'board' => $boards,
            'loadMore' => $loadMore,
            'isAdmin' => $isAdmin
            ];
    }

    public static function getAttachment($id)
    {
        $modelAttach = Attachments::findOne(['elem_type'=>'board', 'elem_id'=>$id]);

        if (!empty($modelAttach)) {
            if ($modelAttach->attachment_type == 'video') {
                $modelVideo = Video::findOne(['id'=>$modelAttach->attachment_id]);
                if (!empty($modelVideo)) {
                    return array('type' => 'video', 'modelVideo' => $modelVideo);
                } else {
                    return null;
                }
            }
            if ($modelAttach->attachment_type == 'photo') {
                $modelPhoto = Photo::findOne(['id'=>$modelAttach->attachment_id]);

                if (!empty($modelPhoto)) {
                    return array('type' => 'photo', Photo::findOne(['id'=>$modelAttach->attachment_id]));
                } else {
                    return null;
                }
            }
        } else {
            return null;
        }
    }


    public function actionAttachmentlist()
    {
        $data = Yii::$app->request->get();

        $atype = $data['atype'];

        if ($atype == 'video') {
            $modelVideo = Video::find()->where("user = :user", [':user' => Yii::$app->user->id])->orderBy("id desc")->all();
            if (!empty($modelVideo)) {
                $count = count($modelVideo);
                $videoList = array();
                for ($i=0; $i<$count; $i++) {
                    $videoList[$i]['id'] = $modelVideo[$i]->id;
                    $videoList[$i]['title'] = $modelVideo[$i]->title;
                    $videoList[$i]['urlImg'] = $modelVideo[$i]->url_img;
                    $videoList[$i]['urlIframe'] = $modelVideo[$i]->url_iframe;
                }
                $records = array(
                    'attachment' => "video",
                    'videoList' => $videoList,
                    );
            } else {
                $records = [
                    'error' => true,
                    'message' => Yii::t('app', 'У вас нет загруженных видеозаписей. Сначала загрузите их себе.')
                ];
            }
        } elseif ($atype == 'photo') {
            $modelPhoto = Photo::find()->where("user = :user", [':user' => Yii::$app->user->id])->orderBy("id desc")->all();

            if (!empty($modelPhoto)) {
                $count = count($modelPhoto);
                $photoList = array();
                for ($i=0; $i<$count; $i++) {
                    $photoList[$i]['id'] = $modelPhoto[$i]->id;
                    $photoList[$i]['name'] = $modelPhoto[$i]->name;
                    $photoList[$i]['nameImg'] = $modelPhoto[$i]->img;
                    $photoList[$i]['idOwner'] = $modelPhoto[$i]->user;
                    $photoList[$i]['idAlbum'] = $modelPhoto[$i]->album;
                }
                $records = [
                        'error' => false,
                        'attachment' => "photo",
                        'photoList' => $photoList,
                    ];
            } else {
                $records = [
                    'error' => true,
                    'message' => Yii::t('app', 'У вас нет загруженных фотографий. Сначала загрузите их себе в альбомы.')
                ];
            }
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $records;
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

    public static function getTimeRecord($time)
    {
        if (date('d m Y', time()) === date('d m Y', $time)) {
            return "Сегодня в ".date('H:i', $time);
        } elseif ((date('d', time())-date('d', $time)) == 1 && date('m Y', time()) == date('m Y', $time)) {
            return "Вчера в ".date('H:i', $time);
        } else {
            $monthes = array(
            1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
            5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
            9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря');

            return date('j', $time).' '. $monthes[(int)date('m', $time)].' в '.date('H:i', $time);
        }
    }

    public function actionLike()
    {
        $data = Yii::$app->request->get();

        $elem_type = (!empty($data['elem_type'])) ? $data['elem_type'] : "board";

        $response = Likes::addLike($elem_type, $data['id']);
        $response['likeCount'] = 0;
        $response['myLike'] = Likes::findMyLike($elem_type, $data['id']);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }


    public function actionCountlikes()
    {
        $data = Yii::$app->request->get();

        if (Board::find()->where(['id' => $data['id']])->count() > 0) {
            $response = Likes::countLikes('board', $data['id']);
            $response = Likes::findMyLike('board', $data['id']);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionComment()
    {
        $data = Yii::$app->request->post();

        $atype = (!empty($data['atype'])) ? $data['atype'] : null;
        $aid = (!empty($data['aid'])) ? $data['aid'] : null;
        $text = (!empty($data['text'])) ? $data['text'] : '' ;

        ///для вложений
        if (!empty($data['atype'])) {
            $type = 'board';
            $atype = $data['atype'];
            $aid = $data['aid'];
        }

        if (empty($text)) {
            echo 'no text';
            return false;
        }

        // Сообщение и его владельца, к которому пивется коммент
        $boardPost = Board::find()->where(array('id' => $data['id']))->one();
        $postOwner = $boardPost['owner'];
        $wallOwner = $boardPost['user'];

        // Настройки приватности
        $modelPrivacy = UserPrivacy::find()->where("`id` = :id", [':id' => $wallOwner])->asArray()->one();
        $commentAbility = UserPrivacy::getPrivacy($modelPrivacy['board_comment'], $wallOwner);

        // Если НЕ владелец стены или НЕ разрешено настройками приватнсоти
        if (!(($wallOwner == Yii::$app->user->id) || $commentAbility)) {
            echo 'Post forbidden board owner!';
            return false;
        }

        if ((Board::find()->where(array('id' => $data['id']))->count() > 0)) {
            $response = Comments::addComment('board', $data['id'], $data['text'], $atype, $aid);
        }

        if ($response['ok']) {
            $model = Comments::find()->where(array('elem_type' => 'board', 'elem_id' => $data['id']))->orderBy('id desc')->limit(1)->one();
            $response['comment'] = $model->comment;
            $response['created'] = self::getTimeRecord(strtotime($model->created));
            $response['elem_id'] = $model->elem_id;
            $response['idComment'] = $model->id;
            $response['user_id'] = $model->user_id;
            $response['user_name'] = 0;
            $response['likeCount'] = Likes::countLikes("comments", $model->id);
            $response['myLike'] = Likes::findMyLike("comments", $model->id);

            if (!empty($atype)) {
                $res = Attachments::addAttachment($type, $model->id, $atype, $aid);
            }
            $records['attachment'] = self::getAttachment($model->id);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }


    public function actionCountcomments()
    {
        $data = Yii::$app->request->get();
        if (Board::find()->where(array('id' => $data['id']))->count() > 0) {
            $response = Comments::countComments('board', $data['id']);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionDelcomment()
    {
        $data = Yii::$app->request->get();
        if (Comments::find()->where(array('id' => $data['id']))->count() > 0) {
            $response = Comments::delComment($data['id']);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionEditcomment()
    {
        $data = Yii::$app->request->get();
        if (Comments::find()->where(array('id' => $data['id']))->count() > 0) {
            $response = Comments::editComment('board', $data['id']);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionRepost()
    {
        $data = Yii::$app->request->get();

        $uid = Yii::$app->user->id;
        $rep = Board::find()->where(array('id' => $data['id']))->one();

        if (!$rep) {
            echo 'post doesn\'t exist';
            return false;
        }
        if ($rep->repost) {
            $rep = $rep->repost()->one();
        }
        $exist = Board::find()->where(array('repost' => $rep->id, 'user' => $uid))->count();

        if ($exist) {
            echo 'already exist';
            return false;
        }

        $response = Likes::addLike("board", $data['id']);
        $response['likeCount'] = Likes::countLikes('board', $data['id']);

        $newBoard = new Board();
        $newBoard->user = $uid;
        $newBoard->owner = $uid;
        $newBoard->created = date("Y-m-d H:i:s");
        $newBoard->text = ($data['text']) ? strip_tags($data['text']) : "";
        $newBoard->repost = $rep->id;
        $newBoard->repost_type = 'board';

        if ($newBoard->validate()) {
            $newBoard->save();

            $modelAttach = Attachments::findOne(['elem_type'=>'board', 'elem_id' => $rep->id]);
            if (!empty($modelAttach)) {
                $res = Attachments::addAttachment('board', $newBoard->id, $modelAttach->attachment_type, $modelAttach->attachment_id);
            }
        }
        $ok = true;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'ok' => $ok,
            'likeCount' => $response['likeCount'],
            ];
    }

    public function actionCity($id)
    {
        $countCity = City::find()->where(['country_id' => $id])->count();
        $cities = City::find()->where(['country_id' => $id])->orderby('name')->all();
        if ($countCity > 0) {
            foreach ($cities as $city) {
                echo "<option value='".$city->city_id."'>".$city->name."</option>";
            }
        } else {
            echo "<option value='0'>-Не выбрано-</option>";
        }
    }
}
