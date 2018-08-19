<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use common\components\classes\Multiplayer;
use frontend\models\VideoServices;

/**
 * Video container
 *
 * Using:
 * - Multiplayer: https://github.com/felixgirault/multiplayer
 *
 * @var $this               yii\web\View
 * @var $video      array   @views/video/_videosingle
 * @var $options    array   @views/video/_videosingle
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
*/

$Multiplayer = new Multiplayer();

echo Html::tag('div',

  # Dynamic video container
  Html::tag('div',
    $Multiplayer->html(
      VideoServices::generateServiceLink($video['video_id'], $video['service_id']),
      $options
    ),
    [
      'class' => 'video__multicontainer'
    ]
  ),

  [
    'class' => 'o-grid__cell--width-100'
  ]
);
