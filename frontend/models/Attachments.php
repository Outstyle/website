<?php

namespace app\models;

use Yii;
use common\components\helpers\ElementsHelper;

/**
 * This is the model class for table "z_attachments".
 *
 * @property integer  $id
 * @property integer  $elem_type
 * @property integer  $elem_id
 * @property integer  $attachment_type
 * @property integer  $attachment_id
 * @property integer  $user_id
 */
class Attachments extends \yii\db\ActiveRecord
{
    const SCENARIO_DEFAULT = 'default';
    const SCENARIO_PREPARE_ATTACHMENT = 'prepare';

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
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [
              'attachment_type',
              'attachment_id',
              'elem_type',
              'elem_id',
              'user_id'
            ],
            self::SCENARIO_PREPARE_ATTACHMENT => [
              'attachment_type',
              'attachment_id',
              'elem_type'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['elem_type', 'elem_id', 'attachment_type', 'attachment_id', 'user_id'],
                'required'
            ],
            [
                ['elem_type', 'attachment_type', 'elem_id', 'attachment_id', 'user_id'],
                'integer'
            ],
            [
                ['elem_id'],
                'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number',
                'message' => 'ATTACHMENT_NO_ELEMENT'
            ],
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
            'user_id' => 'Owner ID',
        ];
    }

    /**
     * Adds an attachment to DB
     * @param string|integer      $attachment_type  Attachment type, i.e. 'photo' (@see: ElementsHelper::$allowedElements)
     * @param integer             $attachment_id
     * @param string|integer      $elem_type        Parent element of an attach, i.e. 'comments' (@see: ElementsHelper::$allowedElements)
     * @param integer             $elem_id
     */
    public static function addAttachment($attachment_type = '', $attachment_id = 0, $elem_type = '', $elem_id = 0)
    {
        $attachment = new self();
        $attachment->attachment_type = is_numeric($attachment_type) ? $attachment_type : ElementsHelper::getElementIdByControllerId($attachment_type);
        $attachment->attachment_id = (int)$attachment_id;
        $attachment->elem_type = ElementsHelper::getElementIdByControllerId($elem_type);
        $attachment->elem_id = (int)$elem_id;
        $attachment->user_id = (int)Yii::$app->user->id;

        if ($attachment->validate()) {
            $attachment->save();
            return $attachment->id;
        } else {
            return $attachment->errors;
        }
    }

    /**
     * Prepares an attachment for storing in user localstorage, without adding it to DB
     * @param string  $attachment_type  Attachment type, i.e. 'photo' (@see: ElementsHelper::$allowedElements)
     * @param integer $attachment_id
     * @param string  $elem_type        Parent element of an attach, i.e. 'comments' (@see: ElementsHelper::$allowedAttachments)
     */
    public static function prepareAttachment($attachment_type = '', $attachment_id = 0, $elem_type = '')
    {
        $attachment = new self(['scenario' => self::SCENARIO_PREPARE_ATTACHMENT]);
        $attachment->attachment_type = ElementsHelper::getElementIdByControllerId($attachment_type);
        $attachment->attachment_id = (int)$attachment_id;
        $attachment->elem_type = ElementsHelper::getElementIdByControllerId($elem_type);

        if ($attachment->validate()) {
            return $attachment;
        } else {
            return $attachment->errors;
        }
    }

    /**
     * Parses string for attachment's element type and ID (localstorage mainly)
     * @param  string $str  Any string to check (valid attachment looks like '8_32' or '6_2')
     * @return array        [0] - element validated TYPE, [1] - element validated ID
     */
    public static function parseStringForAttachment($str = '')
    {
        $str = explode('_', $str);
        if ($str[0] && !isset(ElementsHelper::$allowedElements[$str[0]])) {
            $str[0] = 0;
        }

        return $str;
    }

    /**
     * Parses string for attachments (localstorage mainly)
     * @param  string $str  Any string to check
     * @return array        [0] - element validated TYPE, [1] - element validated ID
     */
    public static function parseStringForAttachments($str = '')
    {
        $attachments = [];
        if (strpos($str, ',') !== false) {
            $str = explode(',', $str);
            foreach ($str as $k => $v) {
                $attachments[$k] = self::parseStringForAttachment($v);
            }
            return $attachments;
        } else {
            $attachments[0] = self::parseStringForAttachment($str);
        }

        return $attachments;
    }

    /**
     * Gets actual attachment models by it's type (i.e. 8 is for video) an that type ID
     * @param  integer  $attachment_type
     * @param  integer  $attachment_id
     * @return array    List of actual elements to work with (video model, photo model, etc.)
     */
    public static function getAttachmentByTypeAndId($attachment_type = 0, $attachment_id = 0)
    {
        switch ($attachment_type) {

            # Video
            case 8:
              $activeAttachments = Video::find()->where(['id' => $attachment_id])->asArray()->one();
              break;

        }

        return $activeAttachments ?? [];
    }

    /**
     * Deletes attachment from DB
     * @param  integer $id
     * @return boolean
     */
    public static function deleteAttachment($id = 0)
    {
        $uid = Yii::$app->user->id;
        return self::find()->where(array('id' => $id))->one()->delete();
    }
}
