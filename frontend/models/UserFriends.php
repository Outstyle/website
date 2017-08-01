<?php

namespace app\models;

use Yii;

class UserFriends extends \common\models\User
{
    public static function getFriends($owner)
    {
        return UserDescription::find()
            ->with(['user'])
            ->join('JOIN',
                   '{{%friend}}',
                   '({{%user_description}}.`id` = {{%friend}}.`user1` AND {{%friend}}.`user2` = :user AND {{%friend}}.`status` = :status)
                    OR ({{%user_description}}.`id` = {{%friend}}.`user2` AND {{%friend}}.`user1` = :user AND {{%friend}}.`status` = :status)', [
                    ':user' => $owner,
                    ':status' => 1,
            ])
            ->orderBy("id desc")
            ->limit(9)
            ->all();
    }
}
