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
        return self::find()->with(['comments'])->where(['id' => $videoId])->asArray()->one();
    }

    /**
     * Gets all videos by user ID
     * @param  int $userId    User ID (defaults to active user ID)
     * @return array
     */
    public static function getByUserId($userId = 0)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }
        return self::find()->where(['user' => $userId])->orderBy("id desc")->asArray()->all();
    }

    /* RELATIONS */
    public function getComments()
    {
        return $this->hasMany(Comments::className(), ['elem_id' => 'id'])->andWhere(['elem_type' => 'video']);
    }
}
