<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace frontend\models;

use Yii;

/**
 * For work with user statuses (i.e. online status)
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class UserStatus extends User
{

    /**
     * User social statuses (indicators)
     * @var integer
     */
    const USER_SOCIAL_OFFLINE = 0;
    const USER_SOCIAL_ONLINE = 1;

    /**
     * Time length for status indicator will stay online
     * @var int     ms
     */
    public static $timeTillOffline = 900;


    /**
     * Check user online status
     * @param  int $timestamp   Valid timestamp of user's lastvisit
     * @return int              Online status value
     */
    public static function checkUserStatusByTimestamp($timestamp)
    {
        if (time()-$timestamp < self::$timeTillOffline) {
            return self::USER_SOCIAL_ONLINE;
        }

        return self::USER_SOCIAL_OFFLINE;
    }
}
