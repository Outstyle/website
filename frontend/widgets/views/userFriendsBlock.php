<?php
/**
 * User friends block view
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


echo Html::beginTag('div', ['class' => 'u-window-box--medium u-window-box--shadowed user__friends']);

  # Widget settings button
  echo ElementsHelper::widgetButton('settings');

  # Widget title
  echo Html::tag('h4', Yii::t('app', 'Friends'));

  # Working with friends model (grid)
  echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter u-letter-box--medium']);


  /* If user have no friends at all */
  if (!$friends['active']) {
      echo Html::tag('div',
       Html::tag('div', '<i class="zmdi zmdi-alert-triangle c-blue"></i> '.Yii::t('app', 'This user has no friends'), [
         'class' => 'u-letter-box--super'
       ]),
       ['class' => 'user__friends--notfound o-grid__cell']);
  } else {
      foreach ($friends['active'] as $friend) {
          echo Html::tag('div',

            # Friend image
            ElementsHelper::linkElement('friend', Html::img($friend['avatar'], [
              'class' => "o-image roundborder friend__avatar friend__avatar--medium"
            ]), Url::to(['/id'.$friend['id']], true), false, $friend['name']).

            # Friend name
            ElementsHelper::linkElement('friend', $friend['name'], Url::to(['/id'.$friend['id']], true)),

            [
              'class' => 'o-grid__cell--width-33 u-window-box--small friend',
            ]
          );
      }
  }


  echo Html::endTag('div');

echo Html::endTag('div');

# SEPARATOR
echo ElementsHelper::separatorWidget(2, 'bottomborder');
