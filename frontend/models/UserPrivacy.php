<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%user_privacy}}".
 *
 * @property integer $id
 * @property integer $birthday
 * @property integer $city
 * @property integer $phone
 * @property integer $site
 * @property integer $skype
 * @property integer $board_comment
 * @property integer $private_messages
 * @property integer $invite_group
 */
class UserPrivacy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_privacy}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'birthday', 'city', 'phone', 'site', 'skype', 'board_comment','private_messages', 'invite_group'], 'default', 'value' => 0],
            [['id', 'birthday', 'city', 'phone', 'site', 'skype', 'board_comment','private_messages', 'invite_group'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'birthday' => Yii::t('app', 'Кто может видеть мой день рождения?'),
            'city' => Yii::t('app', 'Кто может видеть мой город?'),
            'phone' => Yii::t('app', 'Кто может видеть мой телефон?'),
            'site' => Yii::t('app', 'Кто может видеть мой сайт?'),
            'skype' => Yii::t('app', 'Кто может видеть мой Skype?'),
            'board_comment' => Yii::t('app', 'Кто может писать, комментировать на стене?'),
            'private_messages' => Yii::t('app', 'Кто может писать сообщения?'),
            'invite_group' => Yii::t('app', 'Кто может приглашать в группы?'),
        ];
    }

    public static function setNames()
    {
        return [
            0 => Yii::t('app', 'Все пользователи'),
            1 => Yii::t('app', 'Только друзья'),
            2 => Yii::t('app', 'Друзья и друзья друзей'),
            3 => Yii::t('app', 'Только я'),
        ];
    }


    public static function getNames($id = null)
    {
        $r = self::setNames();
        if ($id !== null) {
            return (isset($r[$id]) && $id > 0)?$r[$id]:null;
        }
        return $r;
    }

    public static function getReaction($field, $model, $id, $friendsGeneral)
    {
        if (isset($model->{$field})) {
            switch ($model->{$field}) {
                case 0:
                    return true;
                    break;
                case 3:
                    return false;
                    break;
                case 1:
                    if ($id) {
                        return true;
                    } else {
                        return false;
                    }
                case 2:
                    if (count($friendsGeneral) || $id) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
                default:
                    return false;
                    break;
            }
        } else {
            return true;
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function getPrivacy($privacy, $idOwner)
    {
        if ($idOwner === Yii::$app->user->id) {
            return true;
        }
        switch ($privacy) {
                case 0:
                    return true;
                    break;
                case 3:
                    return false;
                    break;
                case 1://если друг
                    if (self::checkOnFriend($idOwner)) {
                        return true;
                        break;
                    } else {
                        return false;
                        break;
                    }
                case 2:// если друг друга
                    if (self::checkOnFriendMyFriend($idOwner)) {
                        return true;
                        break;
                    } else {
                        return false;
                        break;
                    }
                default:
                    return false;
                    break;
            }
    }

    public static function checkOnFriend($id)
    {
        $model = Friend::find()
                        ->where(
                            "(user1 = :id AND user2 = :user AND status = :status)
                                OR (user1 = :user AND user2 = :id AND status = :status)",
                            [':id' => $id, ':user' => Yii::$app->user->id, ':status' => 1])
                        ->one();
        if (!$model) {
            return 0;
        } else {
            return 1;
        }
    }

    public static function checkOnFriendMyFriend($id)
    {
        $model = Friend::find()
                        ->where(
                            "(user1 = :id AND status = :status)
                                OR (user2 = :id AND status = :status)",
                            [':id' => $id, ':status' => 1])
                        ->all();

        foreach ($model as $key => $value) {
            if ($model[$key]['user1'] == Yii::$app->user->id || $model[$key]['user2'] == Yii::$app->user->id) {
                return 1;
            }

            $idFriend = ($model[$key]['user1'] != $id) ? $model[$key]['user1'] : $model[$key]['user2'];

            $checkfrFr = Friend::find()
                        ->where(
                            "(user1 = :id AND user2 = :user AND status = :status)
                                OR (user1 = :user AND user2 = :id AND status = :status)",
                            [':id' => $idFriend, ':user' => Yii::$app->user->id, ':status' => 1])
                        ->one();
            if ($checkfrFr) {
                return 1;
            }
        }
        return 0;
    }
}
