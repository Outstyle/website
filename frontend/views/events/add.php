<?php
   
    use yii\helpers\Html;
    use yii\helpers\Url;
    use frontend\widgets\WidgetProfileUserMenu;
    use yii\widgets\ActiveForm;
    use frontend\models\UserAvatar;
    use frontend\models\UserDescription;
    use frontend\models\User;
    use frontend\models\Photo;
    use frontend\models\EventsCategory;
    use frontend\models\Country;
    use frontend\models\City;


    /* @var $this yii\web\View */
    $this->title = Yii::t('app', 'Добавление события');
    // $this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
    //$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Главная'), 'url' => ['/main/index']];
  // $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Каталог событий'), 'url' => ['index']];
  $this->params['breadcrumbs'][] = $this->title;
    $this->registerJs('
        $(function() {
            $( "#events-date" ).datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true
            });
        });
    ', $this::POS_READY, 'myDatePicker1');
?>
<div class="form-common">
        <!-- <h1>Добавление события</h1> -->

        <?php $form = ActiveForm::begin([
                            'id' => 'form-resetDate',
                            'enableAjaxValidation' => true,
                            'options' => ['enctype' => 'multipart/form-data']
                        ]); 
        ?>

          <div class="school-add-name">
            <?= $form->field($model, 'title')->textInput(['maxlength' => 30]) ?>
            <p>Не более 30 символов, только буквы и цифры</p>
          </div>

          <div class="school-add-category">
            <p class="top-school-add">Категория и местоположение <span>*</span></p>
            <div class="school-add-category-wrap">
              <?= $form->field($model, 'category')->label(false)->dropDownList(
                                                                  EventsCategory::getCategory(),
                                                                  ['prompt'=> Yii::t('app', 'Выберите категорию...')]
                                                              )?>
                <?= $form->field($model, 'country')->label(false)->dropDownList(
                                                                Country::getToSelect(),
                                                                [
                                                                    'prompt'=>'Выберите страну...',
                                                                    'onchange' => '
                                                                        $.post( "/main/city/?id='.'"+$(this).val(), function( data ){
                                                                            $( "select#events-city" ).html( data );
                                                                        });'
                                                                ])?>
                <?=$form->field($model, 'city')->label(false)->dropDownList(
                                                                City::getToSelect(),
                                                                [
                                                                    'prompt'=>'Выберить город...'
                                                                ])?>
            </div>


            <div class="school-add-inform-all">
              <p class="top-school-add">Общая информация о школе <span>*</span></p>
              <div class="school-add-inform-label">
                <p>Адрес <span>*</span></p>
                <p>Телефон <span>*</span></p>
                <p>Сайт </p>
              </div>
              <div class="school-add-inform-input">
                <?= $form->field($model, 'address')->textInput(['placeholder' => 'Укажите адрес события'])->label(false) ?>
                <?= $form->field($model, 'phones')->textInput(['placeholder' => '+38(___)________'])->label(false) ?>
                <?= $form->field($model, 'site')->textInput(['placeholder' => 'http://'])->label(false) ?>
              </div>
              <div class="school-add-inform-after">
                <p>Например: ул. Стальная, 21</p>
                <p>Внимательно проверьте правильность написания телефона!</p>
              </div>





              <div class="school-add-inform-label">
                <p>Дата события <span>*</span></p>
                <p>Время события <span>*</span></p>
                <p>Цена билета <span>*</span></p>
                <p>E-mail <span>*</span></p>
              </div>

              <div class="school-add-inform-input">
                <?= $form->field($model, 'date')->textInput(['placeholder' => 'Укажите дату события'])->label(false) ?>
                <?= $form->field($model, 'time')->textInput(['placeholder' => 'Укадите время события'])->label(false) ?>
                <?= $form->field($model, 'price')->textInput(['placeholder' => 'Укажите цену билета'])->label(false) ?>
                <?= $form->field($model, 'email')->textInput(['placeholder' => 'Укажите e-mail'])->label(false) ?>
              </div>
              <div class="school-add-inform-after">
                <p>Например: 21 февраля 2016года</p>
                <p>Например: 14.00</p>
                <p>Например: от 150грн до 400грн за билет</p>
                <p>Укажите контактный e-mail</p>
              </div>

            </div>
          
          <?= $form->field($model, 'description')->textArea(['rows' => 5])?>

          <!-- Поле загрузки фоток -->
          <div class="news-add-img">
            <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
            <p>Оптимальный размер загружаемого изображения 1000x350 в формате .jpg или .png</p>
          </div>
            <?//= $form->field($modelin, 'image')->fileInput()->hint(Yii::t('app', 'Допустимые файлы: png,jpg,gif,jpeg, размер не более: {ras}МБ', ['ras' => 1]))->label(false, ['style'=>'display:none']) ?>
          <!-- Поле загрузки фоток -->

            <div class="article-bottom">
                <a href="/events/">« Вернуться назад</a>
                <div class="form-group">
                    <?= Html::submitButton('Добавить событие', ['class' => 'btn btn-primary']) ?>
                    <?//= Html::resetButton('Отменить', ['class' => 'btn btn-danger']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
</div>