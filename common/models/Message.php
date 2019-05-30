<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

namespace common\models;

use Yii;
use frontend\models\UserAvatar;

/**
 * This is the model class for table "{{%message}}".
 * This model serves as a common one, both for backend and frontend.
 *
 * Only general yii2 built-in methods should be used here!
 * If you want to add some modifications or new methods, you should extend from this model.
 * @see frontend/models/Message.php
 * @see backend/models/Message.php
 *
 * Also, all the relations with other models should be declared in this common model.
 *
 * @property int      $id
 * @property int      $sender_id
 * @property string   $message
 * @property int      $dialog
 * @property string   $created
 * @property int      $type
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * Usual user message
     * @var integer
     */
    const MESSAGE_TYPE_ORDINARY = 0;

    /**
     * System message (i.e. fired for everyone by admins)
     * @var integer
     */
    const MESSAGE_TYPE_SYSTEM = 1;

    /**
     * Maximum limit of messages to show in particular dialog
     * @var int $messagesListLimit
     */
    public static $messagesListLimit = 50;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['sender_id', 'dialog'],
                'required',
                'message' => 'REQUIRED',
            ],
            [
                ['sender_id', 'dialog', 'type'],
                'integer',
                'message' => 'NOT_AN_INT',
            ],

            [
                ['message'],
                'required',
                'message' => 'MESSAGE_EMPTY',
            ],
            [
                ['message'],
                'string',
                'message' => 'MESSAGE_SYMBOLS_LIMIT',
                'tooLong' => 'MESSAGE_SYMBOLS_TOO_LONG',
                'tooShort' => 'MESSAGE_SYMBOLS_TOO_SHORT',
                'min' => 1,
                'max' => 2048
            ],
            [
                ['message'],
                'filter',
                'filter' => function ($message) {
                    /* FIXME: Take out to string helper */
                    $message = strip_tags($message);
                    return \yii\helpers\Html::encode($message);
                },
            ],
            [
                ['type'],
                'default',
                'value' => self::MESSAGE_TYPE_ORDINARY
            ],
            [
                ['type'], 'in', 'range' => [
                    self::MESSAGE_TYPE_ORDINARY,
                    self::MESSAGE_TYPE_SYSTEM
                ],
            ],
            [
                ['created'],
                'safe'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sender_id' => Yii::t('app', 'Sender ID'),
            'message' => Yii::t('app', 'Message'),
            'dialog' => Yii::t('app', 'Dialog'),
            'created' => Yii::t('app', 'Created'),
            'type' => Yii::t('app', 'Type'),
        ];
    }


    /* RELATIONS */
    public function getDialog()
    {
        return $this->hasOne(Dialog::className(), ['id' => 'dialog']);
    }
    public function getUserAvatar()
    {
        return $this->hasOne(UserAvatar::className(), ['id' => 'sender_id']);
    }
}
