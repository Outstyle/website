<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

class UserNickname extends UserDescription
{

    /**
     * Get user nickname by his ID
     * If user is in STATUS_DELETED, returns User::STATUS_DELETED
     *
     * @param  int     $userId     User ID
     * @return string              User nickname or empty if user is deleted
     */
    public static function getNickname($userId)
    {
        $user = self::find()->with([
                  'user' => function ($query) {
                      $query->andWhere(['!=', 'status', User::STATUS_DELETED])->select('id');
                  }
                ])->where(['id' => $userId])->one();

        return self::checkNicknameForDeleted($user->nickname ?? '');
    }

    /**
     * Checks if username is not deleted
     *
     * @param  string $nickname   User nickname
     * @return string             If user is deleted, returns 'User deleted' instead real username
     */
    public static function checkNicknameForDeleted($nickname = null)
    {
        if (isset($nickname) && !empty($nickname)) {
            return $nickname;
        }

        return Yii::t('app', 'User deleted');
    }
}
