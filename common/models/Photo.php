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
            'savePathAlias' => Yii::$app->params['imagesSubdomainDir'].'photo/'.Yii::$app->user->id,
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
              ['user'],
              'default', 'value' => Yii::$app->user->id
            ],
            [
              ['user'],
              'required'
            ],
            [
              ['name'],
              'filter', 'filter' => function ($name) {
                  return StringHelper::clearString($name);
              }
            ],
            [
              ['name'],
              'string', 'max' => 255
            ],
            [
              ['user', 'album'],
              'integer'
            ],
            [
              ['service_id'],
              'default', 'value' => 1
            ],
            [
              ['created'],
              'default', 'value' => date("Y-m-d H:i:s")
            ],
            [
              ['img'],
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
            $photos[$i]['img_thumbnail'] = self::addPrefixToPhoto($photo['img'], '150x150_');
        }

        return $photos;
    }

    public static function getByPrefix(string $img, string $photo_prefix)
    {
        return self::addPrefixToPhoto($img, $photo_prefix);
    }

    /**
     * Adds a prefix to photo for getting thumbnails
     * @param string $path   Path to image (original)
     * @param string $prefix Needed prefix (size)
     *
     * @return string Modified path to file
     */
    public static function addPrefixToPhoto($path, $prefix = null)
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
