<?php
    /**
     * User login form
     * Used in portal.php layout, header section
     * Questions? Feel free to ask: <scsmash3r@gmail.com> or skype: smash3rs
     *
     * TODO: redo form with Yii's HTML helpers and vars
     */

/*
    For registered users we show his avatar'n'stuff
*/
use app\models\UserDescription;

$modelDescription = new UserDescription();
if (!Yii::$app->user->isGuest) {
    ?>
    <div class="user-avatar__header">
       <a class="login-uzer login-form__user u-pull-right" href="/admin">
            <img src="<?php echo Yii::$app->homeUrl; ?>images/avatar/<?php echo Yii::$app->user->id; ?>_small.jpg" class="roundborder avatar" alt="" width="56">
        </a>
        <a class="exit-uzer" href="/site/logout/"></a>
    </div>
<?php
/*
    Using http://intercoolerjs.org/docs.html to POST data to controller 'Site'
    Here I tried not to use Yii's ActiveForm instance to avoid certain pitfalls with more complicated code chunks
    <form> wrap is needed for enter key to be usable for submission
*/
} else {
    ?>
    <form id="form-login">
        <div class="mobile-form-login">
            <i class="form-login-ico zmdi zmdi-account-o zmdi-hc-lg"></i>
            <div class="o-grid o-grid--wrap o-grid--no-gutter u-center-block__content u-center-block__content--vertical login-form">
                <div class="u-center-block o-grid__cell o-grid__cell--width-10 login-form__icon">
                    <i class="u-center-block__content zmdi zmdi-account-o zmdi-hc-lg"></i>
                </div>
                <div class="o-grid__cell o-grid__cell--width-70 login-form__input">
                    <input type="text" id="email" name="email" class="c-field c-field--xtra" placeholder="<?=Yii::t('app', 'Email'); ?>" tabindex="1" autofocus>
                </div>
                <div class="u-center-block o-grid__cell o-grid__cell--width-20 login-form__doubleicon">
                    <div class="u-center-block__content u-center-block__content--vertical login-form__icon">
                        <button type="submit"
                                id="login-form__submit"
                                class="zmdi-icon--hoverable"
                                ic-indicator="#outstyle_loader"
                                ic-include="#email,#password"
                                ic-post-to="/api/site/login">
                            <i class="zmdi zmdi-arrow-right zmdi-hc-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="u-center-block o-grid__cell o-grid__cell--width-10 login-form__icon">
                    <i class="u-center-block__content zmdi zmdi-more zmdi-hc-lg"></i>
                </div>
                <div class="o-grid__cell o-grid__cell--width-70 login-form__input">
                   <input type="password" name="password" id="password" class="c-field c-field--xtra" placeholder="<?=Yii::t('app', 'Пароль'); ?>" tabindex="2">
                </div>
                <div class="u-center-block o-grid__cell o-grid__cell--width-20 login-form__doubleicon">
                    <div class="u-center-block__content u-center-block__content--vertical login-form__icon">
                        <a href="#passwordrestore" class="modal-open" title="<?=Yii::t('app', 'Забыли пароль?'); ?>"><i class="zmdi zmdi-alert-circle-o zmdi-hc-lg c-text__color--red"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php

}

/*
    JS stuff, that is related ONLY to this form
    See actionLogin() in MainController -> Intercooler trigger
*/
?>
<script>
jQuery(document).ready(function () {
    jQuery("body").on("form-login", function(event, data) {
        if (data.error) {
            jQuery.each(data.error, function(key, value) {
                ohSnap(value, {'color':'red'});
            });
        }
    });
});
</script>
