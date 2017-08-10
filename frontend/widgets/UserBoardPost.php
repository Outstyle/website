<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;
use common\components\helpers\StringHelper;

/**
 * Handles User -> Board -> Post block, showing posts of user
 * Part of Outstyle network
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @version 1.0
 *
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */
class UserBoardPost extends Widget
{

    /**
     * User board posts array
     * @var array
     */
    public $posts = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $posts = []; /* For storing organized post info */
        $cachedUserInfo = []; /* for taking out users info, avoiding DB queries - kinda mini-cache*/

        # Working with each post, setting necessary data
        if (isset($this->posts)) {
            foreach ($this->posts as $post) {

                # Post ID
                $id = $post->id;
                $userId = $post->user;
                $ownerId = $post->owner;

                # Post board owner (on whose board the post was written?)
                $posts[$id]['boardOwner'] = $ownerId;

                # Post creation time
                $posts[$id]['created'] = StringHelper::convertTimestampToHuman(strtotime($post->created));

                # Post text
                $posts[$id]['text'] = $post->text;

                # Post author
                $posts[$id]['userId'] = $userId;
                if (!isset($cachedUserInfo[$userId])) {
                    $posts[$id]['userAvatar'] = $cachedUserInfo[$userId]['userAvatar'] = \app\models\UserAvatar::getAvatarPath($userId);
                    $posts[$id]['userNickname'] = $cachedUserInfo[$userId]['userNickname'] = \app\models\UserNickname::getNickname($userId);
                    $posts[$id]['userCulture'] = $cachedUserInfo[$userId]['userCulture'] = \app\models\UserDescription::getUserCultureByUserId($userId);
                } else {
                    $posts[$id]['userAvatar'] = $cachedUserInfo[$userId]['userAvatar'];
                    $posts[$id]['userNickname'] = $cachedUserInfo[$userId]['userNickname'];
                    $posts[$id]['userCulture'] = $cachedUserInfo[$userId]['userCulture'];
                }

                # Post likes
                # TODO: can be cacheable?
                $posts[$id]['likesCount'] = \app\models\Likes::findLikesCount(Yii::$app->controller->id, $id);
                $posts[$id]['myLike'] = \app\models\Likes::findMyLike(Yii::$app->controller->id, $id);
            }
            $this->posts = $posts;
        }
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        # If user has no friends
        if (!$this->posts) {
            return;
        }

        return $this->render('userBoardPost', [
          'posts' => $this->posts,
        ]);
    }
}
