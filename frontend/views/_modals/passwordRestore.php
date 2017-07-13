<?php
/**
 * User restore password modal
 * Used in portal.php layout
 * Modal related stuff: /js/jquery.easyModal.js
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use frontend\models\PasswordResetRequestForm;

$model = new PasswordResetRequestForm();
$modal_id = 'passwordrestore';

echo Html::beginTag('div', [
        'id' => $modal_id,
        'class' => 'modal',
        'role' => 'dialog',
        'data-modal-width' => 340
    ]
);

    $form = ActiveForm::begin([
            'id' => 'form-'.$modal_id,
            'enableAjaxValidation' => true,
            'action' => ['api/site/passwordrestore'],
            'options' => ['enctype' => 'multipart/form-data']
        ]
    );

    echo Html::tag('div',

        // modal header
        Html::tag('div',
            Html::tag('span',
                Yii::t('app', 'Password restore'),
                ['class' => 'modal__caption c-text--shadow']
            ).
            // modal button close
            Html::button('<i class="zmdi zmdi-close"></i>',
                [
                    'class' => 'c-button c-button--close modal-close',
                    'title' => Yii::t('app', 'Close')
                ]
            ),
            ['class' => 'modal__header modal__header--branded u-window-box--medium']
        ).

        // modal body
        Html::tag('div',
            Html::tag('div',

                Html::tag('div', '', ['class' => 'clearfix']).

                // modal main body text
                Html::tag('p', Yii::t('app', 'Enter your registration email:')).
                $form->field($model, 'email')->label('E-mail<sup>*</sup>&nbsp;').
                Html::tag('p', Yii::t('app', 'You will receive a message with your new temporary password, that you will be able to change in settings.')).

                Html::tag('div', '', ['class' => 'clearfix']),

                ['class' => 'u-pillar-box--super']
            ),
            ['class' => 'modal__body']
        ).

        // modal footer
        Html::tag('div',
            Html::submitButton(
                Yii::t('app', 'Send a password'),
                [
                    'id' => $modal_id.'-submit',
                    'class' => 'c-button c-button--large'
                ]
            ),
            ['class' => 'modal__footer modal__footer--centered u-window-box--medium']
        ),

        ['class' => 'modal__content']
    );

    ActiveForm::end();
echo Html::endTag('div');

// Reinit is needed for history.back stuff
?>
<script>jQuery(document).ready(function(){modalInit();});</script>
