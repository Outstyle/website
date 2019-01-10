<?php

namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\HttpException;

use frontend\models\Message;
use frontend\models\MessageStatus;
use frontend\models\Dialog;
use frontend\models\DialogMembers;

use frontend\components\handlers\ErrorHandler;
use frontend\components\OutstyleSocialController;
use yii\helpers\ArrayHelper;

class MessagesController extends OutstyleSocialController
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


    public function actionIndex()
    {
        return $this->render('index', [
            'messages' => [],
            'dialogMembers' => [],
            'dialogId' => 0,
            'dialog' => [],
        ]);
    }

    public function actionView(int $dialogId = 0)
    {

        /* Filling dialogs var only if current user is a member of currently browsable dialog */
        if (DialogMembers::isDialogMember(Yii::$app->user->id, $dialogId)) {
            $messages = Message::getByDialogId($dialogId)->asArray()->all();
            $dialogMembers = DialogMembers::getDialogMembersById($dialogId);
            $dialogMembers = DialogMembers::setupData($dialogMembers);
            $dialog = Dialog::findOne($dialogId);
        }

        $response['dialogId'] = $dialogId;

        $headers = Yii::$app->response->headers;
        $headers->add('X-IC-Trigger', '{"messagesLoaded":['.Json::encode($response).']}');

        return $this->render('index', [
            'messages' => $messages ?? [],
            'dialogMembers' => $dialogMembers ?? [],
            'dialogId' => $dialogId,
            'dialog' => $dialog ?? [],
        ]);
    }

    /**
     * [API] Add message action
     * @return null
     */
    public function actionAdd()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Message();
            $model->load(Yii::$app->request->post(), '');
            $model->sender_id = 14;
            $model->message = 'Test message';
            $model->dialog = 1;

            if ($model->validate()) {
                $data = $model->getAttributes([
                    'message'
                ]);
                $model->save();
            } else {
                ErrorHandler::triggerHeaderError($model->errors);
            }
        }
    }

    /**
     * [API] Get message action (check for new)
     * @return string
     */
    public function actionGet()
    {
        if (Yii::$app->request->isAjax) {
            $dialogId = Yii::$app->request->get('dialogId');
            $unreadMessages = MessageStatus::getUnread($dialogId, $userId = Yii::$app->user->id)
                ->asArray()
                ->all();
            $messages = ArrayHelper::getColumn($unreadMessages, 'message');
            $dialogMembers = DialogMembers::getDialogMembersById($dialogId);
            $dialogMembers = DialogMembers::setupData($dialogMembers);

            $response = count($unreadMessages);

            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"messageNew":['.Json::encode($response).']}');

            return $this->render('_singlemessage', [
                'messages' => $messages,
                'dialogMembers' => $dialogMembers,
            ]);
        }
    }
}
