<?php
namespace frontend\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\data\Pagination;
use common\CImageHandler;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use frontend\models\Dialog;
use frontend\models\Friend;
use frontend\models\Message;
use frontend\models\DialogMembers;
use frontend\models\UserDescription;

use frontend\components\ParentController;

class ChatController extends ParentController {

    public $layout='main-new';
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
        ////те с кем был диалог ////
        $models = self::getDialogs();
        $countModels = count($models);
        $modelDialogs = array();
        $array_id = array();

        for($i=0; $i<$countModels; $i++){
            $user_id = ($models[$i]['sender_id'] == Yii::$app->user->id) ? $models[$i]['recipient_id'] : $models[$i]['sender_id'];
            $array_id[] = $user_id;

            $modelDialogs[$i] = array(
                'dialog' => $models[$i]['dialog'],
                'user_id' => $user_id,
                'user_name' => Message::getUserNickname($user_id),
                'online' => ChatController::getIndicator(Message::getLastvisit($user_id)),
                'countNewMessage' => Message::countNewMessageDialog($models[$i]['dialog']),
                'created' => ChatController::getTimeRecord(strtotime($models[$i]['created'])),
                'status' => $models[$i]['status'],
                'lastMessage' => Message::getLastMessage($models[$i]['dialog']),
                );
            $modelDialogs[$i]['lastMessage'] = (iconv_strlen($modelDialogs[$i]['lastMessage'], 'UTF-8') > 140) ? mb_substr($modelDialogs[$i]['lastMessage'], 0, 140, 'UTF-8')."...": $modelDialogs[$i]['lastMessage'];
        }

        ////оставшийся список друзей с кем не было диалога////
        $modelFriends = self::getFriends();
        $countModelFriends = count($modelFriends);
        $iCount = count($modelDialogs)-1;

        for($j=0; $j<$countModelFriends; $j++){
            $user_id = ($modelFriends[$j]['user1'] == Yii::$app->user->id) ? $modelFriends[$j]['user2'] : $modelFriends[$j]['user1'];
            if(array_search($user_id, $array_id) !== false){
                continue;
            }
            $iCount++;

            $modelDialogs[$iCount] = array(
                'dialog' => "",
                'user_id' => $user_id,
                'user_name' => Message::getUserNickname($user_id),
                'online' => ChatController::getIndicator(Message::getLastvisit($user_id)),
                'lastMessage' => "",
                'created' => "",
                );
        }

        return $this->render('index', [
            'modelDialogs' => $modelDialogs,
        ]);
    }

    public static function getFriends()
    {
        $model = Friend::find()->where(
                                "(user1 = :user AND status = :status) OR (user2 = :user AND status = :status)", 
                                [':user' => Yii::$app->user->id, ':status' => 1])
                                ->asArray()->all();
        return $model;
    }

    public static function getDialogs()
    {
        $model = (new Query())
          ->from([
            '{{%message}}' => (new Query())
              ->from('{{%message}}')
              ->where("recipient_id = :user OR sender_id = :user",[':user'=>Yii::$app->user->id])
              ->orderBy([
                'id' => SORT_DESC
              ])
            ])
          ->groupBy('dialog')
          ->orderBy([
                'id' => SORT_DESC
              ])
          ->all();

          return $model;

    }

    public function actionOnedialog()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $data = Yii::$app->request->get();
        $dialogId = null;
        $response = array();
        
