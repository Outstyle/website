<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace app\models;

use Yii;
use yii\data\Pagination;

use app\models\UserDescription;

/**
* This is the model class for table "{{%friend}}".
* This model serves as a frontend one and should always extend common Friend model.
*
* Only custom methods are stored here.
*
* @author [SC]Smash3r <scsmash3r@gmail.com>
* @since 1.0
*/
class Friend extends \common\models\Friend
{
    /**
     * @var $defaultPageSize  How much friends per request to get
     */
    public static $defaultPageSize = 6;


    /**
     * Get user friends by userId and status
     *
     * Expression examples:
     *
     * select with name concat
     * ->select(['{{%user_description}}.id','culture', 'avatar', 'city',
     * new \yii\db\Expression("CONCAT(`name`,' ', `nickname`, ' ',`last_name`) as name")])
     *
     * @param  integer $userId    User ID
     * @param  integer $status    Friendship status (@see: \common\models\Friend for status constants)
     * @param  integer $page
     * @return array
     */
    public static function getUserFriends($status = 0, $userId = 0, $page = 0)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }

        /* First, getting a list of friends from `friend` table
           Friend can either be user1 or user2, depending on who initiated the friendship */
        $friendsQuery = self::find()
        ->where('(user1 = :user AND status = :status) OR (user2 = :user AND status = :status)',
          [
            ':user' => (int)$userId,
            ':status' => (int)$status,
          ]);

        $friendsQuery = $friendsQuery->limit(self::$defaultPageSize);

        /* If we have pagination */
        if ($page) {
            $pagination = new Pagination([
              'defaultPageSize' => self::$defaultPageSize,
              'totalCount' => $friendsQuery->count(),
              'page' => (int)$page,
            ]);

            $friendsQuery->offset($pagination->offset)->limit($pagination->limit);
        }

        /* Getting all the friends and traversing through array to get rid of current user id
           This is also needed to form an array of actual IDs of friends to get their userinfo */
        $friends = $friendsQuery->asArray()->all();

        foreach ($friends as $friend) {
            if ($friend['user1'] != $userId) {
                $friends['active'][] = $friend['user1'];
            }
            if ($friend['user2'] != $userId) {
                $friends['active'][] = $friend['user2'];
            }
        }

        /* Second query for user description and info
           `UserDescription::findUsersByData` can be used instead, for additinal data or same cache */
        $usersQuery = UserDescription::find()
        ->where([
          'id' => $friends['active']
        ])
        ->select('{{%user_description}}.id, name, last_name, nickname, culture, avatar, city')
        ->with([
          'user' => function ($query) {
              $query->select('id, lastvisit');
          },
          'geolocationCities' => function ($query) {
              $query->select('vk_city_id, name, area, region');
          }
        ])
        ->asArray()
        ->all();

        return $usersQuery;
    }
}
