<?php
/**
 * Main SOCIAL layout: AJAX
 * Questions? Feel free to ask: <scsmash3r@gmail.com> / skype: smash3rs / Telegram: @scsmash3r
 * Github: https://github.com/scsmash3r
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Spaceless;

Spaceless::begin();

# Main content AJAX (no headers, no body, content only)
echo Html::tag('main',
  $content,
  [
    'id' => 'content',
    'ic-history-elt' => '',
    'class' => 'content u-window-box--small'
  ]
);

Spaceless::end();
