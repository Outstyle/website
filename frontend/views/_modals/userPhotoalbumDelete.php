<?php
/**
 * User photoalbum delete modal (confirmation)
 * Modal related stuff: /js/jquery.easyModal.js
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Modal ID
 * @var string
 */
$modal_id = 'userphotoalbumdelete';

echo Html::beginTag('div', [
        'id' => $modal_id,
        'class' => 'modal',
        'role' => 'dialog',
        'data-modal-width' => 400,
        'data-modal-top' => 45,
    ]
);

echo Html::tag('div',

    Html::tag('div',

        # Modal header
        Html::tag('span',
            Yii::t('app', 'Delete album'),
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
            Yii::t('app', 'Do you really want to delete this album? Please notice that this action can not be reverted!')).

            # Placeholder for photoalbum name (passed via JS)
            Html::tag('h3', '').

            Html::tag('div', '', ['class' => 'clearfix']),

            ['class' => 'u-pillar-box--super']
        ),
        ['class' => 'modal__body']
    ).

    # Modal footer
    Html::tag('div',
        Html::button(
            Yii::t('app', 'Delete album'),
            [
                'id' => $modal_id.'-confirm',
                'class' => 'c-button c-button--large c-button--withrightmargin',
                'ic-target' => '#userphotoalbumdelete .modal__body',
                'ic-post-to' => Url::toRoute(['api/photoalbum/delete']),
                'ic-push-url' => 'false'
            ]
        ).
        Html::button(
            Yii::t('app', 'Cancel'),
            [
                'id' => $modal_id.'-cancel',
                'class' => 'c-button c-button--large',
                'ic-action' => 'userHidePhotoalbumDeleteModal'
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
