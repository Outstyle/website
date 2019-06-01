<?php
/**
 * Comments form (used at particular single pages)
 * Questions? Feel free to ask: <scsmash3r@gmail.com> or skype: smash3rs
 * Also used by widgets/WidgetComments.php
 */

use yii\helpers\Url;
use yii\helpers\Html;

use frontend\models\UserAvatar;
use common\components\helpers\html\CommentsHelper;
use common\components\helpers\html\AttachmentsHelper;

/* @var $modelElemId */
/* TODO: Make this one form (comments) */
  echo Html::tag('div',

    Html::tag('div',

      '<i class="zmdi zmdi-comment-text zmdi-hc-2x"></i>',

      [
        'class' => 'o-grid__cell o-grid__cell--top o-grid__cell--width-10 u-r post_add__avatar u-letter-box--large'
      ]
    ).

    /* ! --- RIGHT AREA - 90% --- */
    Html::beginTag('div', [
        'id' => 'board_post_form',
        'class' => 'o-grid__cell o-grid__cell--width-90 o-grid__cell--no-gutter post__actions'
    ]).

        /* ! --- MESSAGE SEND FORM BEGIN --- */
         Html::beginTag('form', [
            'id' => 'board-post-form',
            'class' => 'o-grid o-grid--wrap',
            'ic-post-to' => Url::toRoute(['api/board/addpost']),
            'ic-trigger-from' => '.text-send-form-trigger',
            'ic-indicator' => '#outstyle_loader',
            'ic-include' => '{"owner":'.(int)$boardOwnerUserId.'}',
            'ic-push-url' => 'false'
        ]).

            /* ! Message input */
            Html::tag('div',
                Html::tag('div',
                    Html::tag('textarea',
                    '',
                    [
                        'id' => 'text',
                        'name' => 'text',
                        'class' => 'c-field text-send-form-trigger',
                        'maxlength' => 65535,
                        'rows' => 1,
                        'placeholder' => Yii::t('app', 'Write your board post...')
                    ]),
                [
                    'class' => 'o-field u-letter-box--large'
                ]),
            [
                'class' => 'o-grid__cell o-grid__cell--width-85 message__input'
            ]).

            /* ! Message send button */
            Html::tag('div',
                Html::tag('div',
                    '<div class="u-center-block"><div class="u-center-block__content">'.
                        Html::button('<i class="zmdi zmdi-chevron-right zmdi-hc-2x"></i>', [
                            'id' => 'text-send-submit',
                            'class' => 'zmdi-icon--hoverable text-send-form-trigger',
                            'ic-trigger-on' => 'click',
                        ]).
                    '</div></div>',
                [
                    'class' => 'u-letter-box--large'
                ]),
            [
                'class' => 'o-grid__cell o-grid__cell--width-15 message__send'
            ]).

        Html::endTag('form'),
    [
      'class' => 'o-grid o-grid--wrap o-grid--center u-letter-box--large post_add',
    ]
);
