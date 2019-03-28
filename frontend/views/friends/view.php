<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

use frontend\widgets\UserFriendsBlock;

use common\components\helpers\ElementsHelper;
use common\components\helpers\SEOHelper;
use common\components\helpers\html\LoadersHelper;
use common\components\helpers\html\TooltipsHelper;

/**
 * Friends list
 *
 * @var $this                 yii\web\View
 * @var $friends              @frontend/models/Friend
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */

/* FRIENDS widget | @frontend/widgets/UserFriendsBlock.php */
echo UserFriendsBlock::widget([
  'friends' => $friends,
  'options' => [
    'view' => 'userFriendsSearch'
  ]
]);


/* Initial loader to fire up AJAX request for friends, except online */
echo Html::tag('div',
  LoadersHelper::loaderImage('breakdance', 'friends__loader'), [
  'id' => 'friends__loader',
  'ic-action' => 'trigger:loadMoreFriends',
  'ic-trigger-on' => 'scrolled-into-view'
]);

/* Additional fields for AJAX requests here */
echo Html::hiddenInput('friendId', '', ['id' => 'friendId']);
echo Html::hiddenInput('page', 0, ['id' => 'page']);

echo TooltipsHelper::tooltipContainerForFriends();

/* JS: @see js/outstyle.user.friends.js */
?>
<script>jQuery(document).ready(function(){friendsInit()});</script>
