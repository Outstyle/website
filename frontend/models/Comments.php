<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\components\helpers\StringHelper;
use common\components\helpers\ElementsHelper;

/**
 * This is the model class for table "z_comments".
 *
 * @property integer    $id
 * @property string     $elem_type
 * @property int        $elem_id
 * @property int        $user_id
 * @property timestamp  $created
 * @property string     $comment
 */
class Comments extends \common\models\Comments
{

    /**
     * Adds a comment to DB
     *
     * @param string    $elem_type  Which type of element does this comment represents (i.e. news, school, board)
     * @param int       $elem_id    Element's id
     * @param string    $text       Comment's text (body)
     * @param string    $scenario   Scenario to use (i.e. comment with attachments)
     * @return int      Added comment ID
     */
    public static function addComment($elem_type, $elem_id, $text, $scenario = self::SCENARIO_DEFAULT)
    {
        $comment = new self(['scenario' => $scenario]);
        $comment->elem_type = $elem_type;
        $comment->elem_id = $elem_id;
        $comment->user_id = Yii::$app->user->id;
        $comment->comment = $text;

        /* If our comment passes validation rules and is not a duplicate... */
        if ($comment->validate()) {
            $comment->save();
        } else {
            return $comment->errors;
        }

        return $comment->id;
    }

    /**
     * Delete comment
     * Read more about user roles permissions here: https://github.com/developeruz/yii2-db-rbac
     *
     * @param  int          $id         Comment ID
     * @param  int          $user_id    User's ID
     * @return int|false                Deleted comment ID or false in case of error
     */
    public static function deleteComment($id, $user_id)
    {
        /* --- If it is user's own comment --- */
        $args = array(
            'id' => $id,
            'user_id' => $user_id,
        );

        /* TODO FIXME: This should be in commentsController + likes check too! 'comments/delete' is a permission name for comment deleting */
        if (Yii::$app->user->can('comments/delete')) {
            $args = array(
                'id' => $id,
            );
        }

        if ($comment = self::find()->where($args)->one()) {
            $comment->delete();
            Likes::deleteLikes("comments", $id);
            return (int)$id;
        }
        return false;
    }

    /**
     * Edit comment
     *
     * @param  int       $id        Comment ID
     * @param  string    $text      Comment text
     * @return boolean   true|false
     */
    public static function editComment($id, $text)
    {
        $model = self::find()->where(array('id' => $id))->one();
        $model->comment = $text;
        if ($model->validate()) {
            $model->save();
            return true;
        }
        return false;
    }

    /**
     * Gets/retrieves comments from DB
     *
     * @param  array      $where    WHERE clause to add for more precise selection.
     * @param  int        $page     page number
     * @return array|null           Comments data
     */
    public static function getComments($where = [], $page = null)
    {
        /* Init values */
        $modelComments = [];

        /**
         * Getting the comments: let's start partially, adding parameters in case we have pagination (not yet implemented)
         * Notice that we are adding query parameters one by one (chaining), checking additional conditions
         * Read more about QB syntax here: http://www.yiiframework.com/doc-2.0/guide-db-query-builder.html
         *
         * Since we're having greedy query with 'user' table, we need to set proper relation between 'user' <-> 'comments'
         * 'User' query is needed to check whether user (and his data) is active or not.
         *
         * @see: http://www.yiiframework.com/doc-2.0/guide-db-active-record.html#relational-data.
         */

        $commentsQuery = self::find()->with([
          'user' => function ($query) {
              $query->andWhere(['!=', 'status', User::STATUS_DELETED])->select('id');
          },
          'userDescription',
          'attachments' => function ($query) use ($where) {
              $query->andWhere(['elem_type' => 5]); /* 5 is an ID for comments element type @see: helpers/ElementsHelper */
          },
          'likes' => function ($query) {
              $query->andWhere(['elem_type' => 'comments']);
          }
        ])->where($where)->orderBy('id asc');

        $comments = $commentsQuery->all();

        /* If we don't have any comments found, we won't populate comments model */
        if (!$comments) {
            return;
        }

        for ($i=0; $i<count($comments); $i++) {
            $modelComments[$i]['id']                 = $comments[$i]->id;
            $modelComments[$i]['type']               = $comments[$i]->elem_type;
            $modelComments[$i]['elemId']             = $comments[$i]->elem_id;
            $modelComments[$i]['userId']             = $comments[$i]->user_id;
            $modelComments[$i]['userNickname']       = $comments[$i]->user ? $comments[$i]->userDescription->nickname : 0;
            $modelComments[$i]['userAvatar']         = UserAvatar::getAvatarPath($comments[$i]->user ? $comments[$i]->user->id : 0, 'small');
            $modelComments[$i]['userCulture']        = $comments[$i]->user ? ArrayHelper::getValue(UserDescription::cultureList(true), $comments[$i]->userDescription->culture) : 0;
            $modelComments[$i]['created']            = StringHelper::convertTimestampToHuman(strtotime($comments[$i]->created));
            $modelComments[$i]['commentText']        = $comments[$i]->comment;
            $modelComments[$i]['likeCount']          = count($comments[$i]->likes);
            $modelComments[$i]['myLike']             = Likes::checkForMyLike($comments[$i]->likes, $comments[$i]->id);
            $modelComments[$i]['attachments']        = count($comments[$i]->attachments);
        }
        return $modelComments;
    }

    /**
     * Counts comments by element type and element id
     *
     * @param string    $elem_type  Which type of element does this comment represents (i.e. news, school, board)
     * @param int       $elem_id    Element's id
     * @return int                  Total comments
     */
    public static function countComments($elem_type, $elem_id)
    {
        return self::find()->where(array('elem_type' => $elem_type, 'elem_id' => $elem_id))->count();
    }
}
