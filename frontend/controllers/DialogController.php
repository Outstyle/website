<?php

namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

use frontend\models\Dialog;
use frontend\models\DialogMembers;
use frontend\models\DialogPermissions;

use frontend\components\handlers\ErrorHandler;
use frontend\components\OutstyleSocialController;

class DialogController extends OutstyleSocialController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($event)
    {
        if (!parent::beforeAction($event)) {
            return false;
        }

        if (!DialogPermissions::checkActionAccessByUserID($event->id, Yii::$app->user->id)) {
            return false;
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
     * [API] Dialogues list
     * @return string HTML
     */
    public function actionList()
    {
        $dialogs = Dialog::getByUserId(Yii::$app->user->id);

        $pagination = new Pagination([
            'defaultPageSize' => Dialog::$dialogPageSize,
            'totalCount' => $dialogs->count(),
            'page' => 0,
        ]);

        $dialogs = $dialogs
            ->orderBy(['dialog' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        $dialogs = Dialog::setupData($dialogs);

        $response['triggeredBy'] = Yii::$app->request->post('ic-trigger-name') ?? Yii::$app->request->post('ic-trigger-id');
        $response['page'] = 0;

        if (!$response['triggeredBy']) {
            $response['triggeredBy'] = 'loadmore';
            $response['page'] = 1;
        }

        $headers = Yii::$app->response->headers;
        $headers->add('X-IC-Trigger', '{"dialogsLoaded":['.Json::encode($response).']}');

        return $this->renderPartial('index', [
            'dialogs' => $dialogs
        ]);
    }

    /**
     * [API] Dialogue: add new
     * @return string HTML
     */
    public function actionAdd()
    {
        $friendsToAdd = Yii::$app->request->post('selected');
        $headers = Yii::$app->response->headers;

        /* If dialog with this user or users already exists */
        if ($dialogId = DialogMembers::isAlreadyInDialog($friendsToAdd)) {
            $headers->add('X-IC-Trigger', '{"dialogAlreadyExists":['.$dialogId.']}');
            return;
        }

        $dialog = new Dialog();
        $dialog->modified = date('Y-m-d h:i:s', strtotime('+3 hours')); /* FIXME: Needs rewrite to unixtimestamp */
        if ($dialog->save()) {
            if (is_array($friendsToAdd)) {
                foreach ($friendsToAdd as $friendId) {
                    DialogMembers::addMemberToDialog($friendId, $dialog->id);
                }
            }
            DialogMembers::addMemberToDialog(Yii::$app->user->id, $dialog->id);
            DialogMembers::setDialogOwner(Yii::$app->user->id, $dialog->id);

            $headers->add('X-IC-Trigger', '{"dialogCreated":['.$dialog->id.']}');
        }
    }

    /**
     * [API] Dialogue: update existing
     */
    public function actionUpdate()
    {
        $data = Yii::$app->request->post();
        $headers = Yii::$app->response->headers;
        $response = [];

        if (Yii::$app->request->isAjax) {
            $model = Dialog::findOne((int)$data['dialog']);
            $model->load($data);

            if ($model->validate()) {
                /* TODO: Set `modified` attr on beforeUpdate */
                $model->update();

                if ($data['Dialog']['name']) {
                    $response['name'] = urlencode($model->name);
                }

                $headers->add('X-IC-Trigger', '{"dialogUpdated":['.Json::encode($response).']}');
            } else {
                ErrorHandler::triggerHeaderError($model->errors);
            }
        }
    }
}
