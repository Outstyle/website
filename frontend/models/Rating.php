<?php

namespace frontend\models;

use Yii;
use frontend\models\RatingLog;
use UserDescription;

/**
 * This is the model class for table "z_rating_events".
 *
 * @property integer $id
 * @property string $event_name
 * @property string $event_limit
 * @property string $limit_type
 * @property integer $event_price
 * @property integer $active
 */
class Rating extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rating_events}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_name', 'event_limit', 'limit_type', 'event_price', 'active'], 'required'],
            [['event_limit', 'event_price', 'active'], 'integer'],
            [['event_name', 'limit_type'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_name' => 'Event Name',
            'event_limit' => 'Limit',
            'limit_type' => 'Limit Type',
            'event_price' => 'Event Price',
            'active' => 'Active',
        ];
    }

    public static function doEvent($event_name){

        $uid = Yii::$app->user->id;
        $newEvent = Rating::find()->where(array('event_name' => $event_name))->one();
        if($newEvent['active']){

            $userlog = RatingLog::find()->where(array('user_id' => $uid, 'event_id' => $newEvent['id']))->one();
            if (!$userlog) {
                $userlog = new RatingLog;
                $userlog->user_id = $uid;
                $userlog->event_id = $newEvent['id'];
            }
            //--------------Если на событие нет ограничения сразу создаём запись в лог-------------
            if($newEvent['event_limit'] == 0){   
                return self::createLogRecord($userlog);
            }

            //-----------------Если ограничение есть и оно не превышено--------------------------

            if(date('d') <=7){
                $lastweek = 30+date("d")-7;
            }
            else{
                $lastweek = date("d")-7;
            }

            if(date('d') >= 23){
                $nextweek = date("d")+7-30;
            }
            else{
                $nextweek = date("d")+7;
            }


            $current_date = date("Y-m-d H:i:s");

            $today = substr($current_date, 0, 10);

            $lastdate = $userlog['event_date'] ? substr($userlog['event_date'], 0, 10) : 0;

            if($newEvent['limit_type'] == 'daily'){
                    if($lastdate != $today){
                        $userlog['event_count'] = 0;
                    }
            }

            if($newEvent['limit_type'] == 'weekly'){
                    if($lastdate >= $lastweek && $lastdate <= $nextweek){
                        $userlog['event_count'] = 0;
                    }
            }

            if($userlog['event_count'] < $newEvent['event_limit']){
               return self::createLogRecord($userlog);
            }
        }
    }
    private static function createLogRecord($logrecord)
    {        
        $logrecord->event_count++;
        $logrecord->save();
 
        return $logrecord;
    }
}
