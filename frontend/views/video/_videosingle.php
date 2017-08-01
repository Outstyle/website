<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\ElementsHelper;
use frontend\widgets\WidgetComments;

/* $video   array   @views/video/view */

echo Html::tag('div',

  # Video container using Multiplayer: https://github.com/felixgirault/multiplayer
  $this->render('_videocontainer', [
    'video' => $video ?? '',
    'options' => $options ?? []
  ]),

[
  'class' => 'o-grid__cell o-grid__cell--width-100'
]);

# Video header
echo Html::tag('div',
  Html::tag('div',

    # Video H1, date and provider container
    Html::tag('div',

      # Video H1
      Html::tag('h1',
        $video['video_title'],
        ['class' => 'video__title']
      ).

      # Video date and provider
      Html::tag('div',
        Yii::t('app', '{video_date} via {video_provider}', [
          'video_date' => Yii::$app->formatter->asDateTime(strtotime($video['created_at']), Yii::$app->params['date']),
          'video_provider' => Html::a($video['service_id'], ['/away?to='.$video['service_link']], ['target' => '_blank']),
        ]),
        ['class' => 'video__provider']
      ),

      [
        'class' => 'o-grid__cell--width-75'
      ]
    ).

    # Video share and like container
    Html::tag('div',

      # Actions box (like, view count, comment buttons)
      Html::tag('div',
        Html::tag('div',
          ElementsHelper::linkElement('views', 0, false, 'zmdi-eye').
          ElementsHelper::linkElement('comment', 0, Url::toRoute('/'), 'zmdi-comment-text-alt').
          ElementsHelper::likeButton(Yii::$app->controller->id, $video['id'], 0, 0).
          ElementsHelper::linkElement('repost', '', '#', 'icon-megaphone'),
          ['class' => 'video__actions u-center-block__content']
        ),
        ['class' => 'video__actionbox u-center-block']
      ),

      [
        'class' => 'o-grid__cell--width-25'
      ]
    ),

  [
    'class' => 'o-grid o-grid--no-gutter u-window-box--medium color-content--bg video__header'
  ]),

[
  'class' => 'o-grid__cell o-grid__cell--width-100'
]);

# Comments
echo WidgetComments::widget([
  'elem_id' => $video['id']
]);
