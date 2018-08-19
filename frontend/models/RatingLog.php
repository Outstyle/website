<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_rating_log".
 *
 * @property integer $id
 * @property string $event_id
 * @property string $event_date
 * @property string $user_id
 * @property string $event_count
 */
class RatingLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rating_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'user_id', 'event_count'], 'required'],
            [['event_id', 'user_id', 'event_count'], 'integer'],
            [['event_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'event_date' => 'Event Date',
            'user_id' => 'User ID',
            'event_count' => 'Event Count',
        ];
    }
}
