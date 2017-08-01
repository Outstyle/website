<?php
/**
 * User videos block view
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
/* @var $videos */

echo Html::beginTag('div', ['class' => 'user__videos u-window-box--medium u-window-box--shadowed']);

  # Widget settings button
  echo ElementsHelper::widgetButton();

  # Widget title
  echo Html::tag('h4', Yii::t('app', 'Videos'));


  foreach ($videos as $video) {
      echo Html::tag('div',

        # Video image
        ElementsHelper::videoLink($video['hash'], Html::img($video['video_img'], ['class' => 'o-image u-full-width user__videothumbnail'])).

        # Video title and link
        ElementsHelper::videoLink($video['hash'], $video['video_title']).

        # Video date and provider
        Html::tag('div',

          Yii::t('app', '{video_date} via {video_provider}', [
            'video_date' => Yii::$app->formatter->asDateTime(strtotime($video['created_at']), Yii::$app->params['date']),
            'video_provider' => $video['service_id'],
          ]),

          [
            'class' => 'user__videodate'
          ]
        ),

        [
          'class' => 'u-letter-box--medium user__video',
        ]
      );
  }

echo Html::endTag('div');
