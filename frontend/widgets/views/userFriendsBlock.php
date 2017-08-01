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

/* @see @frontend/widgets/UserVideosBlock for vars */
/* @var $friends */

echo Html::beginTag('div', ['class' => 'u-window-box--medium u-window-box--shadowed user__friends']);

  # Widget settings button
  echo ElementsHelper::widgetButton('settings');

  # Widget title
  echo Html::tag('h4', Yii::t('app', 'Friends'));

  # Working with friends model (grid)
  echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter u-letter-box--medium']);

    foreach ($friends as $id => $friend) {
        echo Html::tag('div',

          # Friend image
          ElementsHelper::linkElement('friend', Html::img($friend['friendAvatarPath'], [
            'class' => "o-image roundborder friend__avatar"
          ]), Url::to(['/id'.$id], true), false, $friend['name']).

          # Friend name
          ElementsHelper::linkElement('friend', $friend['name'], Url::to(['/id'.$id], true)),

          [
            'class' => 'o-grid__cell--width-33 u-window-box--small friend',
          ]
        );
    }

  echo Html::endTag('div');


echo Html::endTag('div');

# SEPARATOR
echo ElementsHelper::separatorWidget(2, 'bottomborder');
