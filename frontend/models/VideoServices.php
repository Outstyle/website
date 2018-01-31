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
      0 => [
        'name' => 'unknown service',
        'link_template' => 'https://outstyle.org/unknown/video/{video_id}'
      ],
      1 => [
        'name' => 'youtube',
        'link_template' => 'https://youtu.be/{video_id}'
      ],
      2 => [
        'name' => 'vimeo',
        'link_template' => 'https://vimeo.com/{video_id}'
      ],
      3 => [
        'name' => 'dailymotion',
        'link_template' => 'https://dailymotion.com/video/{video_id}'
      ],
      4 => [
        'name' => 'rutube',
        'link_template' => 'https://rutube.ru/play/embed/{video_id}'
      ],
    ];

    /**
     * Gets video service name from $videoServices
     * @see: http://www.yiiframework.com/doc-2.0/guide-helper-array.html#getting-values
     *
     * @param  integer $id Service ID
     * @return string      Service name
     */
    public static function getVideoServiceNameByServiceId($id = 0)
    {
        return ArrayHelper::getValue(self::$videoServices, "{$id}.name");
    }

    /**
     * Generates a link to the video service, using unique video ID and service ID or name
     * @see: $videoServices for available service names and IDs. You can add more into that Array
     *
     * @param  integer $video_id
     * @param  string  $service_id
     * @return string
     */
    public static function generateServiceLink($video_id = 0, $service_id = '')
    {
        # Converting service ID int to service name, as we will be working with service names
        if (is_numeric($service_id)) {
            $service_id = self::getVideoServiceNameByServiceId($service_id);
        }

        # Generates a link from link_template, set in $videoServices array (using Yii::t() wrapper)
        foreach (self::$videoServices as $service) {
            if ($service['name'] == $service_id && isset($service['link_template'])) {
                return Yii::t('app', $service['link_template'], [
                  'video_id' => $video_id,
                ]);
            }
        }
    }
}
