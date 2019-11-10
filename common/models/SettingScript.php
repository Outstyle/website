<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "z_setting_script".
 *
 * @property int $id
 * @property string $type
 * @property string $label
 * @property string $param
 * @property string $value
 */

class SettingScript extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'z_setting_script';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
            [['type', 'param'], 'string', 'max' => 128],
            [['label'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'label' => 'Label',
            'param' => 'Param',
            'value' => 'Value',
        ];
    }
}
