<?php
/**
 * Registration modal
 * Used in portal.php layout
 * For portal and social registration
 * Modal related stuff: /js/jquery.easyModal.js
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use frontend\models\SignupForm;

$modelSignup = new SignupForm();
$modal_id = 'register';

echo Html::beginTag('div', [
        'id' => $modal_id,
        'class' => 'modal',
        'role' => 'dialog',
        'data-modal-width' => 480
    ]
);

    $form = ActiveForm::begin([
            'id' => 'form-'.$modal_id,
            'enableAjaxValidation' => true,
            'action' => ['api/site/signup'],
            'options' => ['enctype' => 'multipart/form-data']
        ]
    );

    echo Html::tag('div',

        // modal header
        Html::tag('div',
            Html::tag('span',
                Yii::t('app', 'Registration'),
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
                Html::tag('p', Yii::t('app', 'Please fill in some details about you:')).

                $form->field($modelSignup, 'email', ['enableAjaxValidation' => true])->label('E-mail<sup>*</sup>&nbsp;').
                $form->field($modelSignup, 'password')->passwordInput()->label(Yii::t('app', 'Password').'<sup>*</sup>&nbsp;').
                $form->field($modelSignup, 'repeatPassword')->passwordInput()->label(Yii::t('app', 'Repeat password').'<sup>*</sup>&nbsp;').

                $form->field($modelSignup, 'username', [
                    'enableAjaxValidation' => true,
                    'options' => [
                        'class' => 'form-group form-separate'
                    ]
                ])->label(Yii::t('app', 'Your nickname').'<sup>*</sup>&nbsp;').

                Html::tag('p', '<i class="zmdi zmdi-info c-blue"></i> '.Yii::t('app', 'Notice that in order to verify your account and to have additional features, you will need to prove your official identity')).

                Html::tag('div', '', ['class' => 'clearfix']),

                ['class' => 'u-pillar-box--super']
            ),
            ['class' => 'modal__body']
        ).

        // modal footer
        Html::tag('div',
            Html::submitButton(
                Yii::t('app', 'Register'),
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
