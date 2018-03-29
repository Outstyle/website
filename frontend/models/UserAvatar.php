<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

class UserAvatar extends UserDescription
{
    /**
     * Available sizes of a user avatars
     * @var array
     */
    public static $userAvatarSizes = [
      'small',
      'medium',
      'big',
      'original'
    ];

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'image' => Yii::t('app', 'Аватар'),
        ];
    }

    /**
     * Gets an avatar relative path.
     *
     * @param int    $userId     User ID
     * @param string $avatarSize Avatar's size (small, medium, big) (?custom)
     *
     * @return string Path to the user's avatar image
     */
    public static function getAvatarPath($userId, $avatarSize = 'small')
    {
        /* Checking allowed avatar sizes */
        if (!in_array($avatarSize, self::$userAvatarSizes)) {
            $avatarSize = 'small';
        }

        /* Avatar for deleted or inactive user */
        if (!$userId) {
            return Yii::$app->params['imagesPathUrl'].'/images/54x54_avatar_deleted.png';
        }

        //return Yii::$app->params['avatarPathUrl'].$userId.'_'.$avatarSize.'.jpg';
        return Yii::$app->params['imagesPathUrl'].'/images/54x54_avatar_deleted.png';
    }
}
