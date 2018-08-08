<?php

namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Json;

use app\models\Friend;
use app\models\UserDescription;

use frontend\components\handlers\ErrorHandler;
use frontend\components\OutstyleSocialController;

class FriendsController extends OutstyleSocialController
{

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
        if (Yii::$app->request->pathInfo == 'friends') {
            $friends['active'] = $this->userGlobalData['friends']['active'];
            $friends['pending'] = $this->userGlobalData['friends']['pending'];
        }

        if (Yii::$app->request->pathInfo == 'friends/online') {
            $friends['active'] = $this->userGlobalData['friends']['online'];
        }

        if (Yii::$app->request->pathInfo == 'friends/search') {
            $friends['active'] = UserDescription::findUsers()
              ->limit(Friend::$friendsPageSize)
              ->asArray()
              ->all();
        }

        return $this->render('index', [
            'friends' => $friends
        ]);
    }


    /**
     * [API] Friends find action
     * @return array
     */
    public function actionFind()
    {
        $model = new UserDescription(['scenario' => UserDescription::SCENARIO_SEARCH]);
        $model->load(Yii::$app->request->post(), '');

        if ($model->validate()) {
            $data = $model->getAttributes([
              'user',
              'age_min',
              'age_max',
              'sex',
              'country',
              'city',
              'culture',
              'search',
              'sort_by'
            ]);
            $friends = UserDescription::findUsersByData($data)->asArray()->all();

            return $this->renderPartial('view', [
              'friends' => [
                'active' => $friends
              ],
            ]);
        } else {
            ErrorHandler::triggerHeaderError($model->errors);
        }
    }

    /**
     * [API] Friends filter action
     * Used when you need to filter existing friends, not find new ones
     * @return array
     */
    public function actionFilter()
    {
        $model = new UserDescription(['scenario' => UserDescription::SCENARIO_FILTER]);
        $model->load(Yii::$app->request->post(), '');

        if ($model->validate()) {
            $data = $model->getAttributes([
              'user',
              'age_min',
              'age_max',
              'sex',
              'country',
              'city',
              'culture',
              'search',
              'sort_by',
              'friendship_status',
              'page'
            ]);

            $friends = Friend::getUserFriends($data['friendship_status'])
              ->limit(Friend::$friendsPageSize)
              ->asArray()
              ->all();
            $friends = Friend::createFriendsArrayForUser($friends);
            $friends = UserDescription::findUsersByData($data)
              ->andWhere(['id' => $friends]);

            $pagination = new Pagination([
                'defaultPageSize' => Friend::$friendsPageSize,
                'totalCount' => $friends->count(),
                'page' => $data['page'],
            ]);

            $friends = $friends
              ->offset($pagination->offset)
              ->limit($pagination->limit)
              ->asArray()
              ->all();

            return $this->renderPartial('view', [
              'friends' => [
                'active' => $friends
              ]
            ]);
        } else {
            ErrorHandler::triggerHeaderError($model->errors);
        }
    }


    /**
     * [API] Accept friend request
     * @return int    Friendship status (@see: @common\models\Friend for status constants)
     */
    public function actionAccept()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Friend(['scenario' => Friend::SCENARIO_ACCEPT_FRIEND]);
            $model->load(Yii::$app->request->post(), '');

            if ($model->validate()) {
                $data = $model->getAttributes([
                  'friendId'
                ]);
                $model = Friend::find()
                  ->where("user1 = :user AND user2 = :friend AND status = :status", [
                    ':user' => Yii::$app->user->id,
                    ':friend' => $data['friendId'],
                    ':status' => 0])
                  ->one();
                dd($model);
                $model->status = 1;
                $model->save();
            } else {
                ErrorHandler::triggerHeaderError($model->errors);
            }
        }
    }
}
