<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%message_status}}".
 * This model serves as a common one, both for backend and frontend.
 *
 * Only general yii2 built-in methods should be used here!
 * If you want to add some modifications or new methods, you should extend from this model.
 * @see frontend/models/MessageStatus.php
 * @see backend/models/MessageStatus.php
 *
 * Also, all the relations with other models should be declared in this common model.
 *
 * @property int      $id
 * @property int      $dialog
 * @property int      $user
 * @property int      $message_id
 * @property int      $status
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class MessageStatus extends \yii\db\ActiveRecord
{
    const MESSAGE_STATUS_UNREAD = 0;
    const MESSAGE_STATUS_DELIVERED = 1;
    const MESSAGE_STATUS_SELF = 2;
    const MESSAGE_STATUS_ERROR_UNDELIVERED = 3;

    /**
     * Maximum limit of messages to show as a badge in 'Unread messages' i.e. or for paging
     * @var int $messagesNotificationLimit
     */
    public static $messagesNotificationLimit = 99;

    /**
     * Maximum limit of unread messages to show (overall)
     * @var int $messagesUnreadLimit
     */
    public static $messagesUnreadLimit = 1000;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message_status}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'dialog' => Yii::t('app', 'Dialog'),
            'message_id' => Yii::t('app', 'Message ID'),
            'user' => Yii::t('app', 'User'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['dialog', 'message_id', 'user', 'status'],
                'integer',
                'message' => 'NOT_AN_INT',
            ],
            [
                ['status'],
                'default',
                'value' => self::MESSAGE_STATUS_UNREAD
            ],
            [
                ['status'], 'in', 'range' => [
                    self::MESSAGE_STATUS_UNREAD,
                    self::MESSAGE_STATUS_DELIVERED,
                    self::MESSAGE_STATUS_SELF,
                    self::MESSAGE_STATUS_ERROR_UNDELIVERED
                ],
            ],
        ];
    }

    /* RELATIONS */
    public function getDialogMembers()
    {
        return $this->hasMany(DialogMembers::className(), ['dialog' => 'dialog']);
    }
    public function getMessage()
    {
        return $this->hasOne(Message::className(), ['id' => 'message_id']);
    }
}
