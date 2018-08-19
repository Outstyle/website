
<?php
   
    use yii\helpers\Html;
    use yii\helpers\Url;
    use frontend\widgets\WidgetProfileUserMenu;
    use yii\widgets\ActiveForm;
    use frontend\models\UserAvatar;
    use frontend\models\UserDescription;
    use frontend\models\User;
    use frontend\models\Photo;
    use frontend\models\Blacklist;
    use frontend\models\UserPrivacy;

    /* @var $this yii\web\View */
    $this->title = Yii::t('app', 'Настройки');
    $this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
    $this->params['breadcrumbs'][] = Yii::t('app', '');

?>

<script type="text/javascript">

$(document).ready(function() {  
    $('.tab1').on('click', function(){
        var that = $(this);
        $('.tab1').removeClass('active');
        that.addClass('active');
        var index = that.data('index');
        $('.setting').removeClass('active');
        $('[data-content-index="' + index + '"]').addClass('active');
    });
})
</script>

    <div class="settings">
        <div class="select-settings">
            <div  data-index="1" class="tab1 active txt">
                <h1>Общее</h1>
            </div>
            <div  data-index="2" class="tab1 txt">
                <h1>Приватность</h1>
            </div>

        </div>
        <div class="option-settings">
            <div class="content1 setting active" data-content-index="1">
                <div class="change-txt">
                    Изменить пароль 
                </div>


            
            <div class="old-pass">

                <?php $form = ActiveForm::begin([ 'id' => 'form-resetDate',]); ?>
                    <div class="passwords">
                        <?= $form->field($model, 'oldPass')->passwordInput(['value' => ''])?>
                        <?= $form->field($model, 'newPass')->passwordInput(['value' => '']) ?>
                        <?= $form->field($model, 'newPass_repeat')->passwordInput(['value' => '']) ?>
                        <?= Html::submitButton(Yii::t('app', 'Изменить пароль'), ['class' => 'btn-change-pass']) ?>
                    </div>
                    
                <?php ActiveForm::end(); ?>
                
                <!--<?= $form->field($model, 'newPass_repeat', [
                                'template' => '<div class=oldPass> 
                                <label for=name> <span>Ваше имя</span>{input}</label>{error}</div>',
                                ])->passwordInput(['value' => '', 'class' => '']) ?>
                                <div class="form-group">
                                    <?= Html::submitButton(Yii::t('app', 'Изменить пароль'), ['id' => 'p', 'name' => 'signup-button']) ?>
                                </div>-->



               
                </div>


                <div class="change-txt">
                    Изменить почтовый адресс
                </div>
                <div class="mail">
                    
                    <div class="current-mail">
                        Текущий адресс: <input class="cur-mail" type="text" disabled="disabled" placeholder="<?=$oldEmail;?>"> 
                    </div>
                   
                    <?php $form = ActiveForm::begin([ 'id' => 'form-resetMale',]); ?>
                        <?= $form->field($model, 'newEmail')->input('email');?>
                        <?= Html::submitButton(Yii::t('app', 'Сохранить адресс'), ['class' => 'btn-change-pass']) ?>
                    <?php ActiveForm::end(); ?>
                  

                </div>

            </div>

            <div class="content2 setting" data-content-index="2">
                <div class="change-txt">
                    Приватность 
                </div>

                 <div class="setings-private">
                <?php $form = ActiveForm::begin([ 'id' => 'form-resetDate',]); ?>

                    <?php foreach($modelPrivacy->getAttributes() AS $name => $v){ if($name == 'id'){ continue; } ?> 
                        <?=$form->field($modelPrivacy, $name)
                            ->dropDownList(UserPrivacy::setNames(),['options' => [ $v => ['selected' => true]]]);?>
                    <?php } ?>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Сохранить изменения'), ['class' => 'btn btn-change-private-settings', 'name' => 'signup-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>
                </div>
                
               
            </div>
            <!-- <div class="content3 setting" data-content-index="3">
                <div class="change-txt">
                    Черный список
                </div>
                <div class="Add-to-blacklist">
                    <input type="text" placeholder="Введите имя пользователя или ссылку на его страницу">
                    <button class="btn-add-to-blacklist">Добавить в черный список</button>
                </div>
                <div class="change-txt">
                    Уже в списке
                </div>
                <div class="in-blacklist">
                    <div class="black-list-user">
                        <div class="user-ava">
                            <img src = "<?//php echo Yii::$app->homeUrl; ?>css/img/fhoto3.png" alt="">
                        </div>
                        <div class="user-name">
                            Trix
                        </div>
                        <h1>Удалить из списка</h1>
                    </div>
                    <div class="black-list-user">
                        <div class="user-ava">
                            <img src = "<?//php echo Yii::$app->homeUrl; ?>css/img/fhoto3.png" alt="">
                        </div>
                        <div class="user-name">
                            Trix
                        </div>
                        <h1>Удалить из списка</h1>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
<div class="settings_bottom clearfix">
    <div class="settings_bottom-left">
        <h1>Настройки</h1>
    </div>
    <div class="settings_bottom-right"></div>
</div>