//        if ( isset($data['user_id']) && $data['user_id'] ) {
//            $dialogList = DialogMembers::find()
//                    ->where('user = :user', [':user' => (int)$data['user_id']])
//                    ->asArray()
//                    ->all();
//            foreach( $dialogList as $key => &$val ) {
//                if ( $val['user'] == Yii::$app->user->id ) {
//                    $dialogId = $val['dialog'];
//                    break;
//                }
//            }
//        }
        
        if ( empty($data['page']) ) {
            $data['page'] = 1;
        }
        
        if ( isset($data['dialog']) && $data['dialog'] ) {
            $loadMore = true;
            $check = self::getMessage($data['dialog'], $data['page']+1);
            if(empty($check)){
                $loadMore = false;
            }

            $model = self::getMessage($data['dialog'], $data['page']);
            $countModel = count($model);
            $messages = array();
            $idDialog = $model[0]['dialog'];
            for($i=0; $i<$countModel; $i++){
                $messages[$i] = array(
                    'idMessage' => $model[$i]->id,
                    'user_id' => $model[$i]['sender_id'],
                    'message' => $model[$i]['message'],
                    'created' => ChatController::getTimeRecord(strtotime($model[$i]['created'])),
                    'status' => $model[$i]['status'],
                    );
                if($model[$i]['status'] == 0 && $model[$i]['recipient_id'] == Yii::$app->user->id){
                    $apdateModel = Message::findOne(['id' => $model[$i]['id']]);
                    $apdateModel->status = 1;
                    $apdateModel->save();
                }
            }

            $response = ['loadMore' => $loadMore, 'messages' => $messages, 'idDialog' => $idDialog];

//            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//            return $response;

        } elseif ( isset($data['user_id']) && $data['user_id'] ){
            $dialogList = DialogMembers::find()
                    ->where('user = :user', [':user' => (int)$data['user_id']])
                    ->asArray()
                    ->all();
            foreach( $dialogList as $key => &$val ) {
                if ( $val['user'] == Yii::$app->user->id ) {
                    $dialogId = $val['dialog'];
                    break;
                }
            }
//            var_dump($dialogList);
//            var_dump($dialogId);
//            die();
            $response = ['user_id' => $data['user_id'], 'idDialog' => ($dialogId ? $dialogId : 0)];
        }
        return $response;
    }

    public static function getMessage($id, $page = 1)
    {
        //все сообщения в диалоге
        $query = Message::find()->where(['dialog' => $id]);

        $pagination = new Pagination([
            'defaultPageSize' => 8,
            'totalCount' => $query->count(),
            'page' => $page-1,
        ]);

        return $query->orderBy("id desc")
             ->offset($pagination->offset)
             ->limit($pagination->limit)
             ->all();

    }

    public function actionSendmessage()
    {
        $data = Yii::$app->request->get();

        $dialog = null;
        if( isset($data['dialog']) && ($data['dialog'] > 0) ) {
            $dialog = $data['dialog'];
        }
        
        $recipient = null;
        if( isset($data['user']) && $data['user'] ){
            if ( $data['user'] == Yii::$app->user->id ) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                    'error' => true,
                    'message' => Yii::t('app', 'Самому себе запрещено писать сообщение.')
                ];
            }
            $recipient = $data['user'];
        }
        
        $text = trim($data['message']);

        if( $dialog && !empty($text) ) {
            $users = DialogMembers::getUsers($dialog);
            if(!empty($users)){
                $countUsers = count($users);
                for($i=0; $i<$countUsers; $i++){
                    $model = new Message();
                    $model->sender_id = Yii::$app->user->id;
                    $model->recipient_id = $users[$i]['user'];
                    $model->dialog = $dialog;
                    $model->message = $text;
                    $model->status = 0;
                    if($model->validate()){
                        $model->save();
                    }
                }

                $lastMessage = Message::find()->where(['dialog' => $dialog, 'sender_id' => Yii::$app->user->id])->orderBy('id desc')->limit(1)->one();

                $message = array(
                    'idMessage' => $lastMessage->id,
                    'user_id' => $lastMessage->sender_id,
                    'message' => $lastMessage->message,
                    'created' => ChatController::getTimeRecord(strtotime($lastMessage->created)),
                    'status' => $lastMessage->status,
                    );

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $message;
            }
        } elseif ( $recipient && !empty($text) ){

            $userDialogs = DialogMembers::find()->select('dialog')->where(['user' => Yii::$app->user->id])->all();
            $countDialogs =  count($userDialogs);
            $idDialogs = array();
            for($i=0; $i<$countDialogs; $i++){
                $idDialogs[] = $userDialogs[$i]->dialog;
            }

            $checkDialogs = DialogMembers::find()->where(['user' => $recipient, 'dialog' => $idDialogs])->all();

            if(!empty($checkDialogs)){

                $count = count($checkDialogs);

                if($count == 1){
                    $dialogId = $checkDialogs[0]->dialog;
                }elseif($count > 1){
                    for($i=0; $i<$count; $i++){
                        $memberDialogs = DialogMembers::find()->where(['dialog' => $checkDialogs[$i]->dialog])->count();
                        if($memberDialogs == 2){
                            $dialogId = $checkDialogs[$i]->dialog;
                            break;
                        }
                    }
                }

                $saveMessage = Message::newPrivaceMessage($dialogId, $recipient, $text);

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $saveMessage;
            } else {
                $dialogId = Dialog::createDialog();
                $newDialogMemberOne = DialogMembers::addMemberDialog($dialogId, Yii::$app->user->id);
                $newDialogMemberTwo = DialogMembers::addMemberDialog($dialogId, $recipient);
                $saveMessage = Message::newPrivaceMessage($dialogId, $recipient, $text);
                $lastMessage = Message::find()->where(['dialog' => $dialogId, 'sender_id' => Yii::$app->user->id])->orderBy('id desc')->limit(1)->one();

                $message = array(
                    'idMessage' => $lastMessage->id,
                    'user_id' => $lastMessage->sender_id,
                    'message' => $lastMessage->message,
                    'created' => ChatController::getTimeRecord(strtotime($lastMessage->created)),
                    'status' => $lastMessage->status,
                    );
                $message['idDialog'] = $dialogId;

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $message;
            }
        }
    }

    public function actionChecknewmessage()
    {
        $data = Yii::$app->request->get();
        
        $idDialog = isset($data['idDialog']) ? $data['idDialog'] : 0;
        $idMessage = isset($data['idMessage']) ? $data['idMessage'] : 0;

        if ( $idDialog && $idMessage ) {
            
            $checkNewMessage = Message::find()->where('dialog = :dialog AND id > :id', [':dialog' => $idDialog, ':id' => $idMessage])->asArray()->all();       

            if(!empty($checkNewMessage)){
                $countModel = count($checkNewMessage);
                $messages = array();
                for($i=0; $i<$countModel; $i++){
                    $messages[$i] = array(
                        'idMessage' => $checkNewMessage[$i]['id'],
                        'user_id' => $checkNewMessage[$i]['sender_id'],
                        'message' => $checkNewMessage[$i]['message'],
                        'created' => ChatController::getTimeRecord(strtotime($checkNewMessage[$i]['created'])),
                        'status' => $checkNewMessage[$i]['status'],
                        );
                    if($checkNewMessage[$i]['status'] == 0 && $checkNewMessage[$i]['recipient_id'] == Yii::$app->user->id){
                        $apdateModel = Message::findOne(['id' => $checkNewMessage[$i]['id']]);
                        $apdateModel->status = 1;
                        $apdateModel->save();
                    }
                }
                $response = array('messages' => $messages, 'idDialog' => $idDialog);
            }else{
                $response = false;
            }
        
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $response;
        } else {
            echo 'Wrong data!';
            return false;
        }
    }

    public static function getTimeRecord($time)
    {
        if( date('d m Y', time()) === date('d m Y', $time)){
            return "Сегодня в ".date('H:i', $time);
        }elseif( (date('d', time())-date('d', $time)) == 1 && date('m Y', time()) == date('m Y', $time)){
            return "Вчера в ".date('H:i', $time);
        }else{
            $monthes = array(
            1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
            5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
            9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря');
            
            return date('j', $time).' '. $monthes[(int)date('m', $time)].' в '.date('H:i', $time);
        }
    }

    public static function getIndicator($lastvisit)
    {
        if( time()-$lastvisit < 900){
            return '1';
        }else{
            return '0';
        }
    }

    public function actionSearch()
    {
        $data = Yii::$app->request->get();
        
        $search = $data['search'];

        $model = UserDescription::find()->where(['nickname' => $search])->one();
        if(!empty($model)){
            $dialog = Message::find()->where(
                            "(recipient_id = :user AND sender_id = :id) OR (recipient_id = :id AND sender_id = :user)",
                            [':user'=>Yii::$app->user->id, ':id' => $model->id])
                                     ->groupBy('dialog')->asArray()->all();
            if(!empty($dialog)){

                $modelDialogs = array(
                    'dialog' => $dialog[0]['dialog'],
                    'user_id' => $model->id,
                    'user_name' => Message::getUserNickname($model->id),
                    'online' => ChatController::getIndicator(Message::getLastvisit($model->id)),
                    'countNewMessage' => Message::countNewMessageDialog($dialog[0]['dialog']),
                    'created' => ChatController::getTimeRecord(strtotime($dialog[0]['created'])),
                    'status' => $dialog[0]['status'],
                    'lastMessage' => Message::getLastMessage($dialog[0]['dialog']),
                    );
                $modelDialogs['lastMessage'] = (iconv_strlen($modelDialogs['lastMessage'], 'UTF-8') > 140) ? mb_substr($modelDialogs['lastMessage'], 0, 140, 'UTF-8')."...": $modelDialogs['lastMessage'];
        
            }else{
                $modelDialogs = array(
                    'dialog' => "",
                    'user_id' => $model->id,
                    'user_name' => Message::getUserNickname($model->id),
                    'online' => ChatController::getIndicator(Message::getLastvisit($model->id)),
                    'lastMessage' => "",
                    );
            }
        }else{
            $modelDialogs = false;
        }
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $modelDialogs;
    }
}