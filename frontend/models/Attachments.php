<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "z_attachments".
 *
 * @property integer $id
 * @property string $elem_type
 * @property string $elem_id
 * @property string $attachment_type
 * @property string $attachment_id
 */
class Attachments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attachments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
              [
                'elem_type',
                'elem_id',
                'attachment_type',
                'attachment_id'
              ],
               'required'
            ],

            [
              [
                'elem_id',
                'attachment_id'
              ],
               'integer'
            ],

            [
              [
                'elem_type',
                'attachment_type'
              ],
               'string', 'max' => 255
            ]

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
            'attachment_type' => 'Attachment Type',
            'attachment_id' => 'Attachment ID',
        ];
    }

    public static function addAttachment($type, $id, $atype, $aid)
    {
        $uid = Yii::$app->user->id;
        $attachment = new Attachments();
        $attachment->elem_type = $type; //board , comments, news ...
        $attachment->elem_id = $id; // id записи
        $attachment->attachment_type = $atype; // photo, video, mp3
        $attachment->attachment_id = $aid; // id вложения (фотки или видео)

        if ($attachment->save()) {
            return array('ok' => true);
        }
        return array('ok' => false);
    }

    public static function delAttachment($id)
    {
        $uid = Yii::$app->user->id;
        return Attachments::find()->where(array('id' => $id))->one()->delete();
    }
}
