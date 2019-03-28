<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%message_status}".
 * This model serves as a frontend one and should always extend common `MessageStatus` model.
 *
 * Only custom methods are stored here.
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class MessageStatus extends \common\models\MessageStatus
{
    /**
     * Sets unread message status for every user, except current
     */
    public static function setUnread(int $dialogId = 0, int $messageId = 0) : int
    {
        $users = DialogMembers::find()
            ->select('user')
            ->where(['dialog' => $dialogId])
            ->asArray()
            ->column();

        foreach ($users as $user) {
            $messageStatusData[] = [$user, $dialogId, $messageId, self::MESSAGE_STATUS_UNREAD];
        }

        return Yii::$app->db->createCommand()
            ->batchInsert('{{%message_status}}', ['user', 'dialog', 'message_id', 'status'], $messageStatusData)
            ->execute();
    }

    /**
     * Sets delivered status for certain messages, for certain user
     */
    public static function setDelivered(int $dialogId = 0, array $messageIds = [0], int $userId = 0) : int
    {
        return self::updateAll(['status' => self::MESSAGE_STATUS_DELIVERED],
            [
                'message_id' => $messageIds,
                'user' => $userId,
                'dialog' => $dialogId
            ]
        );
    }

    /**
     * ActiveQuery for unread messages
     * @param  integer            $dialogId     0 - for ignoring dialogId (default)
     * @param  integer            $userId
     * @return instanceof         yii\db\ActiveQuery
     */
    public static function getUnread(int $dialogId = 0, int $userId = 0) : yii\db\ActiveQuery
    {
        $where = [
            'status' => self::MESSAGE_STATUS_UNREAD,
            'user' => $userId
        ];

        if ($dialogId != 0 && is_int($dialogId)) {
            $where['dialog'] = $dialogId;
        }

        return self::find()
            ->where($where)
            ->with(['message']);
    }

    /**
     * Form an array of messages, counting messages for each dialog
     * This is used to represent total sum of unread messages i.e. instead of array with unique unread messages IDs
     */
    public static function createMessagesArrayForUser(array $messages = [], int $userId = 0) : array
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }

        $messagesArray = [];
        if ($messages) {
            foreach ($messages as $message) {
                $dialogId = (int)$message['dialog'];
                if (!isset($messagesArray[$dialogId])) {
                    $messagesArray[$dialogId] = 1;
                } else {
                    $messagesArray[$dialogId]++;
                }
            }
        }

        return $messagesArray;
    }
}
