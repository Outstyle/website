<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\Pagination;
use common\models\geolocation\Geolocation;
use backend\models\Category;
use app\models\Comments;
use app\models\Likes;
use common\models\Photo;
use common\components\helpers\StringHelper;
use common\components\helpers\PriceHelper;
use common\components\helpers\PhoneHelper;
use common\components\helpers\BlocksHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%school}}".
 *
 * @property int $id [(11)]
 * @property int $user [(11)]
 * @property string $title [varchar(255)]
 * @property int $category [(11)]
 * @property int $geolocation_id [(11)]
 * @property string $price [varchar(255)]
 * @property string $price_currency [varchar(3)]
 * @property string $phone [varchar(255)]
 * @property string $site [varchar(255)]
 * @property string $description [varchar(2048)]
 * @property JSONarray $additional [text(65536)]
 * @property timestamp $created [CURRENT_TIMESTAMP]
 * @property int $album [(11)]
 * @property int $date_redact [(11)]
 * @property int $redactor_id [(11)]
 * @property int $status [(11)]
 * @property string $img [varchar(255)]
 * @property int $img_block_size [(1)]
 */
class School extends ActiveRecord
{
    const STATUS_MODERATED = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 2;
    const STATUS_DELETED = 3;

    const SCHOOL_CATEGORIES = [1, 2, 3, 4, 9]; /* Categories to select from category table */

    /* Geolocation stuff -> @common/models/geolocation/Geolocation */
    public $address;
    public $country;
    public $city;

    /* $additional stuff -> $this */
    public $trainingTime;
    public $square;
    public $floor;
    public $traininer;
    public $equipment;
    public $trains;
    public $mirrors = 0;
    public $materials = 0;
    public $soundSoft = 0;

    /**
     * @var int    How much schools do we show per page by default?
     * @var int
     * @var string $schoolsOrderBy         Default order of schools shown
     */
    public static $defaultPageSize = 10;
    public static $schoolsOrderBy = 'id desc';

    /**
     * imageUploaderBehavior - https://github.com/demisang/yii2-image-uploader
     * Needed for 'School' image uploading and cropping in admin area.
     */
    public function behaviors()
    {
        return [
        'imageUploaderBehavior' => [
          'class' => 'demi\image\ImageUploaderBehavior',
          'imageConfig' => [
            'imageAttribute' => 'img',
            'savePathAlias' => Yii::$app->params['imagesPathDir'].Yii::$app->controller->id,
            'rootPathAlias' => Yii::$app->params['imagesPathDir'],
            'noImageBaseName' => 'noimage.jpg',
            'imageSizes' => [
              '' => 1000, /* Also serves as a maxWidth limit for uploaded file and only for this set of image sizes */
              '500x500_' => 500, /* 1/1 aspect ratio */
              '500x250_' => 500, /* 2/1 aspect ratio */
              '250x250_' => 250, /* 1/1 aspect ratio */
              '250x125_' => 250, /* 2/1 aspect ratio */
            ],
            'imageValidatorParams' => [
              'minWidth' => 500,
              'minHeight' => 500,
              'maxWidth' => 2000,
              'maxHeight' => 2000,
            ],
            'aspectRatio' => [
              1 / 1,
              2 / 1,
            ],
            'imageRequire' => false,
            'fileTypes' => 'jpg,jpeg,gif,png',
            'maxFileSize' => 3145728,
            'backendSubdomain' => 'admin.',
          ],
        ],
      ];
    }

    public static function tableName()
    {
        return '{{%school}}';
    }

