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
        $culture = 0;
        $user = self::findOne($userId);

        if (isset($user->culture)) {
            $culture = $user->culture;
        }

        return ArrayHelper::getValue(self::cultureList($forCSS), $culture);
    }

    /**
     * Search users, using an array of data
     * @param  array  $data         $_GET or $_POST array with data to be validated
     * @param  string $dataPrefix   data's prefix name for load() method
     * @param  int    $page
     * @return array                An array of users
     */
    public static function findUsersByData($data, $dataPrefix = '', $page = 0)
    {
        $model = new self(['scenario' => self::SCENARIO_SEARCH]);
        $model->load($data, $dataPrefix);

        if ($model->validate()) {
            $query = self::find()
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
              ])
              ->join('INNER JOIN', '{{%friend}} f',
                  '(
                    ({{%user_description}}.`id` = f.`user1` AND f.`user2` = :user) OR
                    ({{%user_description}}.`id` = f.`user2` AND f.`user1` = :user)
                  )
                  AND
                  IF (
                    :age_start != "" OR
                    :age_end != "",
                    (
                      {{%user_description}}.`birthday` < :age_start AND
                      {{%user_description}}.`birthday` > :age_end
                    ),
                    TRUE
                  )
                  AND
                  IF (
                    :sex = "male" OR
                    :sex = "female",
                    (
                      {{%user_description}}.`sex`= :sex
                    ),
                    TRUE
                  )
                  AND f.`status` = :status
                  ',
                  [
                    ':user' => (int)$model->user,
                    ':age_start' => (int)$model->age_start,
                    ':age_end' => (int)$model->age_end,
                    ':sex' => $model->sex,
                    ':status' => $model->status
                  ]);

            /* Additional search params */
            if ($model->country) {
                $query->where([
                  'country' => $model->country
                ]);
            }

            if ($model->city) {
                $query->where([
                  'city' => $model->city
                ]);
            }

            if ($model->culture) {
                $query->where([
                  'culture' => $model->culture
                ]);
            }

            if ($model->search) {
                $query->andWhere([
                  'like',
                  'CONCAT(`name`, `last_name`, `nickname`)',
                  $model->search
                ]);
            }

            $pagination = new Pagination([
                'defaultPageSize' => 50,
                'totalCount' => $query->count(),
                'page' => $page,
            ]);

            return $query->orderBy([$model->sort_by => SORT_DESC])
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()
                ->all();
        } else {
            $e['errors'] = $model->errors;
            return $e;
        }
    }
}
