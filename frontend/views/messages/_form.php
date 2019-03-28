<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Message send form
 *
 * @var $this                       yii\web\View
 * @var $dialogId                   common\models\Dialog
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
**/


echo Html::beginTag('div', [
    'id' => 'messages_sendbox',
    'data-switch-layout' => 'friends_dialogs'
]);

    echo Html::beginTag('div', [
        'class' => 'o-grid o-grid--no-gutter',
    ]);

        /* ! --- LEFT AREA - 35% --- */
        echo Html::beginTag('div', [
            'id' => 'messages_actions',
            'class' => 'o-grid__cell o-grid__cell--width-35 messages__actions'
        ]);

        echo Html::endTag('div');
        

        /* ! --- RIGHT AREA - 65% --- */
        echo Html::beginTag('div', [
            'id' => 'messages_form',
            'class' => 'o-grid__cell o-grid__cell--width-65 messages__actions'
        ]);

            /* ! --- MESSAGE SEND FORM BEGIN --- */
            echo Html::beginTag('form', [
                'id' => 'message-send-form',
                'class' => 'o-grid o-grid--wrap',
                'ic-post-to' => Url::toRoute(['api/messages/add']),
                'ic-trigger-from' => '.message-send-form-trigger',
                'ic-indicator' => '#outstyle_loader',
                'ic-push-url' => 'false'
            ]);

                /* ! Message input */
                echo Html::tag('div',
                    Html::tag('div',
                        Html::tag('textarea',
                        '',
                        [
                            'id' => 'message',
                            'name' => 'message',
                            'class' => 'c-field message-send-form-trigger',
                            'maxlength' => 2048,
                            'rows' => 1,
                            'placeholder' => Yii::t('app', 'Enter your message...')
                        ]),
                    [
                        'class' => 'o-field u-letter-box--large'
                    ]),
                [
                    'class' => 'o-grid__cell o-grid__cell--width-85 message__input'
                ]);

                /* ! Message send button */
                echo Html::tag('div',
                    Html::tag('div',
                        '<div class="u-center-block"><div class="u-center-block__content">'.
                            Html::button('<i class="zmdi zmdi-chevron-right zmdi-hc-2x"></i>', [
                                'id' => 'message-send-submit',
                                'class' => 'zmdi-icon--hoverable message-send-form-trigger',
                                'ic-trigger-on' => 'click',
                            ]).
                        '</div></div>',
                    [
                        'class' => 'u-letter-box--large'
                    ]),
                [
                    'class' => 'o-grid__cell o-grid__cell--width-15 message__send'
                ]);

            echo Html::endTag('form');
        echo Html::endTag('div');
    echo Html::endTag('div');
echo Html::endTag('div');
