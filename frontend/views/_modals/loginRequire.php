<?php
/**
 * Modal, asking for login
 * Used in portal.php layout
 * Modal related stuff: /js/jquery.easyModal.js
 */

use yii\helpers\Html;

$modal_id = 'loginRequire';

?>
	<div class="modal" id="<?=$modal_id;?>" role="dialog" data-modal-width="500" data-input-width="200">
		<div class="modal__content">
            <div class="modal__header modal__header--branded u-window-box--medium">
                <span class="modal__caption c-text--shadow"><?=Yii::t('app', 'Войдите или зарегестрируйтесь');?></span>
                <button type="button" class="c-button c-button--close modal-close" title="<?=Yii::t('app', 'Закрыть');?>"><i class="zmdi zmdi-close"></i></button>
            </div>
            <div class="modal__body modal__body--bigblacktext">
                <div class="u-pillar-box--super">
                    <div class="clearfix"></div>
                        <p><?php echo Yii::t('app', 'Данная возможность доступна только для авторизованного пользователя. Пожалуйста, войдите в свою учетную запись или пройдите регистрацию.');?></p>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="modal__footer modal__footer--centered u-window-box--medium">
                <?php echo Html::button(Yii::t('app', 'Войти'), ['id'=>$modal_id.'-submit', 'class' => 'c-button c-button--large c-button--equal modal-close']); ?>
                <div class="clearfix"></div>
                <?php echo Html::button(Yii::t('app', 'Зарегистрироваться'), ['id'=>$modal_id.'-register', 'class' => 'c-button c-button--large c-button--equal']); ?>
            </div>
		</div>
	</div>
<?php

/*
    JS stuff, that is related ONLY to this form
    See actionLogin() in MainController -> Intercooler trigger
*/
$script = <<< JS
    $('.event_loginRequire').on('click', function(event) {
           event.preventDefault();
        $('#loginRequire').trigger('openModal');
    });
    $('#loginRequire-submit').on('click', function(event) {
        $('#username').focus();
    });
JS;
$this->registerJs($script, yii\web\View::POS_READY);

// Reinit is needed for history.back stuff
?>
<script>jQuery(document).ready(function(){modalInit();});</script>
