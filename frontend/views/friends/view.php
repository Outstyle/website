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

/**
 * Friends list
 *
 * @var $this                 yii\web\View
 * @var $friends              @frontend/models/Friend
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */

echo Html::tag('div',
  LoadersHelper::loaderImage('breakdance', 'friends__loader'), [
  'id' => 'friends__loader'
]);

# FRIENDS widget | @frontend/widgets/UserFriendsBlock.php
echo UserFriendsBlock::widget([
  'friends' => $friends,
  'options' => [
    'view' => 'userFriendsSearch'
  ]
]);
