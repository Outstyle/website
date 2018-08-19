<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_confirm_email".
 *
 * @property integer $id
 * @property string $email
 * @property string $confirm_key
 * @property integer $created
 */
class ConfirmEmail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'z_confirm_email';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'confirm_key', 'created'], 'required'],
            [['created'], 'integer'],
            [['email', 'confirm_key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'confirm_key' => 'Confirm Key',
            'created' => 'Created',
        ];
    }
}
