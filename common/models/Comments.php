<?php

namespace common\models;

use Yii;
use common\components\helpers\ElementsHelper;

/**
 * This is the model class for table "z_comments".
 *
 * @property integer    $id
 * @property string     $elem_type
 * @property int        $elem_id
 * @property int        $user_id
 * @property timestamp  $created
 * @property string     $comment
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * Behavior scenarios for comment add
     * @var string
     */
    const SCENARIO_DEFAULT = 'default';
    const SCENARIO_WITH_ATTACHMENT = 'attachment';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comments}}';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [
              'comment',
              'elem_type',
              'elem_id',
              'user_id'
            ],
            self::SCENARIO_WITH_ATTACHMENT => [
              'comment',
              'elem_type',
              'elem_id',
              'user_id'
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
              ['elem_type', 'elem_id', 'user_id'],
              'required'
            ],
            [
              ['elem_type'],
              'in',
              'range' => ElementsHelper::$allowedElements
            ],
            [
              ['elem_id'],
              'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number',
              'message' => 'COMMENT_NO_ELEMENT'
            ],
            [
              ['comment'],
              'required',
              'message' => 'COMMENT_EMPTY',
              'on' => self::SCENARIO_DEFAULT
            ],
            [
              ['comment'],
              'string',
              'message' => 'COMMENT_SYMBOLS_LIMIT',
              'tooLong' => 'COMMENT_SYMBOLS_TOO_LONG',
              'tooShort' => 'COMMENT_SYMBOLS_TOO_SHORT',
              'min' => 5,
              'max' => 5000
            ],
            [
              ['comment'],
              'filter',
              'filter' => function ($comment) {
                  $comment = strip_tags($comment);
                  return \yii\helpers\Html::encode($comment);
              },
            ],
            [
              ['created'],
              'default', 'value' => date("Y-m-d H:i:s")
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
            'user_id' => 'User ID',
            'created' => 'Created',
            'comment' => 'Comment',
        ];
    }


    /* Relations */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUserDescription()
    {
        return $this->hasOne(\common\models\user\UserDescription::className(), ['id' => 'user_id']);
    }

    public function getLikes()
    {
        return $this->hasMany(\frontend\models\Likes::className(), ['elem_id' => 'id']);
    }

    public function getAttachments()
    {
        return $this->hasMany(\frontend\models\Attachments::className(), ['elem_id' => 'id']);
    }
}
