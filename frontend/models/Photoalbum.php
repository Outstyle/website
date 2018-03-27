<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%photoalbum}}".
 * This model serves as a frontend one and should always extend common Photoalbum model.
 *
 * Only custom methods are stored here.
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class Photoalbum extends \common\models\Photoalbum
{

    /**
     * @var int $defaultPageSize        How much photoalbums per request to get
     */
    public static $defaultPageSize = 100;


    /**
     * Gets user photoalbums from DB and returns an array of data
     * @param  array   $where   WHERE clause to add for more precise selection.
     * @param  int     $page    Page number. Must be >0 for pagination to appear
     * @param  int     $userId  User's ID
     * @return array|null         Photos data
     */
    public static function getPhotoalbums($where = [], $page = null, $userId = 0)
    {
        /* Default user ID is current user */
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }

        /* Default arguments for 'where' clause */
        $where['user'] = $userId;

        /**
         * Getting the photos: let's start by partially adding parameters in case we have pagination
         * Notice that we are adding query parameters one by one (chaining), checking additional conditions
         * Read more about QB syntax here: http://www.yiiframework.com/doc-2.0/guide-db-query-builder.html
         */
        $photoalbumsQuery = self::find()->with([
          'photo' => function ($query) {
              $query->select('id, img, album, service_id');
          },
        ])->where($where)->orderBy('id desc');
        $photoalbumsQuery = $photoalbumsQuery->limit(self::$defaultPageSize);

        /* If we have pagination */
        if ($page) {
            $pagination = new Pagination([
              'defaultPageSize' => self::$defaultPageSize,
              'totalCount' => $photoalbumsQuery->count(),
              'page' => (int)$page - 1,
            ]);

            $photoalbumsQuery = $photoalbumsQuery->offset($pagination->offset)->limit($pagination->limit);
        }

        /* And finally let's make our request to DB */
        $photoalbums = $photoalbumsQuery->asArray()->all();

        /* If we don't have any photos queried, we won't populate photos model */
        if (!$photoalbums) {
            return;
        }

        return $photoalbums;
    }

    /**
     * Deletes photoalbum from DB
     * @param  integer $userId
     * @param  integer $albumId
     * @return int|false
     */
    public static function deleteOneByUserId($userId = 0, $albumId = 0)
    {
        return self::find()->where("user = :user AND id = :id", [
                      ':user' => $userId,
                      ':id' => $albumId
                    ])->one()->delete();
    }
}
