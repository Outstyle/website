<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_events_category".
 *
 * @property integer $id
 * @property string $name
 */
class EventsCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%events_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
            'name' => 'Name',
        ];
    }

    public static function getCategory(){
        $model = self::find()->orderBy('id')->all();
        $r = [];
        foreach($model AS $v){
            $r[$v->id] = $v->name;
        }
        return $r;
    }
}
