<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "z_user_description".
 *
 * @see: @common\models\user\UserDescription for basic methods
 */
class UserDescription extends \common\models\user\UserDescription
{
    /**
     * Gets user culture by user ID
     * @param  integer $userId
     * @param  bool    $forCSS
     * @return string  User culture name (CSS string)
     */
    public static function getUserCultureByUserId($userId = 0, $forCSS = true)
    {
        $culture = 0;
        $user = self::findOne($userId);

        if (isset($user->culture)) {
            $culture = $user->culture;
        }

        return ArrayHelper::getValue(self::cultureList($forCSS), $culture);
    }
}
