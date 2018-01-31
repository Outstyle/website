<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\SEOHelper;

use frontend\widgets\UserProfileBlock;
use frontend\widgets\UserFriendsBlock;
use frontend\widgets\UserVideosBlock;
use frontend\widgets\UserBoardPost;

/* @var $this yii\web\View */
/* @var $user @frontend/controllers/BoardController */

SEOHelper::setMetaInfo($this);

/* --- LEFT BLOCK SECTION --- */
echo Html::beginTag('section', ['id' => 'leftBlock']);

    # PROFILE widget | @frontend/widgets/UserProfileBlock.php
    echo UserProfileBlock::widget([
      'user' => $user
    ]);

    # FRIENDS widget | @frontend/widgets/UserFriendsBlock.php
    echo UserFriendsBlock::widget([
      'friends' => $user->friend
    ]);

    # VIDEOS widget | @frontend/widgets/UserVideosBlock.php
    echo UserVideosBlock::widget([
      'videos' => $user->video,
      'options' => [
          'title' => Yii::t('app', 'Videos'),
          'class' => 'user__videos u-window-box--medium u-window-box--shadowed',
          'cell_class' => 'u-letter-box--medium',
      ]
    ]);

echo Html::endTag('section');


/* --- RIGHT BLOCK SECTION --- */
echo Html::beginTag('section', ['id' => 'rightBlock']);

    # USER BOARD widget | @frontend/widgets/UserBoardPost.php
    echo UserBoardPost::widget([
      'posts' => $user->board
    ]);

echo Html::endTag('section');

/* JS: @see js/outstyle.userboard.js */
?>
<script>jQuery(document).ready(function(){userboardInit();});</script>
