<?php
   
    use yii\helpers\Html;
    use yii\helpers\Url;
    use frontend\widgets\WidgetProfileUserMenu;
    use yii\widgets\ActiveForm;
    use frontend\models\UserAvatar;
    use frontend\models\UserDescription;
    use frontend\models\User;
    use frontend\models\Photo;
    use frontend\models\Country;
    use frontend\models\City;
    use frontend\models\SchoolCategory;

    /* @var $this yii\web\View */
    $this->title = Yii::t('app', 'Добавление школы');
    $this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
    //$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Главная'), 'url' => ['/main/index']];
  // $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Каталог школ'), 'url' => ['index']];
  $this->params['breadcrumbs'][] = $this->title;
?>

<div class="form-common">
  <!-- <h1>Добавление школы</h1> -->
      <?php $form = ActiveForm::begin([
                            'id' => 'form-school-add',
                            'enableAjaxValidation' => true,
                            'options' => ['enctype' => 'multipart/form-data']
                        ]); 
      ?>

        <?= $form->field($model, 'user')->hiddenInput(['value'=>Yii::$app->user->id])->label(false) ?>
  
          <div class="school-add-name">
            <?= $form->field($model, 'title')->textInput(['maxlength' => 30, 'placeholder' => 'Введите название школы'])?>
            <p>Не более 30 символов, только буквы и цифры</p>
          </div>
          <div class="school-add-category">
            <p class="top-school-add">Категория и местоположение <span>*</span></p>
              <div class="school-add-category-wrap">
                <?= $form->field($model, 'category')->label(false)->dropDownList(
                                                                    SChoolCategory::getCategory(),
                                                                    ['prompt'=> Yii::t('app', 'Выберите категорию...')]
                                                                )?>
                                        <?= $form->field($model, 'country')->label(false)->dropDownList(
                                                                                        Country::getToSelect(),
                                                                                        [
                                                                                            'prompt'=>'Выберите страну...',
                                                                                            'onchange' => '
                                                                                                $.post( "/main/city/?id='.'"+$(this).val(), function( data ){
                                                                                                    $( "select#school-city" ).html( data );
                                                                                                });'
                                                                                        ])?>
                                        <?=$form->field($model, 'city')->label(false)->dropDownList(
                                                                                        City::getToSelect(),
                                                                                        [
                                                                                            'prompt'=>'Выберить город...'
                                                                                        ])?>
            </div>
          </div>



        <div class="school-add-inform-all">
          <p class="top-school-add">Общая информация о событии <span>*</span></p>
          <div class="school-add-inform-label">
            <p>Адрес <span>*</span></p>
            <p>Телефон <span>*</span></p>
            <p>Сайт </p>
          </div>
          <div class="school-add-inform-input">
            <?= $form->field($model, 'address')->textInput(['placeholder' => 'Укажите адрес школы'])->label(false) ?>
            <?= $form->field($model, 'phone')->textInput(['placeholder' => '+38(___)________'])->label(false) ?>
            <?= $form->field($model, 'site')->textInput(['placeholder' => 'http://'])->label(false) ?>
          </div>
          <div class="school-add-inform-after">
            <p>Например: ул. Стальная, 21</p>
            <p>Внимательно проверьте правильность написания телефона!</p>
          </div>
        




            <div class="school-add-inform-label">
              <p>Время тренировок <span>*</span></p>
              <p>Цена <span>*</span></p>
              <p>Обоудование <span>*</span></p>
              <p>Площадь зала <span>*</span></p>
              <p>Покрытие пола <span>*</span></p>
            </div>

            <div class="school-add-inform-input">
              <?= $form->field($model, 'trainingTime')->textInput(['placeholder' => 'Укажите время тренировок, часы и дни'])->label(false) ?>
              <?= $form->field($model, 'price')->textInput(['placeholder' => 'Укажите цену'])->label(false) ?>
              <?= $form->field($model, 'equipment')->textInput(['placeholder' => 'Укажите доступное оборудование для тренировок'])->label(false) ?>
              <?= $form->field($model, 'square')->textInput(['placeholder' => '__ м2'])->label(false) ?>
              <?= $form->field($model, 'floor')->textInput(['placeholder' => 'Опишите покрытие в вашем помещении'])->label(false) ?>
            </div>
            <div class="school-add-inform-after">
              <p>Например: с 14:00 до 21:00 (по вторникам и четвергам)</p>
              <p>Например: от 150 грн. до 400 грн. за урок</p>
              <p>Тут можете указать, какое оборудование имеется в наличии</p>
              <p class="content-none">.</p>
              <p>Например: лакированный пол или резина, мягкие маты</p>
            </div>



        <div class="school-add-inform-label">
          <p>Тренирует</p>
          <p>Обучает</p>
        </div>
        <div class="school-add-inform-input">
          <?= $form->field($model, 'traininer')->textInput(['placeholder' => 'Кто проводит тренировки в вашей школе?'])->label(false) ?>
          <?= $form->field($model, 'trains')->textInput(['placeholder' => 'Кто проводит обучение в вашей школе?'])->label(false) ?>
        </div>
        <div class="school-add-inform-after">
          <p>Например: BBoy Pako или Иванов Михаил Петрович</p>
        </div>


      

<!-- работай с этим -->


      <div class="school-bootom-wrap">
        <div class="school-add-inform-label">
          <p>Дополнительно</p>
        </div>
        <div class="school-add-inform-input">
          <?= $form->field($model, 'mirrors')->checkbox(['label' => 'Есть ли на базе школы работа с программами по обработке звука?'])?>
          <?= $form->field($model, 'soundSoft')->checkbox(['label' => 'Присутствуют ли зеркала в помещении?'])?>
          <?= $form->field($model, 'materials')->checkbox(['label' => 'Выдаются ли материалы для обучения за счет школы?'])?>
          <?= $form->field($model, 'other')->checkbox(['label' => 'Тут может быть еще какой-то пункт, если вдруг еще что забыли'])?>
        </div>
        <div class="school-add-inform-after"></div>
      </div>

    </div>
<!-- работай с этим -->



        <!-- пока оставь -->

        <?//= $form->field($model, 'mirrors[]')->checkboxlist([0 =>'нет', 1 =>'Да']);?>
        <?//= $form->field($model, 'materials')->checkboxlist([0 =>'Свои', 1 =>'За счет школы']);?>
        <?//= $form->field($model, 'soundSoft')->checkboxlist([0 =>'нет', 1 =>'Да']);?>

        <?= $form->field($model, 'description')->textArea(['rows' => 5])?>

        <!-- Поле загрузки фоток -->
        <div class="news-add-img">
          <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
          <p>Оптимальный размер загружаемого изображения 1000x350 в формате .jpg или .png</p>
        </div>

        <!-- Поле загрузки фоток -->

          <div class="article-bottom">
            <a href="/events/">« Вернуться назад</a>
            <div class="form-group">
                <?= Html::submitButton('Добавить событие', ['class' => 'btn btn-primary']) ?>
                <?//= Html::resetButton('отменить', ['class' => 'btn btn-danger']) ?>
            </div>
          </div>
      <?php ActiveForm::end(); ?>

</div>