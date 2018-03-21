<?php
/**
 * User photoalbum create form modal
 * Modal related stuff: /js/jquery.easyModal.js
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use common\components\helpers\html\LoadersHelper;

/**
 * Modal ID
 * @var string
 */
$modal_id = 'userphotoalbumcreate';

echo Html::beginTag('div', [
        'id' => $modal_id,
        'class' => 'modal',
        'role' => 'dialog',
        'data-modal-width' => 540,
        'data-modal-top' => 45,
    ]
);

echo Html::tag('div',

    Html::tag('div',

        # Modal header
        Html::tag('span',
            Yii::t('app', 'New album'),
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

    LoadersHelper::loaderImage('breakdance', 'modal__loader').

    # Modal body
    Html::tag('div',
        '',
        ['class' => 'modal__body']
    ),


    ['class' => 'modal__content']
);

echo Html::endTag('div');

/* JS: @see js/outstyle.modal.js */
?>
<script>jQuery(document).ready(function(){modalInit('#<?=$modal_id;?>');});</script>
