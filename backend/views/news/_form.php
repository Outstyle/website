<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use backend\widgets\imperavi\Widget;
use backend\models\Category;
use backend\models\StatusPublication; /* This must be in controller */

use common\components\helpers\BlocksHelper;

/* @var $this yii\web\View */
/* @var $model app\models\News */
/* @var $form yii\widgets\ActiveForm */

/* TODO: get rid of this stuff */
$categories = Category::getCategories(['id' => [1, 2, 3, 4]], '', true);

echo Html::beginTag('div', ['class' => 'news-form news-single']);

    $form = ActiveForm::begin(
      [
        'id' => 'form-create-news',
        'enableAjaxValidation' => true,
        'options' => ['enctype' => 'multipart/form-data'],
      ]
    );

echo

    $form->field($model, 'name')->textInput(['maxlength' => 255]),
    $form->field($model, 'category')->textInput()->dropDownList($categories, ['prompt' => Yii::t('app', 'Choose category...')]),

    $form->field($model, 'title')->textInput(['maxlength' => 255]),
    $form->field($model, 'description')->textInput(['maxlength' => 255]),
    $form->field($model, 'small')->textarea(['rows' => 8]),

    /* Main big text. Using imperavi widget: imperavi\Widget */
    $form->field($model, 'text', ['options' => ['class' => 'textarea--enhanced']])->textarea([
      'rows' => 12,
      'id' => Yii::$app->controller->id.'-textarea', ])->widget(Widget::className(), [
        'settings' => [
          'lang' => 'ru',
          'minHeight' => 300,
          'pastePlainText' => true,
          'buttonSource' => true,
          'buttonAdvanced' => true,
          'plugins' => ['clips', 'properties'],
          'imageUpload' => Url::to([Yii::$app->controller->id.'/imageupload']),
        ],
      ]);

    /* News main photo */
    if (strpos($model->getImageSrc(), 'noimage') !== false) {
        echo $form->field($model, 'img')->widget('demi\image\FormImageWidget', [
          'imageSrc' => $model->getImageSrc('250_'),
          'deleteUrl' => ['deleteImage', 'id' => $model->getPrimaryKey()],
          'cropUrl' => ['cropImage', 'id' => $model->getPrimaryKey()],
        ]);
    }

    echo
    $form->field($model, 'status')->dropDownList(StatusPublication::getStatusList()),
    $form->field($model, 'img_block_size')->dropDownList(BlocksHelper::getBlockSizeList()),

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
    JS stuff, that is related ONLY to this form
*/
$script = <<< 'JS'
$(function(){

        if ($(".redactor-box").height() >= 640) {
            $('.redactor-editor').slimScroll({
                height: '640px',
                wheelStep: 5,
            });
        }
        $('.field-news-textarea a.re-html').on("click", function() {
            $('.field-news-textarea .slimScrollDiv').toggle();
        });

        $('#news-status option[value="1"]').attr("selected",true);

});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
