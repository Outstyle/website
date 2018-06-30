<?php

namespace frontend\controllers;

/* Check what is not used */
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\data\Pagination;
use yii\web\UploadedFile;
use common\CImageHandler;
use yii\web\HttpException;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use yii\helpers\Json;

use app\models\Friend;
use app\models\UserDescription;

use frontend\components\CsrfController;

class FriendsController extends CsrfController
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
     * Friends index action
     * @return array
     */
    public function actionIndex()
    {
        $friends = Friend::getUserFriends();

        return $this->render('index', [
            'friends' => $friends,
        ]);
    }

    /**
     * Friends find action
     * @return array
     */
    public function actionFind()
    {
        $data = Yii::$app->request->post();
        $friends = UserDescription::findUsersByData($data);

        if (isset($friends['errors'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"friendsSearchError":['.Json::encode($friends['errors']).']}');
            return;
        }

        return $this->renderPartial('view', [
            'friends' => $friends,
        ]);
    }


    public static function getRequest()
    {
        return UserDescription::find()
                ->with(['user'])
                ->join(
                    'JOIN',
                    '{{%friend}}',
                    '({{%user_description}}.`id` = {{%friend}}.`user1`
                        AND {{%friend}}.`user2` = :user
                        AND {{%friend}}.`status` = :status)',
                    [
                        ':user' => Yii::$app->user->id,
                        ':status' => 0,
                    ])
                ->all();
    }


//////прием заявки
    public function actionAccept()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $id = $data['idUserRequest'];
            $model = Friend::find()
                                ->where(
                                    "user1 = :id AND user2 = :user AND status = :status",
                                    [':id' => $id, ':user' => Yii::$app->user->id, ':status' => 0])
                                ->one();
            $model->status = 1;
            if ($model->validate()) {
                $model->save();
            }
            $ok = 1;
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'ok' => $ok,
            ];
    }

/////отклонение заявки
    public function actionRefuse()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $id = $data['idUserRefuse'];
            $model = Friend::find()
                                ->where(
                                    "user1 = :id AND user2 = :user AND status = :status",
                                    [':id' => $id, ':user' => Yii::$app->user->id, ':status' => 0])
                                ->one();
            $model->delete();
            $ok = 1;
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'ok' => $ok,
            ];
    }
/////удаление из друзей
    public function actionDel()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $id = $data['idDel'];
            $model = Friend::find()
                                ->where(
                                    "(user1 = :id AND user2 = :user AND status = :status)
                                        OR (user1 = :user AND user2 = :id AND status = :status)",
                                    [':id' => $id, ':user' => Yii::$app->user->id, ':status' => 1])
                                ->one();
            $model->delete();
            $ok = 1;
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'ok' => $ok,
            ];
    }

/////отправка заявки в друзья
    public function actionRequest()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $id = $data['idAdd'];
            $checkRequest = Friend::find()
                                ->where(
                                    "(user1 = :id AND user2 = :user)
                                    OR (user1 = :user AND user2 = :id)",
                                    [':id' => $id, ':user' => Yii::$app->user->id])
                                ->one();

            if (empty($checkRequest)) {
                $model = new Friend();
                $model->user1 = Yii::$app->user->id;
                $model->user2 = $id;
                $model->status = 0;
                if ($model->validate()) {
                    $model->save();
                }
                $ok = 1;
            }
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'ok' => $ok,
            ];
    }
}
