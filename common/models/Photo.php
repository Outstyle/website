<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace common\models;

use Yii;

use common\components\helpers\StringHelper;

/**
 * This is the model class for table "{{%photo}}".
 * This model serves as a common one, both for backend and frontend.
 *
 * Only general yii2 built-in methods should be used here!
 * If you want to add some modifications or new methods, you should extend from this model.
 * @see: frontend/models/Photo.php
 * @see: backend/models/Photo.php
 *
 * Also, all the relations with other models should be declared in this common model.
 *
 * @property string $id
 * @property string $user
 * @property int $album
 * @property string $name
 * @property string $img
 * @property int $service_id
 * @property string $created
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */

class Photo extends \yii\db\ActiveRecord
{
    const ALLOWED_FILE_TYPES = 'jpg,jpeg,gif,png';

    /**
     * Photo types
     * @var integer
     */
    const PHOTO_TYPE_DEFAULT = 0;
    const PHOTO_TYPE_AVATAR = 1;

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
            'savePathAlias' => Yii::$app->params['imagesSubdomainDir'].self::_getPhotosPath(),
            'rootPathAlias' => Yii::$app->params['imagesSubdomainDir'],
            'noImageBaseName' => 'noimage.jpg',
            'imageSizes' => [
              '' => 2000, /* Also serves as a maxWidth limit for uploaded file and only for this set of image sizes */
              '210x126_' => 210, /* 5/3 aspect ratio */
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
              5 / 3,
            ],
            'imageRequire' => false,
            'uploadMultiple' => 1,
            'deleteRow' => true,
            'fileTypes' => self::ALLOWED_FILE_TYPES,
            'maxFileSize' => 3145728,
            'backendSubdomain' => 'admin.',
          ],
        ],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%photo}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
              'user',
              'default', 'value' => Yii::$app->user->id ?? 0
            ],
            [
              'user',
              'required'
            ],
            [
              'user',
              'integer'
            ],
            [
              'name',
              'filter', 'filter' => function ($name) {
                  return StringHelper::clearString($name);
              }
            ],
            [
              'name',
              'string', 'max' => 255
            ],
            [
              'album',
              'default', 'value' => 0
            ],
            [
              'album',
              'integer'
            ],
            [
              'service_id',
              'default', 'value' => 1
            ],
            [
              'type',
              'default', 'value' => self::PHOTO_TYPE_DEFAULT
            ],
            [
              'type',
              'integer'
            ],
            [
              'type',
              'in', 'range' => [
                  self::PHOTO_TYPE_DEFAULT,
                  self::PHOTO_TYPE_AVATAR
                ]
              ],
            [
              'created',
              'default', 'value' => date("Y-m-d H:i:s")
            ],
            [
              'img',
              'image', 'skipOnEmpty' => false,
              'extensions' => self::ALLOWED_FILE_TYPES,
              'maxFiles' => 1
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'User'),
            'album' => Yii::t('app', 'Album'),
            'name' => Yii::t('app', 'Name'),
            'img' => Yii::t('app', 'Image'),
            'service_id' => Yii::t('app', 'Service ID'),
            'created' => Yii::t('app', 'Created'),
        ];
    }

    /**
     * Gets all photos by album ID
     *
     * @param string $album_id  ID of an album containing photos
     *
     * @return array Array containing all the links
     */
    public static function getByAlbumId(int $album_id)
    {
        $photos = self::find()->where(array('album' => $album_id))->select('img')->asArray()->all();
        foreach ($photos as $i => $photo) {
            $photos[$i]['img_thumbnail'] = self::_addPrefixToPhoto($photo['img'], '150x150_');
        }

        return $photos;
    }

    /**
     * Gets photos by type
     * TODO: implement if needed
     */
    public static function getByType()
    {
        throw new NotSupportedException('"getByType" is not implemented.');
    }

    /**
     * Gets an image path with size prefix
     * TODO: Make this method have less params, split
     * @param  string $path           Original relative path to image
     * @param  string $photo_prefix   Prefix to use (i.e. '150x150_')
     * @param  int    $service_id     Service ID to get photo from
     * @param  int    $type
     * @param  int    $user_id
     * @return string                 Absolute URL path to image
     */
    public static function getByPrefixAndServiceId(
      string $path,
      string $photo_prefix,
      int $service_id = 0,
      int $type = 0,
      int $user_id = 0
    ) {
        if (!$path) {
            return;
        }

        $path = self::_addPrefixToPhoto($path, $photo_prefix);
        if ($type === self::PHOTO_TYPE_DEFAULT) {
            $path = Yii::$app->params['photoServices'][$service_id].self::_getPhotosPath($user_id).$path;
        }
        if ($type === self::PHOTO_TYPE_AVATAR) {
            $path = Yii::$app->params['photoServices'][$service_id].Yii::$app->params['avatarPathUrl'].$path;
        }

        return $path;
    }

    /**
     * Adds a prefix to photo for getting thumbnails
     * @param string $path   Path to image (original)
     * @param string $prefix Needed prefix (size)
     *
     * @return string Modified path to file
     */
    private static function _addPrefixToPhoto($path, $prefix = null)
    {
        if ($prefix === null || $prefix == '') {
            return $path;
        }

        $path = str_replace('\\', '/', $path);
        $dir = explode('/', $path);
        $lastIndex = count($dir) - 1;
        $dir[$lastIndex] = $prefix.$dir[$lastIndex];

        return implode('/', $dir);
    }

    /**
     * Gets a path to particular user's photos
     * @param  int    $user_id
     * @return string
     */
    private static function _getPhotosPath(int $user_id = 0)
    {
        if (!$user_id) {
            $user_id = Yii::$app->user->id ?? 0;
        }
        return Yii::$app->params['photosPathUrl'].$user_id.'/';
    }

    /* Relations */
    public function getPhotoalbum()
    {
        return $this->hasOne(Photoalbum::className(), ['id' => 'album']);
    }
    public function getComments()
    {
        return $this->hasMany(Comments::className(), ['elem_id' => 'id'])->andWhere(['elem_type' => 'photo']);
    }
}
