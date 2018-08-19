<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_auth_assignment".
 *
 * @property string $item_name
 * @property string $user_id
 * @property integer $created_at
 *
 * @property AuthItem $itemName
 */
class AuthAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_assignment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['created_at'], 'integer'],
            [['item_name', 'user_id'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => 'Item Name',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    public static function isAdmin($id)
    {
        $model = self::find()->where(['user_id' => $id])->one();
        //$role = $model->item_name;
        
        if(!empty($model) && ($model->item_name == 'mainadmin' || $model->item_name == 'admin')){
            return true;
        }else{
            return false;
        }
    }


}
