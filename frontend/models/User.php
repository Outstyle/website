<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace frontend\models;

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
     * Deletes user and all his related info by uID
     * @param int $userId     User ID to remove from DB
     * @return bool true
     */
    public function deleteUserByUserId($userId = 0)
    {
        $user = $this->find()
          ->where(['id' => $userId])
          ->one()
          ->delete();

        $userDescription = new \frontend\models\UserDescription();
        $userDescription->find()
          ->where(['id' => $userId])
          ->one()
          ->delete();

        $userPrivacy = new \frontend\models\UserPrivacy();
        $userPrivacy->find()
          ->where(['id' => $userId])
          ->one()
          ->delete();

        return true;
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
