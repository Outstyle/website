<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

class VideoServices extends Video
{
    /**
     * Available video providers - unique ID's
     * @var array
     */
    private static $videoServices = [
      0 => 'unknown service',
      1 => 'youtube',
      2 => 'vimeo',
      3 => 'dailymotion',
      4 => 'rutube',
    ];

    /**
     * Gets video service name from $videoServices
     * @param  integer $id Service ID
     * @return string      Service name
     */
    public static function getVideoServiceNameByServiceId($id = 0)
    {
        return ArrayHelper::getValue(self::$videoServices, $id);
    }
}
