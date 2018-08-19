<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_blacklist".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $blacklisted_id
 */
class Blacklist extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%blacklist}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'blacklisted_id'], 'required'],
            [['user_id', 'blacklisted_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'blacklisted_id' => 'Blacklisted ID',
        ];
    }

    public static function addBlacklist($bid)
    {  
        $uid = Yii::$app->user->id;
        $args = array('blacklisted_id' => $bid, 'user_id' => $uid);
        $added = null;
        $blacklist = Blacklist::find()->where($args)->one();
        if($blacklist) {
           $added = false;
        }
        else {
            $blacklist = new Blacklist();
            $blacklist->blacklisted_id = $bid;
            $blacklist->user_id = $uid;
            // var_dump($blacklist, $bid);
            $added = $blacklist->save();
        }
        return array('ok' => true, 'added' => $added);
    }

    public static function delBlacklist($id)
    {
        $uid = Yii::$app->user->id;
        return Blacklist::find()->where(array('id' => $id))->one()->delete();
    }
}
