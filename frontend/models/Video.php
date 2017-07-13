<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class Video extends \common\models\Video
{

    /**
     * Gets video by its unique ID (DB column: video_id)
     * @param  string $videoId    Video ID
     * @return array
     */
    public static function getById($videoId)
    {
        $video = self::find()->where(['id' => $videoId])->asArray()->one();
        $video['service_id'] = VideoServices::getVideoServiceNameByServiceId($video['service_id']);
        $video['service_link'] = VideoServices::generateServiceLink($video['video_id'], $video['service_id']);
        return $video;
    }

    /* RELATIONS */
    public function comments()
    {
        return $this->hasMany(Comments::className(), ['elem_id' => 'id'])->andWhere(['elem_type' => 'video']);
    }
}
