<?php

use yii\helpers\Html;
use common\components\classes\Multiplayer;

/* $video   array   @views/video/_videosingle */

# Multiplayer: https://github.com/felixgirault/multiplayer
$Multiplayer = new Multiplayer();

echo Html::tag('div',

  # Dynamic video container
  Html::tag('div',
    $Multiplayer->html($video['service_link'], $options),
    [
      'class' => 'video__multicontainer'
    ]
  ),

  [
    'class' => 'o-grid__cell--width-100'
  ]
);
