<?php
/**
 * User friends search block representation
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
use yii\helpers\Url;
use common\components\helpers\ElementsHelper;

/**
 * @var array   $friends    @see: @frontend/widgets/UserFriendsBlock.php
 */

/* If no users are found at all */
if (!$friends) {
    echo Html::tag('div',
      Html::tag('div', '<i class="zmdi zmdi-alert-triangle c-blue"></i> '.Yii::t('app', 'No users found matching your criteria...')),
      [
        'class' => 'search__friends search__friends--notfound'
      ]
    );
    return;
}

/* Working with friends model (grid), if atleast one user is found */
echo Html::beginTag('div', ['class' => 'search__friends']);
  echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter']);

      foreach ($friends as $friend) {
          echo Html::tag('div',

            # Friend image
            ElementsHelper::linkElement('friend',
              Html::img($friend['avatar'], ['class' => 'o-image roundborder friend__avatar friend__avatar--small']),
            Url::to(['/id'.$friend['id']], true), false, $friend['fullname']).

            # Additional friend info block
            Html::tag('div',

              ElementsHelper::linkElement('friend', $friend['fullname'], Url::to(['/id'.$friend['id']], true)).
              '<br>'.$friend['location'].
              '<br>'.$friend['birthday_date'],

              [
                'class' => 'friend__info',
              ]
            ),

            [
              'class' => 'o-grid__cell--width-100 u-window-box--small friend__box',
            ]
          );
      }

  echo Html::endTag('div');
echo Html::endTag('div');
