<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
* This is the model class for table "z_video".
*
* @property integer $id
* @property string $user
* @property string $service
* @property string $video_id
* @property string $title
* @property string $description
* @property string $url_img
* @property string $url_iframe
* @property integer $created
* @property integer $privacy_video
* @property integer $privacy_comments
*/

class Video extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%video}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
           TimestampBehavior::className(),
       ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [
            [
              'user',
              'service_id',
              'video_id',
              'video_title',
              'video_img'
            ], 'required'
          ],
          [
            [
              'user'
            ], 'integer'
          ],
          [
            [
              'service_id',
              'video_id',
              'video_title',
              'video_desc',
              'video_img'
            ], 'string', 'max' => 255
          ],
          [
            [
              'created_at'
            ], 'safe'
          ]
        ];
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
           'id' => 'ID',
           'user' => 'User',
           'service_id' => 'Service',
           'video_id' => 'Video ID',
           'video_title' => 'Title',
           'video_desc' => 'Description',
           'video_img' => 'Url Img',
           'created_at' => 'Created'
        ];
    }
}
