<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Create new dialog assets (bottom panel in messages section)
 *
 * @var $this                       yii\web\View
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
**/

/* 100% GRID START --- */
echo Html::beginTag('div', [
    'id' => 'dialog_createnew',
    'class' => 'o-grid o-grid--no-gutter u-i',
    'data-switch-layout' => 'friends_dialogs'
]);

    /* ! --- LEFT AREA - 35% --- */
    echo Html::beginTag('div', [
        'id' => 'dialog_actions',
        'class' => 'o-grid__cell o-grid__cell--width-35 dialog__actions'
    ]);

        /* ! Centered button: create dialog */
        echo
        Html::tag('div',
            Html::tag('div',
                Html::button(
                    Yii::t('app', 'Create new dialog'),
                    [
                      'id' => 'dialog-create-new',
                      'class' => 'c-button c-button--default u-small',
                      'title' => Yii::t('app', 'Delete album'),
                      'ic-action' => 'trigger:createNewDialog'
                    ]
                ),
            [
             'class' => 'u-center-block__content'
            ]),
        [
            'class' => 'u-center-block u-fh'
        ]);

    echo Html::endTag('div');

    /* ! --- RIGHT AREA - 65% --- */
    echo Html::beginTag('div', [
        'class' => 'o-grid__cell o-grid__cell--width-65 dialog__actions'
    ]);

    echo Html::endTag('div');

echo Html::endTag('div'); /* 100% GRID END --- */
