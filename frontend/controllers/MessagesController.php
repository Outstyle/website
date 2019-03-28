<?php

namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

use frontend\models\Message;
use frontend\models\MessageStatus;
use frontend\models\Dialog;
use frontend\models\DialogMembers;

use frontend\components\handlers\ErrorHandler;
use frontend\components\OutstyleSocialController;

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
            'messages' => 0,
            'dialogMembers' => 0,
            'dialogId' => 0,
            'dialog' => 0,
        ]);
    }

    public function actionView(int $dialogId = 0)
    {

        /* Filling dialogs var only if current user is a member of currently browsable dialog */
        if (DialogMembers::isDialogMember(Yii::$app->user->id, $dialogId)) {
            $messages = Message::getByDialogId($dialogId)
                ->limit(Message::$messagesListLimit)
                ->orderBy(['id' => SORT_DESC])
                ->asArray()
                ->all();
            $dialogMembers = DialogMembers::getDialogMembersById($dialogId);
            $dialogMembers = DialogMembers::setupData($dialogMembers);
            $dialog = Dialog::findOne($dialogId);
        } else {
            throw new HttpException(403, Yii::t('err', 'Conversation not found'));
        }

        $response['dialogId'] = $dialogId;

        $headers = Yii::$app->response->headers;
        $headers->add('X-IC-Trigger', '{"messagesLoaded":['.Json::encode($response).']}');

        return $this->render('index', [
            'messages' => array_reverse($messages) ?? [],
            'dialogMembers' => $dialogMembers ?? [],
            'dialogId' => $dialogId,
            'dialog' => $dialog ?? [],
        ]);
    }

    /**
     * [API] Add message action
     */
    public function actionAdd()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Message();
            $model->load(Yii::$app->request->post(), '');
            $model->sender_id = Yii::$app->user->id;

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
     * Here we're cancelling short-polling, if user has unread messages
     */
    public function actionGet()
    {
        if (Yii::$app->request->isAjax) {
            $model = new MessageStatus();
            $model->load(Yii::$app->request->get(), '');

            if ($model->validate()) {
                $data = $model->getAttributes(['dialog']);
                $currentUserId = Yii::$app->user->id;

                /* Checking if there is any unread messages for current user arrived between polling ticks? */
                $unreadMessages = MessageStatus::getUnread($data['dialog'], $currentUserId)
                    ->limit(MessageStatus::$messagesNotificationLimit)
                    ->asArray()
                    ->all();

                /* Queue DB and mark all unread messages as 'read', if triggered from last recieved message */
                if (Yii::$app->request->get('ic-trigger-name') == 'message-last') {
                    $unreadMessageIdsArray = ArrayHelper::getColumn($unreadMessages, 'message_id');
                    $deliveredMessages = MessageStatus::setDelivered($data['dialog'], $unreadMessageIdsArray, $currentUserId);
                    return;
                }

                if ($unreadMessages) {
                    $messages = ArrayHelper::getColumn($unreadMessages, 'message');
                    $dialogMembers = DialogMembers::getDialogMembersById($data['dialog']);
                    $dialogMembers = DialogMembers::setupData($dialogMembers);

                    $countMessages = count($unreadMessages);

                    foreach ($messages as $message) {
                        if ($message['sender_id'] == $currentUserId) {
                            $selfMessages[] = $message['id'];
                        }
                    }

                    if (isset($selfMessages)) {
                        $deliveredMessages = MessageStatus::setDelivered($data['dialog'], $selfMessages, $currentUserId);
                    }

                    $headers = Yii::$app->response->headers;
                    $headers->add('X-IC-Trigger', '{"messageNew":['.Json::encode($countMessages).']}');

                    return $this->render('_singlemessage', [
                        'messages' => $messages,
                        'dialogMembers' => $dialogMembers,
                    ]);
                }
            } else {
                ErrorHandler::triggerHeaderError($model->errors);
            }
        }
    }
}
