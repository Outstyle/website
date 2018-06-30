<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace common\models;

use Yii;

use common\models\user\UserDescription;

/**
  * This is the model class for table "{{%friend}}".
  * This model serves as a common one, both for backend and frontend.
  *
  * Only general yii2 built-in methods should be used here!
  * If you want to add some modifications or new methods, you should extend from this model.
  * @see: frontend/models/Friend.php
  * @see: backend/models/Friend.php
  *
  * Also, all the relations with other models should be declared in this common model.
  *
  * @property int $id
  * @property int $user1
  * @property int $user2
  * @property int $status
  *
  * @author [SC]Smash3r <scsmash3r@gmail.com>
  * @since 1.0
  */
class Friend extends \yii\db\ActiveRecord
{

    /**
     * STATUS: initiator of friendship is waiting for friend to accept his friendship (default status)
     * @var integer
     */
    const STATUS_ACTIVE_PENDING = 0;
    /**
     * STATUS: friendship is confirmed by both users
     * @var integer
     */
    const STATUS_ACTIVE_FRIENDSHIP = 1;
    /**
     * STATUS: initiator of friendship is having a friend, but friend has not confirmed (or rejected) friendship
     * @var integer
     */
    const STATUS_ACTIVE_ONESIDED = 2;



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

    /* RELATIONS */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user1']);
    }
}
