<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace common\models\user;

use Yii;
use yii\helpers\ArrayHelper;
use common\components\helpers\StringHelper;

use common\models\User;
use common\models\geolocation\GeolocationCities;
use common\models\geolocation\GeolocationCountries;
use common\models\Friend;

/**
 * This is the model class for table "{{%user_description}}".
 * This model serves as a common one, both for backend and frontend.
 *
 * Only general yii2 built-in methods should be used here!
 * If you want to add some modifications or new methods, you should extend from this model.
 * @see: frontend/models/UserDescription.php
 * @see: backend/models/UserDescription.php
 *
 * Also, all the relations with other models should be declared in this common model.
 * TODO: Work with table values and types, needed columns?
 *
 * @property integer  $id
 * @property string   $name
 * @property string   $last_name
 * @property string   $nickname
 * @property string   $status
 * @property integer  $family
 * @property string   $birthday
 * @property integer  $birthday_show
 * @property integer  $country
 * @property integer  $city
 * @property integer  $culture
 * @property string   $team
 * @property string   $phone
 * @property string   $site
 * @property string   $skype
 * @property string   $music
 * @property string   $film
 * @property string   $shows
 * @property string   $books
 * @property string   $game
 * @property string   $citation
 * @property string   $about
 * @property string   $politics
 * @property string   $world_view
 * @property string   $worth_life
 * @property string   $worth_people
 * @property string   $inspiration
 * @property integer  $language
 * @property string   $sex
 * @property string   $rating
 * @property integer  $avatar
 * @property integer  $avatar_small
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class UserDescription extends \yii\db\ActiveRecord
{
    /**
     * Behavior scenarios for user description
     * @var string
     */
    const SCENARIO_DEFAULT = 'default';
    const SCENARIO_SEARCH = 'search';
    const SCENARIO_FILTER = 'filter';


    /**
     * @inheritdoc
     */
    public $user;
    public $search;
    public $age_min;
    public $age_max;
    public $sort_by;
    public $friendship_status;
    public $page;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_description}}';
    }


    /**
     * @inheritdoc
     * TODO: Check validation on strings (chars limit, ~)
     */
    public function rules()
    {
        return [

          ['id', 'default', 'value' => Yii::$app->user->id ?? 0],
          ['id', 'integer'],
          ['id', 'required', 'on' => self::SCENARIO_DEFAULT, 'message' => Yii::t('app', 'ID is required')],
          ['name', 'required', 'on' => self::SCENARIO_DEFAULT],
          ['name', 'string', 'max' => 255],
          ['last_name', 'required', 'on' => self::SCENARIO_DEFAULT],
          ['last_name', 'string', 'max' => 255],
          ['nickname', 'required', 'on' => self::SCENARIO_DEFAULT],
          ['nickname', 'string', 'max' => 255],
          ['status', 'string', 'max' => 120],
          ['family', 'integer'],
          ['family', 'in', 'range' => range(0, (count(self::familyList())-1))],
          ['birthday', 'date', 'format' => 'yyyy-M-d'],
          ['birthday_show', 'integer'],
          ['birthday_show', 'in', 'range' => [0, 1]],
          ['country', 'integer'],
          ['city', 'integer'],
          ['culture', 'integer'],
          ['culture', 'in', 'range' => range(0, (count(self::cultureList())-1))],
          ['team', 'string', 'max' => 255],
          ['phone', 'string', 'max' => 255],
          ['site', 'string', 'max' => 255],
          ['site', 'url'],
          ['skype', 'string', 'max' => 4000],
          ['music', 'string', 'max' => 4000],
          ['film', 'string', 'max' => 4000],
          ['shows', 'string', 'max' => 4000],
          ['books', 'string', 'max' => 4000],
          ['game', 'string', 'max' => 4000],
          ['citation', 'string', 'max' => 4000],
          ['about', 'string', 'max' => 4000],
          ['politics', 'string', 'max' => 255],
          ['world_view', 'string', 'max' => 4000],
          ['worth_life', 'string', 'max' => 255],
          ['worth_people', 'string', 'max' => 255],
          ['inspiration', 'string', 'max' => 255],
          ['language', 'integer', 'max' => 255],
          ['sex', 'filter', 'filter' => function ($value) {
              if (is_array($value)) {
                  if (ArrayHelper::isSubset(['male' , 'female'], $value)) {
                      return;
                  }
                  return array_pop($value);
              }
          }],
          ['sex', 'in', 'range' => ['male', 'female']],
          ['rating', 'integer'],
          ['avatar', 'string', 'max' => 50],
          ['avatar_small', 'integer'], /* TODO: Remove this */

          /* Additional validation rules */
          ['user', 'default', 'value' => Yii::$app->user->id ?? 0],
          ['user', 'integer'],
          ['user', 'required',
            'on' => [
              self::SCENARIO_SEARCH,
              self::SCENARIO_FILTER
            ],
           'message' => Yii::t('app', 'User ID is required')],

          ['age_min', 'default', 'value' => 1],
          ['age_min', 'required',
            'on' => [
              self::SCENARIO_SEARCH,
              self::SCENARIO_FILTER
            ],
          ],
          ['age_min', 'integer', 'message' => Yii::t('app', 'Age must be a numeric value')],
          ['age_min', 'filter', 'filter' => function ($value) {
              if ($value > 110) {
                  $value = 110; /* In other case we will get int/float date() error */
              }
              $age_min = date('Y-m-d', time()-($value*31556926));
              return (int)$age_min;
          }],

          ['age_max', 'default', 'value' => 110],
          ['age_max', 'required',
            'on' => [
              self::SCENARIO_SEARCH,
              self::SCENARIO_FILTER
            ],
          ],
          ['age_max', 'integer', 'message' => Yii::t('app', 'Age must be a numeric value')],
          ['age_max', 'filter', 'filter' => function ($value) {
              if ($value > 110) {
                  $value = 110; /* In other case we will get int/float date() error */
              }
              $age_max = date('Y-m-d', time()-($value*31556926));
              return (int)$age_max;
          }],

          ['search', 'string',
           'max' => 64,
           'min' => 3,
           'tooLong' => Yii::t('app', 'Search value must not be longer than 64 symbols'),
           'tooShort' => Yii::t('app', 'Search value must be longer than 3 symbols'),
            'on' => [
              self::SCENARIO_SEARCH,
              self::SCENARIO_FILTER
            ],
          ],

          ['search', 'filter', 'filter' => function ($value) {
              return StringHelper::clearString($value);
          }],

          ['friendship_status', 'default', 'value' => Friend::STATUS_ACTIVE_FRIENDSHIP],
          ['friendship_status', 'required',
            'on' => [
              self::SCENARIO_FILTER
            ]
          ],
          ['friendship_status', 'in', 'range' => [
            Friend::STATUS_ACTIVE_PENDING,
            Friend::STATUS_ACTIVE_FRIENDSHIP,
            Friend::STATUS_ACTIVE_ONESIDED
          ],
           'message' => Yii::t('app', 'Status value is invalid')],

          ['sort_by', 'default', 'value' => 'id'],
          ['sort_by', 'required',
            'on' => [
              self::SCENARIO_SEARCH,
              self::SCENARIO_FILTER
            ]
          ],
          ['sort_by', 'in', 'range' => ['id','rating'],
           'message' => Yii::t('app', 'Sort value is invalid')],

          ['page', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
          'id' => Yii::t('app', 'ID'),
          'name' => Yii::t('app', 'Name'),
          'last_name' => Yii::t('app', 'Last name'),
          'nickname' => Yii::t('app', 'Nickname'),
          'status' => Yii::t('app', 'Status'),
          'family' => Yii::t('app', 'Relationship'),
          'birthday' => Yii::t('app', 'Birthday'),
          'birthday_show' => Yii::t('app', ''),
          'country' => Yii::t('app', 'Country'),
          'city' => Yii::t('app', 'City'),
          'culture' => Yii::t('app', 'Culture'),
          'team' => Yii::t('app', 'Team'),
          'phone' => Yii::t('app', 'Phone'),
          'site' => Yii::t('app', 'Website'),
          'skype' => Yii::t('app', 'Skype'),
          'music' => Yii::t('app', 'Music'),
          'film' => Yii::t('app', 'Movies'),
          'shows' => Yii::t('app', 'Shows'),
          'books' => Yii::t('app', 'Books'),
          'game' => Yii::t('app', 'Games'),
          'citation' => Yii::t('app', 'Quotes'),
          'about' => Yii::t('app', 'About me'),
          'politics' => Yii::t('app', 'Politics'),
          'world_view' => Yii::t('app', 'World view'),
          'worth_life' => Yii::t('app', 'Worth in life'),
          'worth_people' => Yii::t('app', 'Worth in people'),
          'inspiration' => Yii::t('app', 'Inspiration sources'),
          'sex' => Yii::t('app', 'Sex'),
          'rating' => Yii::t('app', 'Rating'),
        ];
    }

    /**
     * Family status list
     * @return array
     */
    public static function familyList()
    {
        return [
          0 => Yii::t('app', '- none -'),
          1 => Yii::t('app', 'Замужем'),
          2 => Yii::t('app', 'Женат'),
          3 => Yii::t('app', 'Не замужем'),
          4 => Yii::t('app', 'Не женат'),
          5 => Yii::t('app', 'Встречаюсь'),
          6 => Yii::t('app', 'Любовь'),
          7 => Yii::t('app', 'Все сложно'),
          8 => Yii::t('app', 'В активном поиске'),
        ];
    }

    /**
     * Sex status list
     * @return array
     */
    public static function sexList()
    {
        return [
          'male' => Yii::t('app', 'Мужской'),
          'female' => Yii::t('app', 'Женский'),
        ];
    }

    /**
     * Culture names list
     *
     * @param  boolean  $forCSS If set to true, the names will be returned as a class name representation in CSS file.
     * @return array
     */
    public static function cultureList($forCSS = false)
    {
        if ($forCSS == true) {
            return [
              0 => 'default',
              1 => 'breaking',
              2 => 'graffiti',
              3 => 'rap',
              4 => 'dj',
            ];
        }

        return [
          0 => Yii::t('app', 'Not selected...'),
          1 => Yii::t('app', 'b-boy/b-girl'),
          2 => Yii::t('app', 'graffiti writer'),
          3 => Yii::t('app', 'mc/rapper'),
          4 => Yii::t('app', 'dj/beatmaker'),
        ];
    }

    /* Relations */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id']);
    }
    public function getGeolocationCities()
    {
        return $this->hasOne(GeolocationCities::className(), ['vk_city_id' => 'city']);
    }
    public function getGeolocationCountries()
    {
        return $this->hasOne(GeolocationCountries::className(), ['vk_country_id' => 'country']);
    }
}
