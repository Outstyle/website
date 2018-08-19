<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_newsfeed".
 *
 * @property integer $id
 * @property string $elem_type
 * @property string $elem_id
 * @property string $user_id
 */
class Newsfeed extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%newsfeed}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['elem_type', 'elem_id', 'user_id'], 'required'],
            [['elem_id', 'user_id'], 'integer'],
            [['elem_type'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'elem_type' => 'Elem Type',
            'elem_id' => 'Elem ID',
            'user_id' => 'User ID',
        ];
    }


    
}
