<?php

namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Json;

use frontend\models\Friend;
use frontend\models\UserDescription;

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
            $friends['active'] = [];
            $friends['pending'] = $this->userGlobalData[$this->boardOwnerRelation]['friends']['pending'];
        }

        if (Yii::$app->request->pathInfo == 'friends/online') {
            $friends['active'] = [];
        }

        if (Yii::$app->request->pathInfo == 'friends/search') {
            $friends['active'] = [];
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
              'sort_by',
              'page',
              'is_online'
            ]);

            $response['triggeredBy'] = Yii::$app->request->post('ic-trigger-name');
            $response['search'] = Yii::$app->request->post('search');
            $response['page'] = 0;

            if (!$response['triggeredBy'] && !$response['search']) {
                $response['triggeredBy'] = 'loadmore';
                $response['page'] = (int)$data['page'];
            }

            $friends = UserDescription::findUsersByData($data);

            $pagination = new Pagination([
                'defaultPageSize' => Friend::$friendsPageSize,
                'totalCount' => $friends->count(),
                'page' => $response['page'],
            ]);
            $response['page']++;

            $friends = $friends
              ->offset($pagination->offset)
              ->limit($pagination->limit)
              ->asArray()
              ->all();

            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"friendsFindSuccess":['.Json::encode($response).']}');

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
              'page',
              'is_online'
            ]);

            /* ! TODO Move this chunk into more appropriate place? v REDO this stuff. */
            $response['triggeredBy'] = Yii::$app->request->post('ic-trigger-name') ?? Yii::$app->request->post('ic-trigger-id');
            $response['page'] = (int)$data['page'];

            if (!$response['triggeredBy']) {
                $response['triggeredBy'] = 'loadmore';
            }

            if ($data['search'] && $response['triggeredBy'] != 'loadmore') {
                $response['page'] = 0;
                $response['search'] = $data['search'];
                $response['triggeredBy'] = Yii::$app->request->post('ic-element-id');
            }

            if (empty($data['search']) && $response['triggeredBy'] != 'loadmore') {
                $response['page'] = 0;
                $response['triggeredBy'] = Yii::$app->request->post('ic-element-id');
            }

            $friends = Friend::getUserFriends([$data['friendship_status'], Friend::FRIENDSHIP_STATUS_ONESIDED])
              ->limit(Friend::$friendsPageSize)
              ->asArray()
              ->all();

            $friends = Friend::createFriendsArrayForUser($friends);
            $friends = UserDescription::findUsersByData($data)
              ->andWhere(['{{%user_description}}.id' => $friends]);

            $pagination = new Pagination([
                'defaultPageSize' => Friend::$friendsPageSize,
                'totalCount' => $friends->count(),
                'page' => $response['page'],
            ]);
            $response['page']++;

            $friends = $friends
              ->offset($pagination->offset)
              ->limit($pagination->limit)
              ->asArray()
              ->all();

            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"friendsFilterSuccess":['.Json::encode($response).']}');

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

                /**
                 * Check if we already have friendship between this two users
                 * Here, current user is not an initiator of a friendship - he only accepts a proposal
                 */
                $model = Friend::find()
                  ->where("user1 = :user AND user2 = :friend", [
                    ':user' => $data['friendId'],
                    ':friend' => Yii::$app->user->id
                  ])
                  ->one();

                /* If no friendship is found, it is new request */
                if ($model) {
                    $model->status = Friend::FRIENDSHIP_STATUS_ACTIVE;
                    if ($model->save()) {
                        $headers = Yii::$app->response->headers;
                        $headers->add('X-IC-Trigger', '{"newFriendshipApproved":['.Json::encode((int)$data['friendId']).']}');
                    }
                }
            } else {
                ErrorHandler::triggerHeaderError($model->errors);
            }
        }
    }

    /**
     * [API] Add to friends action
     * @return null
     */
    public function actionRefuse()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Friend(['scenario' => Friend::SCENARIO_ACCEPT_FRIEND]);
            $model->load(Yii::$app->request->post(), '');

            if ($model->validate()) {
                $data = $model->getAttributes([
                  'friendId'
                ]);

                $model = Friend::find()
                  ->where("user1 = :user AND user2 = :friend", [
                    ':user' => $data['friendId'],
                    ':friend' => Yii::$app->user->id
                  ])
                  ->one();

                /* If no friendship is found, it is new request */
                if ($model) {
                    $model->status = Friend::FRIENDSHIP_STATUS_ONESIDED;
                    if ($model->save()) {
                        $headers = Yii::$app->response->headers;
                        $headers->add('X-IC-Trigger', '{"newFriendshipOnesided":['.Json::encode((int)$data['friendId']).']}');
                    }
                }
            } else {
                ErrorHandler::triggerHeaderError($model->errors);
            }
        }
    }

    /**
     * [API] Add to friends action
     * @return null
     */
    public function actionAdd()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Friend(['scenario' => Friend::SCENARIO_ADD_FRIEND]);
            $model->load(Yii::$app->request->post(), '');

            if ($model->validate()) {
                $data = $model->getAttributes([
                  'friendId'
                ]);

                /* Check if we already have friendship between this two users */
                $model = Friend::find()
                  ->where("(user1 = :user AND user2 = :friend) OR
                           (user2 = :user AND user1 = :friend)", [
                    ':user' => Yii::$app->user->id,
                    ':friend' => $data['friendId']
                  ])
                  ->one();

                /* If no friendship is found, it is new request */
                if (!$model) {
                    $model = new Friend();
                    $model->user1 = Yii::$app->user->id;
                    $model->user2 = $data['friendId'];
                    $model->status = Friend::FRIENDSHIP_STATUS_PENDING;

                    if ($model->save()) {
                        $headers = Yii::$app->response->headers;
                        $headers->add('X-IC-Trigger', '{"newFriendAddedSuccess":['.Json::encode((int)$data['friendId']).']}');
                    }
                } else {
                    $response[$data['friendId']] = Yii::t('app', 'Friend is already added, wait for confirmation!');
                    $headers = Yii::$app->response->headers;
                    $headers->add('X-IC-Trigger', '{"newFriendAlreadyAdded":['.Json::encode($response).']}');
                }
            } else {
                ErrorHandler::triggerHeaderError($model->errors);
            }
        }
    }
}
