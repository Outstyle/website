<?php

namespace app\models;

use app\models\City;

use Yii;

/**
 * This is the model class for table "{{%friend}}".
 *
 * @property string $id
 * @property string $user1
 * @property string $user2
 * @property string $status
 */
class Friend extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%friend}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user1', 'user2'], 'required'],
            [['user1', 'user2', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user1' => Yii::t('app', 'User1'),
            'user2' => Yii::t('app', 'User2'),
        ];
    }

    public static function whoIs($user)
    {
        if (Yii::$app->user->id == $user->id) {
            return 3;
        }
        $model = self::find()->where("(user1 = :current AND user2 = :user) OR (user2 = :current AND user1 = :user)", [
            ':current' => Yii::$app->user->id,
            ':user' => $user->id,
        ])->one();
        if (!$model) {
            return 2;
        }

        return 0;
    }

    public static function checkOnFriend($id)
    {
        $model = self::find()
                        ->where(
                            "(user1 = :id AND user2 = :user )
                                OR (user1 = :user AND user2 = :id) ",
                            [':id' => $id, ':user' => Yii::$app->user->id])
                        ->one();
        if (!$model) {
            return 0;
        } elseif ($model->status == 1) {
            return 1;
        } elseif ($model->status == 0) {
            return 2;
        }
    }

    public static function getRequest()
    {
        return self::find()->where(
                                    "user2 = :user AND status = :status",
                                    [':user' => Yii::$app->user->id, ':status' => 0])
                                ->count();
    }

    public static function all($userId = null)
    {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }
        return self::find()->where(
                                    "(user1 = :user OR user2 = :user) AND status = :status",
                                    [':user' => $userId, ':status' => 1])
                                ->count();
    }
}
