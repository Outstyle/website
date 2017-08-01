<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "z_user".
 *
 * @see: @common\models\user\User for basic methods
 */
class User extends \common\models\User
{

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
