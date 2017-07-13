<?php
/**
 * User single video preview modal
 * Modal related stuff: /js/jquery.easyModal.js
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$modal_id = 'uservideo';

echo Html::beginTag('div', [
        'id' => $modal_id,
        'class' => 'modal',
        'role' => 'dialog',
        'data-modal-width' => 760,
        'data-modal-height' => 500,
        'data-modal-top' => 45,
    ]
);

echo Html::tag('div',

    Html::tag('div',

        # Modal header
        Html::tag('span',
            Yii::t('app', 'Просмотр видео'),
            ['class' => 'modal__caption c-text--shadow']
        ).

        # Modal button close
        Html::button('<i class="zmdi zmdi-close"></i>',
            [
                'class' => 'c-button c-button--close modal-close',
                'title' => Yii::t('app', 'Close')
            ]
        ),

        ['class' => 'modal__header modal__header--branded u-window-box--medium']
    ).

    # Modal body
    Html::tag('div',

        Html::tag('div',
            Html::tag('div', '', ['class' => 'clearfix']).

            # Modal main body text
            '...'.

            Html::tag('div', '', ['class' => 'clearfix']),

            ['class' => 'modal__iframe']
        ),
        ['class' => 'modal__body']
    ),

    ['class' => 'modal__content']
);

echo Html::endTag('div');

// Reinit is needed for history.back stuff
?>
<script>jQuery(document).ready(function(){modalInit();});</script>
