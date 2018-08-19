<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%language}}".
 *
 * @property string $id
 * @property string $code
 * @property string $name
 */
class Language extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%language}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['code'], 'string', 'max' => 6],
            [['name'], 'string', 'max' => 255],
            [['code'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    public static function getToSelect(){
        $model = self::find()->orderBy('id')->all();
        $r = [];
        foreach($model AS $v){
            $r[$v->id] = $v->name;
        }
        return $r;
    }
}
