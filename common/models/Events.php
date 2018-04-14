<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\Pagination;
use backend\models\Category;
use app\models\Comments;
use app\models\Likes;
use app\models\UserDescription;
use app\models\UserAvatar;
use yii\helpers\ArrayHelper;
use common\models\geolocation\Geolocation;
use common\components\helpers\PriceHelper;
use common\components\helpers\PhoneHelper;

/**
 * This is the model class for table "{{%events}}".
 *
 * @property int(11)           $id
 * @property int(11)           $user
 * @property varchar(255)      $title
 * @property int(11)           $category
 * @property int(11)           $album
 * @property int(11)           $geolocation_id
 * @property text              $description
 * @property varchar(255)      $price
 * @property varchar(255)      $phones
 * @property varchar(255)      $host
 * @property varchar(255)      $site
 * @property varchar(255)      $email
 * @property timestamp         $events_date
 * @property int(11)           $date_redact
 * @property int(11)           $redactor_id
 * @property int(11)           $status
 * @property int(11)           $created
 */
class Events extends ActiveRecord
{
    const STATUS_MODERATED = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 2;
    const STATUS_DELETED = 3;

    const EVENTS_CATEGORIES = [1, 2, 3, 4]; /* Categories to select from category table */

    /**
     * @var int    How much events do we show per page by default?
     * @var string $eventsOrderBy            Default order of events shown
     */
    public static $defaultPageSize = 10;
    public static $recommendedPageSize = 6;
    public static $eventsOrderBy = 'id desc';

