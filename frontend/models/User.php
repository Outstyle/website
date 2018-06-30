<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 * This model serves as a frontend one and should always extend common User model.
 *
 * Only custom methods are stored here.
 * @see: @common\models\user\User for basic methods
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class User extends \common\models\User
{
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

    /* Relations - Frontend */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['user' => 'id']);
    }

    public function getBoard()
    {
        return $this->hasMany(Board::className(), ['owner' => 'id']);
    }

    public function getVideo()
    {
        return $this->hasMany(Video::className(), ['user' => 'id']);
    }

    public function getPhoto()
    {
        return $this->hasMany(Photo::className(), ['user' => 'id']);
    }

    public function getFriend()
    {
        return $this->hasMany(Friend::className(), ['user1' => 'id']);
    }

    public function getUserPrivacy()
    {
        return $this->hasOne(UserPrivacy::className(), ['id' => 'id']);
    }

    public function getUserDescription()
    {
        return $this->hasOne(UserDescription::className(), ['id' => 'id']);
    }
}
