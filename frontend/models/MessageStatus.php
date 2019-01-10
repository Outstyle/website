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
     * Sets unread message status for every user
     * @param  int          $dialogId
     * @return bool
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
     * ActiveQuery for unread messages
     * @param  integer            $dialogId
     * @param  integer            $userId
     * @return obj
     */
    public static function getUnread(int $dialogId = 0, int $userId = 0) : yii\db\ActiveQuery
    {
        return self::find()
            ->where(['status' => self::MESSAGE_STATUS_UNREAD])
            ->with(['message'])
            ->where(['dialog' => $dialogId, 'user' => $userId]);
    }
}
