<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Dialogs / conversations search form
 *
 * @var $this         yii\web\View
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */


echo
Html::tag('div',

    /* ! --- DIALOGS SEARCH FORM INPUT - 90% --- */
    Html::tag('div',
        Html::tag('div',
            Html::input('text', 'search', '', [
                'class' => 'c-field c-field--rounded',
                'autocomplete' => 'off',
                'maxlength' => 64,
                'placeholder' => Yii::t('app', 'Search in dialogs...'),
            ]).
            Html::tag('i', '', ['class' => 'zmdi zmdi-search zmdi-hc-lg c-icon']),
        [
            'class' => 'o-field o-field--icon-right'
        ]),
    [
        'class' => 'o-grid__cell o-grid__cell--width-90 dialogs__search'
    ]).

    /* ! --- ADD NEW USER TO DIALOGUE ICON - 10% --- */
    Html::tag('div',
        Html::tag('div',
            Html::tag('div',
                Html::tag('i', '', [
                    'class' => 'zmdi zmdi-plus zmdi-hc-2x c-icon',
                    'title' => Yii::t('app', 'Create new dialogue...')
                ]),
            [
                'class' => 'u-center-block__content'
            ]),
        [
            'class' => 'u-center-block u-fh'
        ]),
    [
        'class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-10 dialogs__add',
        'ic-action' => "trigger:dialogsSearchModeSwitch"
    ]),

[
    'class' => 'o-grid o-grid--wrap',
    'id' => 'dialogs_search',
    'data-switch-layout' => 'friends_dialogs'
]);
