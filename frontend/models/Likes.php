<?php

/* TODO: check methods usage around other models */

namespace frontend\models;

use Yii;
use common\components\helpers\ElementsHelper;

/**
 * This is the model class for table "{{%likes}}".
 *
 * @property string $user_id
 * @property string $elem_id
 * @property string $elem_table
 */
class Likes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%likes}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['elem_type', 'user_id', 'elem_id'], 'required'],
            [['user_id', 'elem_id'], 'integer'],
            ['elem_type', 'in', 'range' => ElementsHelper::$allowedElements],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'elem_type' => Yii::t('app', 'Table'),
            'user_id' => Yii::t('app', 'ID'),
            'elem_id' => Yii::t('app', 'Element'),
        ];
    }

    /**
     * Adds a single like to DB.
     *
     * @param string $elem_type Type of he element being liked (see in range)
     * @param int    $elem_id   Element ID
     */
    public static function addLike($elem_type, $elem_id)
    {
        $uid = Yii::$app->user->id;
        $args = array(
            'elem_type' => $elem_type,
            'elem_id' => $elem_id,
            'user_id' => $uid,
        );
        $added = null;

        $like = self::find()->where($args)->one();
        if ($like) {
            $like->delete();
            $added = false;
        } else {
            $like = new self();
            $like->elem_type = $elem_type;
            $like->elem_id = $elem_id;
            $like->user_id = $uid;
            $like->save();
            $added = true;
        }

        return array('ok' => true, 'added' => $added);
    }

    /**
     * Deletes all likes from DB by element's type and ID.
     *
     * @param string $elem_type Element type (see in range)
     * @param string $elem_id   Element ID
     *
     * @return true
     */
    public static function deleteLikes($elem_type, $elem_id)
    {
        $args = array(
            'elem_type' => $elem_type,
            'elem_id' => $elem_id,
        );

        self::deleteAll($args);

        return true;
    }

    /**
     * Checks if the element is being liked by the user or not (SINGLE CHECK FROM DB).
     *
     * @param string $elem_type Element type (see in range)
     * @param string $elem_id   Element ID
     *
     * @return int Is already liked or not
     */
    public static function findMyLike($elem_type, $elem_id)
    {
        $model = self::find()->where(['user_id' => Yii::$app->user->id, 'elem_type' => $elem_type, 'elem_id' => $elem_id])->one();

        return (!empty($model)) ? 1 : 0;
    }

    /**
     * Count likes from DB by element's type and id (SINGLE CHECK FROM DB).
     *
     * @param string $elem_type Element type (see in range)
     * @param string $elem_id   Element ID
     *
     * @return int Likes count
     */
    public static function findLikesCount($elem_type, $elem_id)
    {
        return self::find()->where(array('elem_type' => $elem_type, 'elem_id' => $elem_id))->count();
    }

    /**
     * Checks for logged in user's like.
     *
     * @param object $likes An object containing an array of likes from DB
     *
     * @return bool Is user's like found or not?
     */
    public static function checkForMyLike($likes, $commentId)
    {
        if (Yii::$app->user->id) {
            foreach ($likes as $like) {
                if ($like->elem_id == $commentId) {
                    return true;
                }
            }
        }

        return false;
    }
}
