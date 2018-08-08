<?php

namespace app\models;

use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%user_description}}".
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
        $user = self::findOne($userId);

        if (isset($user->culture)) {
            $culture = $user->culture;
        }

        return ArrayHelper::getValue(self::cultureList($forCSS), $culture ?? 0);
    }

    /**
     * Find users start point
     * @return query
     */
    public static function findUsers()
    {
        return self::find()
          ->select('{{%user_description}}.id, name, last_name, nickname, birthday, country, city, culture, sex, avatar')
          ->with([
            'user' => function ($query) {
                $query->select('id, lastvisit');
            },
            'geolocationCities' => function ($query) {
                $query->select('vk_city_id, name, area, region');
            },
            'geolocationCountries' => function ($query) {
                $query->select('vk_country_id, iso_code, name_ru');
            }
          ]);
    }

    /**
     * Search users, using an array of data
     * @param  array  $data         $_GET or $_POST array with validated data
     * @return array                An array of users
     */
    public static function findUsersByData($data = [])
    {
        $query = self::findUsers();

        /* Additional search params */
        if ($data['age_min']) {
            $query->andWhere([
              '<',
              'birthday',
              $data['age_min']
            ]);
        }

        if ($data['age_max']) {
            $query->andWhere([
              '>',
              'birthday',
              $data['age_max']
            ]);
        }

        if ($data['sex']) {
            $query->andWhere([
              'sex' => $data['sex']
            ]);
        }

        if ($data['country']) {
            $query->andWhere([
              'country' => $data['country']
            ]);
        }

        if ($data['city']) {
            $query->andWhere([
              'city' => $data['city']
            ]);
        }

        if ($data['culture']) {
            $query->andWhere([
              'culture' => $data['culture']
            ]);
        }

        if ($data['search']) {
            $query->andWhere([
              'like',
              'CONCAT(`name`, `last_name`, `nickname`)',
              $data['search']
            ]);
        }

        return $query->orderBy([
          $data['sort_by'] => SORT_DESC
        ]);
    }
}