    /* [IMPORTANT] TODO: CHECK ON RULES! */
    public function rules()
    {
        return [
            [['title', 'category'], 'required'],
            [['title'], 'unique'],
            [['user', 'category', 'geolocation_id'], 'integer'],
            [['user', 'created', 'additional'], 'safe'],
            [['album', 'date_redact', 'geolocation_id'], 'default', 'value' => 0],
            [['site', 'trainingTime', 'square', 'floor', 'traininer', 'equipment', 'trains'], 'string', 'max' => 255],
            [['price_currency'], 'string'],

            ['img_block_size', 'integer', 'min' => 0, 'max' => 9],
            ['title', 'string', 'max' => 64],
            ['price', 'integer', 'min' => 0, 'max' => 100000],
            ['phone', 'match', 'pattern' => PhoneHelper::PHONE_INTERNATIONAL_REGEX],
            ['description', 'string', 'max' => 4096],
            ['site', 'url', 'defaultScheme' => 'http'],
            ['price_currency', 'in', 'range' => PriceHelper::getPriceCurrenciesISO()],
        ];
    }

    /* TODO: EN entities */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user' => Yii::t('app', 'User'),
            'title' => Yii::t('app', 'School name'),
            'category' => Yii::t('app', 'Category'),
            'geolocation_id' => Yii::t('app', 'Geolocation ID'),
            'price' => Yii::t('app', 'Price'),
            'phone' => Yii::t('app', 'Phone'),
            'site' => Yii::t('app', 'Website'),
            'status' => Yii::t('app', 'Status'),
            'price_currency' => Yii::t('app', 'Currency'),
            'description' => Yii::t('app', 'Additional information'),
            'created' => Yii::t('app', 'Created'),
            'trainingTime' => Yii::t('app', 'Training time'),
            'square' => Yii::t('app', 'Dance floor area'),
            'floor' => Yii::t('app', 'Floor surface'),
            'mirrors' => Yii::t('app', 'Mirrors'),
            'traininer' => Yii::t('app', 'Trainer'),
            'equipment' => Yii::t('app', 'Equipment'),
            'trains' => Yii::t('app', 'Teaches'),
            'materials' => Yii::t('app', 'Materials'),
            'soundSoft' => Yii::t('app', 'Soundworks'),
            'imageFiles' => Yii::t('app', 'Upload image'),
            'img' => Yii::t('app', 'School main image'),
            'img_block_size' => Yii::t('app', 'Image block size'),
        ];
    }

    /**
     * Gets schools from DB and returns an array of data.
     *
     * @param array $where WHERE clause to add for more precise selection. Defaults to select everything with published status
     * @param int   $page  page number. Must be >0 for pagination to appear
     *
     * @return array|null schools data
     */
    public static function getSchools($where = [], $page = null)
    {

        /* Default arguments for 'where' clause */
        $where['status'] = self::STATUS_PUBLISHED;

        /**
         * Getting the schools: let's start by partially adding parameters in case we have pagination
         * Notice that we are adding query parameters one by one (chaining), checking additional conditions
         * Read more about QB syntax here: http://www.yiiframework.com/doc-2.0/guide-db-query-builder.html.
         *
         * Also notice, that we don't want to show schools that are not having geolocation ID ('andWhere' chain link)
         */
        $schoolsQuery = self::find()->where($where)->andWhere(['!=', 'geolocation_id', 0])->orderBy(self::$schoolsOrderBy);
        $schoolsQuery = $schoolsQuery->limit(self::$defaultPageSize);

        /* If we have pagination */
        if ($page) {
            $pagination = new Pagination([
              'defaultPageSize' => self::$defaultPageSize,
              'totalCount' => $schoolsQuery->count(),
              'page' => $page - 1,
            ]);

            $schoolsQuery = $schoolsQuery->offset($pagination->offset)->limit($pagination->limit);
        }

        /* And finally let's make our request to DB */
        $schools = $schoolsQuery->all();

        /* If we don't have any schools queried, we won't populate schools model */
        if (!$schools) {
            return;
        }

        /* Now to populate our School model with data */
        for ($i = 0; $i < count($schools); ++$i) {
            /* Other models data */
            $likes = Likes::findLikesCount('school', $schools[$i]->id);
            $commentsCount = Comments::countComments('school', $schools[$i]->id);
            $imagesize = BlocksHelper::getBlockSizeStatus($schools[$i]->img_block_size);

            $model[$i]['id'] = $schools[$i]->id;
            $model[$i]['userId'] = $schools[$i]->user;
            $model[$i]['title'] = $schools[$i]->title;
            $model[$i]['description'] = (!isset($where['id'])) ? StringHelper::cutString($schools[$i]->description, 200) : $schools[$i]->description;
            $model[$i]['img'] = $schools[$i]->getImageSrc($imagesize.'_');
            $model[$i]['img_block_size'] = $imagesize;
            $model[$i]['myLike'] = Likes::findMyLike('school', $schools[$i]->id);
            $model[$i]['likesCount'] = ($likes) ? (int) $likes : 0;
            $model[$i]['countComments'] = (!empty($commentsCount)) ? (int) $commentsCount : 0;
            $model[$i]['category'] = $schools[$i]->categories->name;
            $model[$i]['categoryUrl'] = $schools[$i]->categories->url;

            //$model[$i]['albumId']               = $schools[$i]->album; /* ? */
            //$model[$i]['nameImg'] = self::getOnePhotoSchool($model[$i]->album); /* ? */
            //$model[$i]['rights'] = ($model->user == Yii::$app->user->id) ? 1 : 0; /* ? */

            /*
             * Additional stuff for single school page
             * $schoolParams - stored as JSON array in DB
             * $photoalbum - related to Photo model, getting a photos from particular school photoalbum
             */
            if (isset($where['id'])) {
                $schoolParams = json_decode($schools[$i]->additional, true);

                $model[$i]['comments'] = Comments::getComments(['elem_type' => 'school', 'elem_id' => $schools[$i]->id]);
                $model[$i]['price'] = $schools[$i]->price;
                $model[$i]['price_currency'] = $schools[$i]->price_currency;
                $model[$i]['phone'] = $schools[$i]->phone;
                $model[$i]['site'] = $schools[$i]->site;
                $model[$i]['geolocation'] = $schools[$i]->geolocation;
                $model[$i]['trainingTime'] = $schoolParams['trainingTime'];
                $model[$i]['square'] = $schoolParams['square'];
                $model[$i]['floor'] = $schoolParams['floor'];
                $model[$i]['mirrors'] = ($schoolParams['mirrors']) ? Yii::t('app', 'Yes') : '';
                $model[$i]['traininer'] = $schoolParams['traininer'];
                $model[$i]['equipment'] = $schoolParams['equipment'];
                $model[$i]['trains'] = $schoolParams['trains'];
                $model[$i]['materials'] = ($schoolParams['materials']) ? Yii::t('app', 'Provided by school') : Yii::t('app', 'Own materials');
                $model[$i]['soundSoft'] = ($schoolParams['soundSoft']) ? Yii::t('app', 'Yes') : Yii::t('app', 'No');

                /* For single school instance we are using fixed image size - 500x500 */
                $imagesize = '500x500';
                $model[$i]['img'] = $schools[$i]->getImageSrc($imagesize.'_');
                $model[$i]['img_block_size'] = $imagesize;

                /* Gallery */
                $model[$i]['gallery'] = Photo::getByAlbumId($schools[$i]->album);
            }

            /* Removing empty values, but keeping 0 - http://stackoverflow.com/a/3654309 */
            $model[$i] = array_filter($model[$i], function ($value) {
                return $value !== '';
            });
        }

        return $model;
    }

    /**
     * Relations: http://www.yiiframework.com/doc-2.0/guide-db-active-record.html#relational-data.
     */
    public function getCategories()
    {
        return $this->hasOne(Category::className(), ['id' => 'category']);
    }
    public function getPhotoalbum()
    {
        return $this->hasOne(Photoalbum::className(), ['id' => 'album']);
    }

    public function getGeolocation()
    {
        return $this->hasOne(Geolocation::className(), ['id' => 'geolocation_id']);
    }

    /* TODO: WTF is this? */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user'])->one()->username;
    }

    public function getRedactor()
    {
        if (!empty($this->hasOne(User::className(), ['id' => 'redactor_id'])->one()->username)) {
            return $this->hasOne(User::className(), ['id' => 'redactor_id'])->one()->username;
        } else {
            return  '';
        }
    }
}
