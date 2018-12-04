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
 * @property int      $recipient_id
 * @property string   $message
 * @property int      $dialog
 * @property string   $created
 * @property int      $status
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * Message is stored in DB but not yet delivered to recipient
     * @var integer
     */
    const MESSAGE_STATUS_UNREAD = 0;

    /**
     * Message is delivered to recipient
     * @var integer
     */
    const MESSAGE_STATUS_READ = 1;


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
            [['sender_id', 'recipient_id', 'message', 'dialog'], 'required'],
            [['sender_id', 'recipient_id', 'dialog', 'status'], 'integer'],
            [['created'], 'safe'],
            [['message'], 'string', 'max' => 4096],
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
            'recipient_id' => Yii::t('app', 'Recipient ID'),
            'message' => Yii::t('app', 'Message'),
            'dialog' => Yii::t('app', 'Dialog'),
            'created' => Yii::t('app', 'Created'),
            'status' => Yii::t('app', 'Status'),
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
