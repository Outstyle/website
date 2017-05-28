<?php

namespace app\models;

use Yii;

use common\components\helpers\StringHelper;

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
class Comments extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['elem_type', 'elem_id', 'user_id'],
                'required'
            ],
            [
                ['comment'],
                'required',
                'message' => 'COMMENT_EMPTY'
            ],
            [
                'elem_type',
                'in',
                'range' => ['news', 'school', 'board', 'comments', 'photo', 'video', 'article', 'events']
            ],
            [
                ['elem_id', 'user_id'],
                'integer'
            ],
            [
                'created',
                'safe'
            ],
            [
                'comment',
                'string',
                'min' => 1,
                'max' => 5000
            ],
            [
                'elem_type',
                'string',
                'max' => 255
            ],
            [
                ['user_id', 'elem_id', 'comment', 'elem_type'],
                'unique',
                'targetAttribute' => ['user_id', 'elem_id', 'comment', 'elem_type'],
                'message' => 'COMMENT_DUPLICATE'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'elem_type' => 'Elem Type',
            'elem_id' => 'Elem ID',
            'user_id' => 'User ID',
            'created' => 'Created',
            'comment' => 'Comment',
        ];
    }


/**
 * Adds a comment to DB
 *
 * @param string    $elem_type  Which type of element does this comment represents (i.e. news, school, board)
 * @param int       $elem_id    Element's id
 * @param string    $text       Comment's text (body)
 * @param string    $atype      Attachment type for comment (video, photo)
 * @param int       $aid        Attachment's ID
 * @return int      Added comment ID
 */
    public static function addComment($elem_type, $elem_id, $text, $atype = null, $aid = null)
    {
        $uid = Yii::$app->user->id;

        $comment = new Comments;
        $comment->elem_type = $elem_type;
        $comment->elem_id = $elem_id;
        $comment->user_id = $uid;
        $comment->comment = $text;

        /* If our comment passes validation rules and is not a duplicate... */
        if ($comment->validate()) {
            $comment->save();
        } else {
            return $comment->errors;
        }

        /* If our comment has an attachment */
        if ($atype && $aid) {
            $res = Attachments::addAttachment('comment', $id, $atype, $aid);
        }

        return $comment->id;
    }

/**
 * Delete comment
 *
 * @param  int          $id         Comment ID
 * @param  int          $user_id    User's ID
 * @return int|false                Deleted comment ID or false in case of error
 *
 * Read more about user roles permissions here: https://github.com/developeruz/yii2-db-rbac
 */
    public static function deleteComment($id, $user_id)
    {
        /* --- If it is user's own comment --- */
        $args = array(
            'id' => $id,
            'user_id' => $user_id,
        );

        /* --- 'comments/delete' is a permission name for comment deleting --- */
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
         * HACK: This approach with greedy 'user' query goes well, if you want to exclude comments from being populated, but violates SOLID principles
         * 'user' => function ($query) {$query->andWhere(['!=', 'status', User::STATUS_DELETED])->select('id');}
         */

        $commentsQuery = self::find()->with([
          'user' => function ($query) {
              $query->andWhere(['!=', 'status', User::STATUS_DELETED])->select('id');
          },
          'userDescription',
          'likes' => function ($query) {
              $query->andWhere(['elem_type' => 'comments']);
          }
        ])->where($where)->orderBy('id desc');

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
            $modelComments[$i]['userAvatar']         = UserAvatar::getAvatarPath($comments[$i]->user ? $comments[$i]->user->id : 0, 'medium');
            $modelComments[$i]['userCulture']        = UserDescription::getCultureList($comments[$i]->user ? $comments[$i]->userDescription->culture : 0, true);
            $modelComments[$i]['created']            = StringHelper::convertTimestampToHuman(strtotime($comments[$i]->created));
            $modelComments[$i]['commentText']        = $comments[$i]->comment;
            $modelComments[$i]['likeCount']          = count($comments[$i]->likes);
            $modelComments[$i]['myLike']             = Likes::checkForMyLike($comments[$i]->likes, $comments[$i]->id);
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

    /* Relations */
    public function attachments()
    {
        return $this->hasMany(Attachments::className(), ['elem_id' => 'id', 'elem_type' => 'comment']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUserDescription()
    {
        return $this->hasOne(UserDescription::className(), ['id' => 'user_id']);
    }

    public function getLikes()
    {
        return $this->hasMany(Likes::className(), ['elem_id' => 'id']);
    }
}
