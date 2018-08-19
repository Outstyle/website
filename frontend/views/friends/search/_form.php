<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use common\components\helpers\ElementsHelper;
use common\components\helpers\PrivacyHelper;

use frontend\models\UserDescription;

/**
 * Friends search form
 *
 * Form has two states, based on routes:
 * 'filter' - when it's just filtering existing friends of user
 * 'find' - when it's actually a request for searching from all users pool
 *
 * @var $this         yii\web\View
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */

$form_ajax_action = 'filter';
if (Yii::$app->request->pathInfo == 'friends/search') {
    $form_ajax_action = 'find';
}

echo Html::beginTag('form', [
  'id' => 'friends-search-form',
  'class' => 'o-grid o-grid--wrap',
  'ic-post-to' => Url::toRoute(['api/friends/'.$form_ajax_action]),
  'ic-trigger-from' => '.form-trigger',
  'ic-indicator' => '#friends__loader',
  'ic-target' => '#rightBlock',
  'ic-trigger-delay' => '200ms',
  'ic-on-beforeSend' => 'friendsBeforeSearchActions()',
  'ic-on-complete' => 'friendsAfterSearchActions()'
]);

  echo Html::tag('div',

    /* Full search field with icon */
    Html::tag('div',
      Html::input('text', 'search', '', [
        'id' => 'friends-search',
        'class' => 'c-field c-field--rounded form-trigger',
        'maxlength' => 64,
        'ic-trigger-on' => 'focusout changed',
      ]).
      Html::tag('i', '', ['class' => 'zmdi zmdi-search zmdi-hc-lg c-icon']),
    [
      'class' => 'o-field o-field--icon-right'
    ]),

  [
    'class' => 'o-grid__cell o-grid__cell--width-100 friends__search'
  ]);


  echo Html::tag('div', '',
  [
    'class' => 'o-grid__cell o-grid__cell--width-20 friends__activebuttons'
  ]);

      /* All friends */
      echo Html::tag('div',
        ElementsHelper::linkElement('roundbutton',
          Html::tag('i', '', ['class' => 'zmdi zmdi-accounts-alt zmdi-hc-2x c-icon']),
        Url::to(['/friends'], true), '', Yii::t('app', 'All friends')),
      [
        'id' => 'friends__roundbutton-all',
        'class' => 'o-grid__cell o-grid__cell--width-20 friends__activebuttons'
      ]);

      /* Friends online */
      echo Html::tag('div',
        ElementsHelper::linkElement('roundbutton',

          Html::tag('span',
            Html::tag('i', '', ['class' => 'zmdi zmdi-accounts-alt zmdi-hc-lg']).
            Html::tag('i', '', ['class' => 'zmdi zmdi-circle zmdi-hc-stack-1x zmdi-hc-sided c-text__color--lightgreen']),
          [
            'class' => 'zmdi-hc-stack'
          ]),

        Url::to(['/friends/online'], true), '', Yii::t('app', 'Friends online')),
      [
        'id' => 'friends__roundbutton-online',
        'class' => 'o-grid__cell o-grid__cell--width-20 friends__activebuttons'
      ]);

      /* Search friends */
      echo Html::tag('div',
        ElementsHelper::linkElement('roundbutton',
          Html::tag('i', '', ['class' => 'zmdi zmdi-accounts-add zmdi-hc-2x c-icon']),
        Url::to(['/friends/search'], true), '', Yii::t('app', 'Search friends')),
      [
        'id' => 'friends__roundbutton-search',
        'class' => 'o-grid__cell o-grid__cell--width-20 friends__activebuttons'
      ]);


  echo Html::tag('div', '',
  [
    'class' => 'o-grid__cell o-grid__cell--width-20 friends__activebuttons'
  ]);


  echo Html::tag('div',
    Html::tag('div',

      /* Sorting dropdown */
      Html::dropDownList('sort_by',
        null,
        [
          'id' => Yii::t('app', 'Sort by ID'),
          'rating' => Yii::t('app', 'Sort by rating')
        ],
      [
        'class' => 'form-trigger select--mini select--darker',
        'ic-trigger-on' => 'change',
      ]),

    [
      'class' => 'u-letter-box--medium'
    ]),
  [
    'class' => 'o-grid__cell o-grid__cell--width-100 o-grid__cell--lighterbg'
  ]);


  echo ElementsHelper::separatorDiamond(Yii::t('app', 'Country and city'), 'small');
  echo Html::tag('div', $this->render('@common/views/geolocation/_filterblock'),
  ['class' => 'o-grid__cell o-grid__cell--width-100']);


  echo ElementsHelper::separatorDiamond(Yii::t('app', 'Age'), 'small');

  echo Html::tag('div',
    Html::tag('div',

      /* Age min */
      Html::input('number', 'age_min', '', [
        'class' => 'c-field c-field--sharpborder form-trigger',
        'maxlength' => 2,
        'min' => 0,
        'placeholder' => Yii::t('app', 'from...'),
        'ic-trigger-on' => 'focusout changed',
      ]),

    [
      'class' => 'form-group form-group--marginbottom'
    ]),
  [
    'class' => 'o-grid__cell o-grid__cell--width-45 u-prn'
  ]);

  echo Html::tag('div',
    Html::tag('div', '-', ['class' => 'form-group form-group--marginbottom']),
  [
    'class' => 'o-grid__cell o-grid__cell--width-10 o-grid__cell--no-gutter'
  ]);

  echo Html::tag('div',
    Html::tag('div',

      /* Age max */
      Html::input('number', 'age_max', '', [
        'class' => 'c-field c-field--sharpborder form-trigger',
        'maxlength' => 3,
        'min' => 0,
        'placeholder' => Yii::t('app', 'to...'),
        'ic-trigger-on' => 'focusout changed',
      ]),

    [
      'class' => 'form-group form-group--marginbottom'
    ]),
  [
    'class' => 'o-grid__cell o-grid__cell--width-45 u-pln'
  ]);

  echo ElementsHelper::separatorDiamond(Yii::t('app', 'Sex'), 'small');

  echo Html::tag('div',
    Html::tag('div',

      Html::tag('label',
        Html::input('checkbox', 'sex[]', 'male', [
          'class' => 'form-trigger',
          'ic-trigger-on' => 'click',
        ]).
        '<span>'.Yii::t('app', 'Male').'</span><br>').

      Html::tag('label',
        Html::input('checkbox', 'sex[]', 'female', [
          'class' => 'form-trigger',
          'ic-trigger-on' => 'click',
        ]).
        '<span>'.Yii::t('app', 'Female').'</span>'),

    [
      'class' => 'form-group form-group--marginbottom u-l'
    ]),
  [
    'class' => 'o-grid__cell o-grid__cell--width-50'
  ]);

  /* Additional fields for friends/search route */
  if (Yii::$app->request->pathInfo == 'friends/search') {
      echo Html::tag('div',
        Html::tag('div',

          Html::tag('label',
            Html::input('checkbox', 'has_photo', 'male', [
              'class' => 'form-trigger',
              'ic-trigger-on' => 'click',
            ]).
            '<span>'.Yii::t('app', 'Has photo').'</span><br>').

          Html::tag('label',
            Html::input('checkbox', 'is_online', 'female', [
              'class' => 'form-trigger',
              'ic-trigger-on' => 'click',
            ]).
            '<span>'.Yii::t('app', 'Online now').'</span>'),

        [
          'class' => 'form-group form-group--marginbottom u-l'
        ]),
      [
        'class' => 'o-grid__cell o-grid__cell--width-50'
      ]);
  }

  echo ElementsHelper::separatorDiamond(Yii::t('app', 'Who is in culture'), 'small');

  echo Html::tag('div',

      Html::dropDownList('culture',
        null,
        UserDescription::cultureList(),
      [
        'class' => 'form-trigger form-group--marginbottom',
        'ic-trigger-on' => 'change',
      ]),

  [
    'class' => 'o-grid__cell o-grid__cell--width-100'
  ]);

echo Html::endTag('form');

/* JS: @see js/outstyle.user.friends.js */
?>
<script>jQuery(document).ready(function(){friendsSearchFormInit()});</script>
