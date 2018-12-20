<?php

namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Json;

use frontend\models\Message;
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
            $messages = Message::getByDialogId($dialogId)
                ->asArray()
                ->all();
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
}
