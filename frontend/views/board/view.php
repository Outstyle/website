<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\SEOHelper;

use frontend\widgets\UserProfileBlock;
use frontend\widgets\UserFriendsBlock;
use frontend\widgets\UserVideosBlock;
use frontend\widgets\UserBoardPost;

/* @var $this yii\web\View */
/* @var $user @frontend/user/UsersController */

SEOHelper::setMetaInfo($this);

/* --- LEFT BLOCK SECTION --- */
echo Html::beginTag('section', ['id' => 'leftBlock']);

    # PROFILE
    echo UserProfileBlock::widget([
      'user' => $user
    ]);

    # FRIENDS
    echo UserFriendsBlock::widget([
      'friends' => $user->friend
    ]);

    # VIDEOS
    echo UserVideosBlock::widget([
      'videos' => $user->video
    ]);

echo Html::endTag('section');


/* --- RIGHT BLOCK SECTION --- */
echo Html::beginTag('section', ['id' => 'rightBlock']);

    # USER BOARD: posts
    echo UserBoardPost::widget([
      'posts' => $user->board
    ]);

echo Html::endTag('section');

/* JS: @see js/outstyle.userboard.js */
?>
<script>jQuery(document).ready(function(){userboardInit();});</script>