    /* Geolocation stuff -> @common/models/geolocation/Geolocation */
    public $address;
    public $country;
    public $city;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%events}}';
    }

    public function rules()
    {
        return [
            [
              [
                'user',
                'title',
                'category',
                'description',
                'price',
                'price_currency',
              ],
              'required',
            ],
            ['date_redact', 'default', 'value' => 0],
            ['redactor_id', 'default', 'value' => 0],
            [['user', 'category', 'album', 'redactor_id', 'status', 'geolocation_id'], 'integer'],
            [['created', 'date_redact'], 'safe'],
            [['events_date'], 'required'],
            [['geolocation_id'], 'default', 'value' => 0],
            [['phones', 'site', 'email'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 60],
            [['description'], 'string', 'max' => 2048],
            [['price_currency'], 'string'],
            ['price_currency', 'in', 'range' => PriceHelper::getPriceCurrenciesISO()],
            ['price_visual', 'in', 'range' => PriceHelper::getPriceVisualListKeys()],
            ['email', 'email'],
            ['price', 'integer', 'min' => 1, 'max' => 10000],
            ['phones', 'match', 'pattern' => PhoneHelper::PHONE_INTERNATIONAL_REGEX],
            ['events_date', 'match', 'pattern' => '/^(\d{2})\.(\d{2})\.(\d{4}) (\d{2}):(\d{2})$/'],
            ['site', 'url', 'defaultScheme' => 'http'],
            [['img'], 'file', 'extensions' => ['png', 'jpg', 'jpeg', 'gif']],
        ];
    }

    /**
     * imageUploaderBehavior - https://github.com/demisang/yii2-image-uploader
     * Needed for 'Events' image uploading and cropping in admin area.
     */
    public function behaviors()
    {
        return [
        'imageUploaderBehavior' => [
          'class' => 'demi\image\ImageUploaderBehavior',
          'imageConfig' => [
            'imageAttribute' => 'img',
            'savePathAlias' => '@frontend/web/images/events',
            'rootPathAlias' => '@frontend/web/images',
            'noImageBaseName' => 'noimage.jpg',
            'imageSizes' => [
              '' => 1000, /* Also serves as a maxWidth limit for uploaded file and only for this set of image sizes */
              '960x360_' => 960, /* 16 / 6 AR */
              '320x120_' => 320, /* 16 / 6 AR */
              /* '360x540_' => 360, /* 2 / 3 AR */
            ],
            'imageValidatorParams' => [
              'minWidth' => 200,
              'minHeight' => 200,
              'maxWidth' => 5000,
              'maxHeight' => 5000,
            ],
            'aspectRatio' => [
              16 / 6,
              /* 2 / 3, */
            ],
            'imageRequire' => false,
            'fileTypes' => 'jpg,jpeg,gif,png',
            'maxFileSize' => 3145728,
            'backendSubdomain' => 'admin.',
          ],
        ],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user' => Yii::t('app', 'Автор'),
            'title' => Yii::t('app', 'Название'),
            'category' => Yii::t('app', 'Категория'),
            'events_date' => Yii::t('app', 'Дата проведения'),
            'description' => Yii::t('app', 'Описание'),
            'geolocation_id' => Yii::t('app', 'Geolocation Data'),
            'price_currency' => Yii::t('app', 'Price currency'),
            'price_visual' => Yii::t('app', 'Price visual'),
            'imageFiles' => Yii::t('app', 'Загрузить фото'),
            'price' => Yii::t('app', 'Цена билета'),
            'phones' => Yii::t('app', 'Телефоны'),
            'host' => Yii::t('app', 'Хост'),
            'site' => Yii::t('app', 'Сайт'),
            'email' => Yii::t('app', 'E-mail'),
            'date_redact' => Yii::t('app', 'Дата редактирования'),
            'redactor_id' => Yii::t('app', 'Редактор'),
            'status' => Yii::t('app', 'Статус'),
            'created' => Yii::t('app', 'Создано'),
            'img' => Yii::t('app', 'Главное большое фото'),
        ];
    }

    /**
     * Gets the events from DB and returns an array of data
     * [?][TODO]
     * $overpastEvents = Events::find()->where("date < :date", [':date' => time()])->orderBy('id desc')->limit(5)->all();.
     *
     * @param array $where WHERE clause to add for more precise selection. Defaults to select everything with published status
     * @param int   $page  page number. Must be >0 for pagination to appear
     *
     * @return array|null events data
     */
    public static function getEvents($where = [], $page = null)
    {

        /* Default arguments for 'where' and $andWhere clauses */
        $where['status'] = self::STATUS_PUBLISHED;
        /**
        if want print not all events: the array ($andWhere ) must be not empty, but with sort parameters by date, as below */
        $andWhere = ['>', 'events_date', date('Y-m-d H:i:s', time())]; /* Only active events */
        $andWhere = []; /* Only all events */
        $eventsOrderBy = 'events_date desc';/* Only all events. Default order by id desc */
        /**
         * Getting the events: let's start by partially adding parameters in case we have pagination
         * Notice that we are adding query parameters one by one (chaining), checking additional conditions
         * Read more about QB syntax here: http://www.yiiframework.com/doc-2.0/guide-db-query-builder.html
         * Also we are using 'with()' for eager loading from user description table.
         */
        /**
         * if want to print not all events, uncomment next line. Parameter 'where' must not empty. Parameter 'orderBy' default: id desc;
         */
        /*$eventsQuery = self::find()->with(['userDescription'])->where($where)->andWhere($andWhere)->orderBy(self::$eventsOrderBy);*/ /* Only active events */
        $eventsQuery = self::find()->with(['userDescription'])->where($where)->andWhere($andWhere)->orderBy($eventsOrderBy);/* Only all events sort by events date*/

        /* If we have pagination */
        if ($page) {
            $pagination = new Pagination([
              'defaultPageSize' => self::$defaultPageSize,
              'totalCount' => $eventsQuery->count(),
              'page' => $page - 1,
            ]);

            $eventsQuery = $eventsQuery->offset($pagination->offset)->limit($pagination->limit);
        } else {
            $eventsQuery = $eventsQuery->limit(self::$defaultPageSize);
        }

        /* And finally let's make our request to DB */
        $events = $eventsQuery->all();

        /* If we don't have any events queried, we won't populate events model */
        if (!$events) {
            return;
        }

        /* Now to populate our Events model with data */
        for ($i = 0; $i < count($events); ++$i) {
            /* Other models data */
            $likes = Likes::findLikesCount('events', $events[$i]->id);
            $commentsCount = Comments::countComments('events', $events[$i]->id);

            $modelEvents[$i]['id'] = $events[$i]->id;
            $modelEvents[$i]['userId'] = $events[$i]->user;
            $modelEvents[$i]['albumId'] = $events[$i]->album;
            $modelEvents[$i]['title'] = $events[$i]->title;
            $modelEvents[$i]['events_date'] = $events[$i]->events_date;
            $modelEvents[$i]['img'] = $events[$i]->getImageSrc('320x120_');
            $modelEvents[$i]['price'] = $events[$i]->price;
            $modelEvents[$i]['price_currency'] = $events[$i]->price_currency;
            $modelEvents[$i]['price_visual'] = $events[$i]->price_visual;
            $modelEvents[$i]['myLike'] = Likes::findMyLike('events', $events[$i]->id);
            $modelEvents[$i]['likesCount'] = ($likes) ? (int) $likes : 0;
            $modelEvents[$i]['countComments'] = (!empty($commentsCount)) ? (int) $commentsCount : 0;
            $modelEvents[$i]['category'] = $events[$i]->categories->name;
            $modelEvents[$i]['categoryUrl'] = $events[$i]->categories->url;
            $modelEvents[$i]['geolocation'] = $events[$i]->geolocation;

            /*
             * Additional stuff for single events page
             * If we have URL parameter, let's also populate additional array with similar events from same category
             * Description key is needed for single events page (SEO purposes). You can add additional stuff for singles here
             */
            if (isset($where['id'])) {
                $modelEvents[$i]['description'] = $events[$i]->description;
                $modelEvents[$i]['userName'] = $events[$i]->userDescription->nickname;
                $modelEvents[$i]['userAvatar'] = UserAvatar::getAvatarPath($events[$i]->user);
                $modelEvents[$i]['userCulture'] = ArrayHelper::getValue(UserDescription::cultureList(true), $events[$i]->userDescription->culture);
                $modelEvents[$i]['comments'] = Comments::getComments(['elem_type' => 'events', 'elem_id' => $events[$i]->id]);
                $modelEvents[$i]['categories'] = Category::getCategories(['id' => self::EVENTS_CATEGORIES]);

                $modelEvents[$i]['img_big'] = $events[$i]->getImageSrc('960x360_');

                /* Getting recommended events */
                /* TODO: How to count recommendations? Probably by likes count? https://trello.com/c/tgqgMiFJ */
                /* TODO: make it as a separate method */

                $where['category'] = $events[$i]->category;
                $andWhere = ['!=', 'id', $events[$i]->id];
                unset($where['id']);

                $recommendedEvents = self::find()
                ->where($where)
                ->andWhere($andWhere)
                ->orderBy(self::$eventsOrderBy)
                ->limit(self::$recommendedPageSize)
                ->all();
                if (!empty($recommendedEvents)) {
                    $count = (count($recommendedEvents) >= self::$recommendedPageSize) ? self::$recommendedPageSize : count($recommendedEvents);
                    for ($s = 0; $s < $count; ++$s) {
                        $modelEvents[$i]['recommended'][$s]['id'] = $recommendedEvents[$s]->id;
                        $modelEvents[$i]['recommended'][$s]['title'] = $recommendedEvents[$s]->title;
                        $modelEvents[$i]['recommended'][$s]['img'] = null; /* TODO: similar to news getSrc() */
                    }
                }
            }
        }

        return $modelEvents;
    }

    /**
     * Relations: http://www.yiiframework.com/doc-2.0/guide-db-active-record.html#relational-data.
     */
    public function getUserDescription()
    {
        return $this->hasOne(UserDescription::className(), ['id' => 'user']);
    }

    public function getCategories()
    {
        return $this->hasOne(Category::className(), ['id' => 'category']);
    }

    public function getGeolocation()
    {
        return $this->hasOne(Geolocation::className(), ['id' => 'geolocation_id']);
    }

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
