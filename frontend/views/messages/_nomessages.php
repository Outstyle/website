<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

use yii\widgets\Spaceless;

use common\components\helpers\ElementsHelper;
use common\components\helpers\StringHelper;

/**
 * User messages list: no messages at all - meaning we're on messages main page
 *
 * @var $this                       yii\web\View
 *
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
**/

echo Html::tag('div',
    '<div class="u-center-block__content"><i class="zmdi zmdi-comment-more zmdi-hc-5x"></i><br>'.
        \Yii::t('app', 'Please choose a dialogue or {dialogue_action_new}', [
        'dialogue_action_new' => ElementsHelper::linkElement(
            'newdialogue',
            \Yii::t('app', 'click here to create a new dialogue')),
        ]).
    '</div>',
[
    'class' => 'u-center-block u-c conversations__new'
]);
