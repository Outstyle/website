<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\components\helpers\ElementsHelper;
use common\components\helpers\html\LoadersHelper;

/**
 * Exit from dialog confirmation window
 * @see /js/jquery.easyModal.js
 *
 * @var $this                       yii\web\View
 * @var $dialog                     common\models\Dialog
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
**/

$modal_id = 'dialoguserleave';

echo Html::beginTag('div', [
    'id' => $modal_id,
    'class' => 'modal',
    'role' => 'dialog',
    'data-modal-width' => 380,
    'data-modal-top' => 45,
]);

echo Html::tag('div',

    Html::tag('div',

        # Modal header
        Html::tag('span',
            Yii::t('app', 'Leave conversation?'),
            ['class' => 'modal__caption c-text--shadow']
        ).

        # Modal button close
        Html::button('<i class="zmdi zmdi-close"></i>',
            [
                'class' => 'c-button c-button--close modal-close',
                'title' => Yii::t('app', 'Close')
            ]
        ),

        ['class' => 'modal__header modal__header--branded modal__header--warning u-window-box--medium']
    ).

    # Modal body
    Html::tag('div',
        Html::tag('div',

            Html::tag('div', '', ['class' => 'clearfix']).

            Html::tag('p',
            '<i class="zmdi zmdi-alert-triangle c-red"></i>&nbsp;'.
            Yii::t('app', 'Please confirm your action:'),
                [
                    'class' => 'u-c'
                ]
            ).

            Html::tag('div', '', ['class' => 'clearfix']),

            ['class' => 'u-pillar-box--super']
        ),
        ['class' => 'modal__body']
    ).

    # Modal footer
    Html::tag('div',
        Html::button(
            Yii::t('app', 'Leave this conversation'),
            [
                'id' => $modal_id.'-confirm',
                'class' => 'c-button c-button--large c-button--withrightmargin',
                'ic-target' => '',
                'ic-post-to' => Url::toRoute(['api/0']),
                'ic-push-url' => 'false'
            ]
        ).
        Html::button(
            Yii::t('app', 'Back'),
            [
                'id' => $modal_id.'-cancel',
                'class' => 'c-button c-button--large modal-close'
            ]
        ),
        ['class' => 'modal__footer modal__footer--centered u-window-box--medium']
    ),

    ['class' => 'modal__content']
);

echo Html::endTag('div');

/* JS: @see js/outstyle.modal.js */
?>
<script>jQuery(document).ready(function(){modalInit('#<?=$modal_id;?>');});</script>
