<?php
/**
 * User friends search block list representation
 * Part of Outstyle network
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @version 1.0
 *
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */

use yii\helpers\Html;
use common\components\helpers\html\TooltipsHelper;

/**
 * @var array   $friends    @see: @frontend/widgets/UserFriendsBlock.php
 */

$page = Yii::$app->request->post('page');

/* Working with friends model (grid), if atleast one user is found */
echo Html::beginTag('div', ['id' => 'friendsList']);
  echo Html::beginTag('div', ['class' => 'search__friends search__friends--notfound']);
    echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter']);

    /* If no users are found at all */
    if (!$page) {
        echo Html::tag('div',
        Html::tag('div',
          '<i class="zmdi zmdi-alert-triangle c-blue"></i> '.
          Yii::t('app', 'No users found matching your criteria...')),
        ['class' => 'o-grid__cell message--alert']
      );
    /* If no users are found cause it is the end of pagination*/
    } else {
        echo Html::tag('div',
        Html::tag('div',
          '<i class="zmdi zmdi-info c-green"></i> '.
          Yii::t('app', 'End of users list reached')),
        ['class' => 'o-grid__cell message--alert']
      );
    }

    echo Html::endTag('div');
  echo Html::endTag('div');
echo Html::endTag('div');
