<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\SEOHelper;

use frontend\widgets\UserProfileBlock;
use frontend\widgets\UserFriendsBlock;
use frontend\widgets\UserVideosBlock;
use frontend\widgets\UserPhotosBlock;
use frontend\widgets\UserBoardPost;
use frontend\widgets\WidgetComments;

/* HACK: Check reviews and more indepth abstraction selftests */
/**
 * Main user board view
 *
 * @var array     $this                 yii\web\View
 * @var array     $user                 @frontend/controllers/BoardController
 * @var array     $friends              @frontend/controllers/BoardController
 * @var array     $isOwner              @frontend/controllers/BoardController [Needs review]
 * @var array     $boardOwnerUserId     @frontend/controllers/BoardController [Needs review]
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */

SEOHelper::setMetaInfo($this);

/* ! --- LEFT BLOCK SECTION --- */
echo Html::beginTag('section', ['id' => 'leftBlock']);

    # ! PROFILE widget | @frontend/widgets/UserProfileBlock.php
    echo UserProfileBlock::widget([
      'user' => $user
    ]);

    # ! FRIENDS widget | @frontend/widgets/UserFriendsBlock.php
    echo UserFriendsBlock::widget([
      'friends' => $friends
    ]);

    # ! VIDEOS widget | @frontend/widgets/UserVideosBlock.php
    echo UserVideosBlock::widget([
      'videos' => $user->video,
      'options' => [
          'title' => Yii::t('app', 'Videos'),
          'class' => 'user__videos u-window-box--medium u-window-box--shadowed',
          'cell_class' => 'u-letter-box--medium',
      ]
    ]);

    # ! PHOTOS widget | @frontend/widgets/UserPhotosBlock.php
    echo UserPhotosBlock::widget([
      'photos' => $user->photo,
      'options' => [
          'title' => Yii::t('app', 'Photos'),
          'class' => 'user__videos u-window-box--medium u-window-box--shadowed',
          'cell_class' => 'u-letter-box--medium',
      ]
    ]);

echo Html::endTag('section');


/* ! --- RIGHT BLOCK SECTION --- */
echo Html::beginTag('section', ['id' => 'rightBlock']);

    # ! NEW BOARD POST FORM
    echo $this->render('_form', [
        'isOwner' => $isOwner,
        'boardOwnerUserId' => $boardOwnerUserId
    ]);

    # ! USER BOARD widget | @frontend/widgets/UserBoardPost.php
    echo UserBoardPost::widget([
      'posts' => $user->board
    ]);

echo Html::endTag('section');

/* ! --- OTHER ELEMENTS RELATED TO THIS PAGE --- */
echo $this->render('@modals/userVideo');
echo $this->render('@modals/userPhoto');
echo $this->render('@modals/userAttachments');

/* JS: @see js/outstyle.userboard.js */
?>
<script>jQuery(document).ready(function(){userboardInit();});</script>
