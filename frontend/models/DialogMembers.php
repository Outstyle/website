<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_dialog_members".
 *
 * @property integer $id
 * @property string $user
 * @property string $dialog
 */
class DialogMembers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dialog_members}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user', 'dialog'], 'required'],
            [['user', 'dialog'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user' => 'User',
            'dialog' => 'Dialog',
        ];
    }

    public function getMessage()
    {
        return $this->hasMany(Message::className(), ['dialog' => 'dialog']);
    }

    public static function getUsers($id){
        return self::find()->where('dialog = :dialog AND user != :user',[':dialog' => $id, ':user' => Yii::$app->user->id])->asArray()->all();
    }

    public static function addMemberDialog($dialog, $user){
        $model = new DialogMembers();
        $model->user = $user;
        $model->dialog = $dialog;
        if($model->validate()){
            $model->save();
            return true;
        }
        return false;
    }
}
