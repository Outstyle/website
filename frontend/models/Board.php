<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%board}}".
 *
 * @property bigint(20) $id
 * @property int(11) $user
 * @property int(11) $owner
 * @property timestamp $created
 * @property text $text
 * @property bigint(20) $photo
 * @property bigint(20) $notice
 * @property int(11) $repost
 * @property varchar(255) $repost_type
 */
class Board extends \yii\db\ActiveRecord
{
    public static $boardPageSize = 15;
    public static $boardOrderBy = 'id desc';

    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return '{{%board}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            [['user', 'owner'], 'required'],
            [['user', 'owner', 'repost'], 'integer'],
            [['created'], 'safe'],
            [['repost_type'], 'string', 'length' => [0, 255]],
            [['text'], 'string', 'length' => [0, 65535]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'User'),
            'owner' => Yii::t('app', 'Owner'),
            'created' => Yii::t('app', 'Created'),
            'text' => Yii::t('app', ''),
        ];
    }

    /**
     * Gets user board by user ID
     * @param  int $userId    User ID
     * @return array
     */
    public static function getByUserId($userId)
    {
        $board = User::find()
        ->where([
          'id' => $userId,
          'status' => User::STATUS_ACTIVE
        ])
        ->with([
          'userDescription',
          'userPrivacy',
          'video' => function (\yii\db\ActiveQuery $query) {
              $query->orderBy(self::$boardOrderBy)->limit(2)->asArray()->all();
          },
          'friend' => function (\yii\db\ActiveQuery $query) {
              $query->orderBy(self::$boardOrderBy)->limit(6)->asArray()->all();
          },
          'board' => function (\yii\db\ActiveQuery $query) {
              $query->orderBy(self::$boardOrderBy)->limit(self::$boardPageSize)->all();
          }
        ])
        ->one();

        return $board;
    }
}
