<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class Photo extends \common\models\Photo
{

    /**
     * @var $defaultPageSize  How much photos per request to get
     */
    public static $defaultPageSize = 15;

    /**
     * Gets photo by its unique ID (DB column: id)
     * @param  string $photoId    Photo ID
     * @return array
     */
    public static function getById($photoId)
    {
        return self::find()->with(['comments'])->where(['id' => $photoId])->asArray()->one();
    }

    /**
     * Gets user photos from DB and returns an array of data
     *
     * @param  array   $where   WHERE clause to add for more precise selection.
     * @param  int     $page    Page number. Must be >0 for pagination to appear
     * @param  int     $userId  User's ID
     *
     * @return array|null         Photos data
     */
    public static function getPhotos($where = [], $page = null, $userId = 0)
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
        $photosQuery = self::find()->where($where)->orderBy('id desc');
        $photosQuery = $photosQuery->limit(self::$defaultPageSize);

        /* If we have pagination */
        if ($page) {
            $pagination = new Pagination([
              'defaultPageSize' => self::$defaultPageSize,
              'totalCount' => $photosQuery->count(),
              'page' => (int)$page - 1,
            ]);

            $photosQuery = $photosQuery->offset($pagination->offset)->limit($pagination->limit);
        }

        /* And finally let's make our request to DB */
        $photos = $photosQuery->asArray()->all();

        /* If we don't have any photos queried, we won't populate photos model */
        if (!$photos) {
            return;
        }

        return $photos;
    }

    /**
     * Delete all photos by photoalbum ID
     * @param  integer $albumId
     * @return int    	         The number of rows deleted
     */
    public static function deleteAllPhotosByPhotoalbumId($albumId = 0)
    {
        return self::deleteAll(['album' => $albumId]);
    }
}
