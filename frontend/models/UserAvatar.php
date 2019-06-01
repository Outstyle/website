<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace frontend\models;

use Yii;

/**
 * For work with user avatars
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class UserAvatar extends \common\models\Photo
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
        /**
         * imageUploaderBehavior - https://github.com/demisang/yii2-image-uploader
         */
        'imageUploaderBehavior' => [
          'class' => 'demi\image\ImageUploaderBehavior',
          'imageConfig' => [
            'imageAttribute' => 'img',
            'savePathAlias' => Yii::$app->params['imagesSubdomainDir'].Yii::$app->params['avatarPathUrl'],
            'rootPathAlias' => Yii::$app->params['imagesSubdomainDir'],
            'noImageBaseName' => 'noimage.jpg',
            'imageSizes' => [
              '' => 2000, /* Also serves as a maxWidth limit for uploaded file and only for this set of image sizes */
              '150x150_' => 150, /* 1/1 aspect ratio */
            ],
            'imageValidatorParams' => [
              'minWidth' => 150,
              'minHeight' => 150,
              'maxWidth' => 2000,
              'maxHeight' => 2000,
            ],
            'aspectRatio' => [
              1 / 1,
            ],
            'imageRequire' => false,
            'uploadMultiple' => 1,
            'deleteRow' => false,
            'fileTypes' => self::ALLOWED_FILE_TYPES,
            'maxFileSize' => 3145728,
            'backendSubdomain' => 'admin.',
          ],
        ],
      ];
    }



    /**
     * Gets an avatar absolute path, also relating on services
     * TODO: Move $size var into avatar_deleted size by default to able to dynamically change it via calling
     * @param  string  $relativePath    Path to avatar from userDescription->avatar column
     * @param  string  $size            i.e. 150x150_
     * @param  integer $serviceId
     * @return string
     */
    public static function getAvatarPath($relativePath = '', $size = '150x150_', $serviceId = 0) : string
    {
        /* Avatar for deleted or inactive user */
        if (!$relativePath) {
            return Yii::$app->params['imagesPathUrl'].'images/54x54_avatar_deleted.png';
        }

        return self::getByPrefixAndServiceId($relativePath, $size, $serviceId, $photoType = self::PHOTO_TYPE_AVATAR);
    }

    /**
     * Gets an avatar absolute path by avatar ID
     * @param  int  $avatarId
     * @return string
     */
    public static function getById(int $avatarId = 0) : string
    {
        if ($avatarId === 0) {
            return Yii::$app->params['imagesPathUrl'].'images/54x54_avatar_deleted.png';
        }

        $photo = self::find()
            ->where(['id' => $avatarId])
            ->asArray()
            ->one();

        return self::getAvatarPath($photo['img'], '150x150_', $photo['service_id']);
    }

    /**
     * Gets an avatar absolute path by user ID
     * @param  int  $userId
     * @return string
     */
    public static function getByUserId(int $userId = 0) : string
    {
        $avatar = UserDescription::find()
            ->select('avatar')
            ->where(['id' => $userId])
            ->asArray()
            ->one();

        $avatarId = (isset($avatar['avatar']) ? $avatar['avatar'] : 0);

        return self::getById($avatarId);
    }
}
