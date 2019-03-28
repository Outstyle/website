<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Users (in dialogs) search form
 *
 * @var $this         yii\web\View
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */

/* ! --- USERS IN DIALOGS SEARCH FORM + EVENTS --- */
echo Html::beginTag('form', [
    'id' => 'friends_in_dialogs_search',
    'class' => 'o-grid o-grid--wrap u-i',
    'data-switch-layout' => 'friends_dialogs',
    'autocomplete' => 'off',
    'ic-post-to' => Url::toRoute(['api/friends/filter']),
    'ic-indicator' => '#outstyle_loader',
    'ic-trigger-from' => '.friends_in_dialogs_search_trigger',
    'ic-target' => '#friends_in_dialogs_area .os-content',
    'ic-select-from-response' => '#friendsList',
    'ic-push-url' => 'false'
]).

    /* ! --- USERS SEARCH FORM INPUT - 90% --- */
    Html::tag('div',
        Html::tag('div',
            Html::input('text', 'search', '', [
                'class' => 'c-field c-field--rounded friends_in_dialogs_search_trigger',
                'autocomplete' => 'off',
                'maxlength' => 64,
                'placeholder' => Yii::t('app', 'Search friends...'),
                'ic-trigger-on' => 'change',
            ]).
            Html::tag('i', '', ['class' => 'zmdi zmdi-search zmdi-hc-lg c-icon']),
        [
            'class' => 'o-field o-field--icon-right'
        ]),
    [
        'class' => 'o-grid__cell o-grid__cell--width-90 friends__search'
    ]).

    /* ! --- CLOSE ICON - 10% --- */
    Html::tag('div',
        Html::tag('div',
            Html::tag('div',
                Html::tag('i', '', [
                    'class' => 'zmdi zmdi-close zmdi-hc-2x c-icon',
                    'title' => Yii::t('app', 'Close friends search')
                ]),
            [
             'class' => 'u-center-block__content'
            ]),
        [
            'class' => 'u-center-block u-fh'
        ]),
    [
        'class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-10 dialogs__close',
        'ic-action' => 'trigger:dialogsSearchModeSwitch',
    ]).

Html::endTag('form');
