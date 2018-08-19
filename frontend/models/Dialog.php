<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_dialog".
 *
 * @property integer $id
 * @property string $created
 * @property string $name
 */
class Dialog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dialog}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Created',
            'name' => 'Name',
        ];
    }

    public static function createDialog(){
        $model = new Dialog();
        $model->save();
        return $model->id;
    }
}
