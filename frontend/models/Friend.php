<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;

use frontend\models\UserDescription;

/**
 * This is the model class for table "{{%friend}}".
 * This model serves as a frontend one and should always extend common Friend model.
 *
 * Only custom methods are stored here.
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class Friend extends \common\models\Friend
{
    /**
     * Get user friends by status and userId
     *
     * @param  integer|array    $status    Friendship status (@see: \common\models\Friend for status constants)
     * @param  integer          $userId    User ID
     * @return array
     */
    public static function getUserFriends($status = self::FRIENDSHIP_STATUS_ACTIVE, $userId = 0)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }

        if (is_array($status)) {
            $status = implode(',', $status);
        }

        return self::find()
          ->where('({{%friend}}.`user1` = :user AND {{%friend}}.`status` IN ('.$status.')) OR
                   ({{%friend}}.`user2` = :user AND {{%friend}}.`status` IN ('.$status.'))',
          [':user' => (int)$userId]);
    }

    /**
     * Get user friends by online indicator (lastvisit)
     *
     * @param  integer $userId    User ID
     * @return array
     */
    public static function getUserFriendsOnline($userId = 0)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }

        return self::getUserFriends([self::FRIENDSHIP_STATUS_ACTIVE, self::FRIENDSHIP_STATUS_ONESIDED], $userId)
          ->join('INNER JOIN', '{{%user}} u',
            '((u.`id` = {{%friend}}.`user1` AND {{%friend}}.`user2` = :user) OR
              (u.`id` = {{%friend}}.`user2` AND {{%friend}}.`user1` = :user))
              AND u.`lastvisit` > :lastvisit',
            [
              ':lastvisit' => (time() - self::$timeTillOffline)
            ]);
    }

    /**
     * Get friends description by an array of IDs
     * @param  array  $friendsIdArray
     * @return array
     */
    public static function getFriendsDescription($friendsIdArray = [])
    {
        return UserDescription::findUsers()->where(['id' => $friendsIdArray])->asArray()->all();
    }

    /**
     * Filtering all the friends, removing active userID from users array
     * @param  array  $userFriends    Array from getUserFriends
     * @return array
     */
    public static function createFriendsArrayForUser($userFriends = [], $userId = 0)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }

        foreach ($userFriends as $friend) {
            /* We won't show onesided friendship for initiator of friendship */
            if ($friend['user1'] != $userId && $friend['status'] != self::FRIENDSHIP_STATUS_ONESIDED) {
                $friends[] = (int)$friend['user1'];
            }
            /* We also won't show pending friendships for non-owner of friendship */
            if ($friend['user2'] != $userId && $friend['status'] != self::FRIENDSHIP_STATUS_PENDING) {
                $friends[] = (int)$friend['user2'];
            }
        }

        return $friends ?? [];
    }
}
