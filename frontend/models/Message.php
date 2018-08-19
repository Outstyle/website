<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_message".
 *
 * @property integer $id
 * @property string $sender_id
 * @property string $recipient_id
 * @property string $message
 * @property string $dialog
 * @property string $created
 * @property integer $status
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender_id', 'recipient_id', 'message', 'dialog'], 'required'],
            [['sender_id', 'recipient_id', 'dialog', 'status'], 'integer'],
            [['created'], 'safe'],
            [['message'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => 'Sender ID',
            'recipient_id' => 'Recipient ID',
            'message' => 'Message',
            'dialog' => 'Dialog',
            'created' => 'Created',
            'status' => 'Status',
        ];
    }

    public static function getUserNickname($id)
    {
        $model = UserDescription::findOne(['id' => $id])->nickname;
        return $model;
    }

    public static function getLastvisit($id){
        $model = User::findOne(['id' => $id])->lastvisit;
        return $model;
    }

    public static function countNewMessageDialog($id){
        return self::find()->where(['dialog' => $id, 'status' => 0, 'recipient_id' => Yii::$app->user->id])->count();
    }

    public static function getNewMessage(){
        return self::find()->where(['recipient_id' => Yii::$app->user->id, 'status' => 0])->count();
    }

    public static function newPrivaceMessage($dialogId, $recipient, $text){
        $model = new Message();
        $model->sender_id = Yii::$app->user->id;
        $model->recipient_id = $recipient;
        $model->dialog = $dialogId;
        $model->message = $text;
        $model->status = 0;
        if($model->validate()){
            $model->save();
            return true;
        }
        return false;
    }

    public static function getLastMessage($id){
        $model = self::find()->where(['dialog' => $id])->orderBy('id desc')->limit(1)->one();
        return $model->message;
    }

}
