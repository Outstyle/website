<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use backend\widgets\imperavi\Widget;
use common\components\helpers\ElementsHelper;
use common\components\helpers\PriceHelper;

/*
 * @var $model      backend\models\Events
 * @var $categories backend\models\Events
 * @var $form       yii\widgets\ActiveForm
 */

/* If we're having an errors, that are sent by another model - displaying it after ajaxified validation */
if ($errors) {
    echo ElementsHelper::errorsContainer($errors);
}

echo Html::beginTag('div', ['class' => 'events-form']);

  $form = ActiveForm::begin(
    [
      'id' => 'form-create-event',
      'enableAjaxValidation' => true,
      'options' => ['enctype' => 'multipart/form-data'],
    ]
  );

echo
    $form->field($model, 'user')->hiddenInput(['value' => Yii::$app->user->id])->label(false),
    $form->field($model, 'title'),
    $form->field($model, 'category')->dropDownList($categories,
      ['prompt' => Yii::t('app', 'Не указана')]
    ),
    $form->field($model, 'events_date')->textInput(
      ['value' => $model->events_date]
    ),

    /* Price and currency stuff */
    Html::beginTag('div', ['class' => 'row']),
      $form->field($model, 'price', ['options' => ['class' => 'col-md-4']])->textInput(['maxlength' => 255]),
      $form->field($model, 'price_currency', ['options' => ['class' => 'col-md-4']])->dropDownList(PriceHelper::getPriceCurrenciesList()),
      $form->field($model, 'price_visual', ['options' => ['class' => 'col-md-4']])
      ->dropDownList(
          array_map(function ($name) {
              return Yii::t('app', $name);
          },
          PriceHelper::getPriceVisualList())
      ),
    Html::endTag('div'),

    /* Geolocation dropdowns */
    $this->render('@common/views/geolocation/_form', [
      'geolocation_id' => $model->geolocation_id,
      'geolocation' => $model->geolocation,
    ]),

    $form->field($model, 'phones')->textInput(['id' => 'phone']),
    $form->field($model, 'site'),
    $form->field($model, 'email')->textInput(['type' => 'email']),

    /* Main big description. Using imperavi widget: imperavi\Widget */
    $form->field($model, 'description', ['options' => ['class' => 'textarea--enhanced']])->textarea([
      'rows' => 12,
      'id' => Yii::$app->controller->id.'-textarea', ])->widget(Widget::className(), [
        'settings' => [
          'lang' => 'ru',
          'minHeight' => 300,
          'pastePlainText' => true,
          'buttonSource' => true,
          'buttonAdvanced' => true,
          'imageUpload' => Url::to([Yii::$app->controller->id.'/imageupload']),
        ],
        'plugins' => [
          'videos' => 'backend\assets\RedactorAsset',
          'imageuploader' => 'backend\assets\RedactorAsset'
        ]
      ]);

    /* Event main photo */
    if (strpos($model->getImageSrc(), 'noimage') !== false && $model->getPrimaryKey()) {
        echo $form->field($model, 'img')->widget('demi\image\FormImageWidget', [
          'imageSrc' => $model->getImageSrc('900_'),
          'deleteUrl' => ['deleteImage', 'id' => $model->getPrimaryKey()],
          'cropUrl' => ['cropImage', 'id' => $model->getPrimaryKey()],
        ]);
    }

    echo
    $form->field($model, 'status')->dropDownList($status),

    /* Submit button */
    Html::tag('div',
      Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']),
      [
        'class' => 'form-group',
      ]
    );

  ActiveForm::end();

echo Html::endTag('div');

/*
    ----------------------------------------------------------------------------
    JS stuff, that is related ONLY to this view
    ----------------------------------------------------------------------------
*/

?>
<script>
jQuery(document).ready(function () {

  /* Init */
  var currentTime = new Date();

  /* Masked input: phone */
  jQuery("#phone").mask("+9(999)999-99-99?99");

  /* Datetimepicker */
  jQuery.datetimepicker.setLocale('ru');
  jQuery("#events-events_date").datetimepicker({
    format: "d.m.Y H:i",
    minDate:0,
    yearStart: currentTime.getFullYear(),
    yearEnd: currentTime.getFullYear() + 2,
  });

  /* Slimscroll FIXME */
  setTimeout(function(){
    if (jQuery(".redactor-box").height() >= 640) {
        jQuery('.redactor-editor').slimScroll({
            height: '640px',
            wheelStep: 5,
        });
    }
  },250);

  jQuery('.textarea--enhanced a.re-html').on("click", function() {
      jQuery('.textarea--enhanced .slimScrollDiv').toggle();
  });

});
</script>
