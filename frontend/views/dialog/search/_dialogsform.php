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


/* ! --- DIALOGS SEARCH FORM + EVENTS --- */
echo Html::beginTag('form', [
    'class' => 'o-grid o-grid--wrap',
    'id' => 'dialogs_search',
    'data-switch-layout' => 'friends_dialogs',
    'autocomplete' => 'off',
    'ic-post-to' => Url::toRoute(['api/dialog/list']),
    'ic-indicator' => '#outstyle_loader',
    'ic-trigger-from' => '.dialogs_search_trigger',
    'ic-target' => '#conversations_area',
    'ic-select-from-response' => '#dialogsList',
    'ic-push-url' => 'false'
]).

    /* ! --- DIALOGS SEARCH FORM INPUT - 90% --- */
    Html::tag('div',
        Html::tag('div',
            Html::input('text', 'search', '', [
                'class' => 'c-field c-field--rounded dialogs_search_trigger',
                'autocomplete' => 'off',
                'maxlength' => 64,
                'placeholder' => Yii::t('app', 'Search in dialogs...'),
                'ic-trigger-on' => 'change'
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
        'class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-10 dialogs__add search-mode-switch',
        'ic-action' => 'trigger:dialogsSearchModeSwitch',
        'ic-trigger-on' => 'click',
        'ic-trigger-from' => '.search-mode-switch'
    ]).

Html::endTag('form');
