<?php

/**
 * New Photoalbum creation form
 *
 * @var $this       yii\web\View
 * @var $model      common\models\Photoalbum
 * @var $form_type  Form can be 'create' (with empty $model) or 'edit' (with populated $model)
**/

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\components\helpers\ElementsHelper;
use common\components\helpers\PrivacyHelper;

echo Html::beginTag('div', [
 'id' => 'photoalbum-activeform'
]);

$this->beginPage();
$this->beginBody();

echo
Html::beginTag('div', [
  'class' => ($form_type == 'edit') ? Yii::$app->controller->id.'-form' : Yii::$app->controller->id.'-form u-window-box--super'
]);

  $form = ActiveForm::begin(
    [
      'id' => 'form-'.$form_type.'-photoalbum',
      'action' => Url::toRoute(['api/photoalbum/'.$form_type]),
      'enableAjaxValidation' => false,
      'options' => [
        'enctype' => 'multipart/form-data',
        'csrf' => false /* Already automatically passing it via jQuery.ajax() data param, so no need to generate hidden input field */
      ]
    ]
  );

echo
    /* Form fields for new photoalbum */
    $form->field($model, 'name')->textInput(['maxlength' => 64]),
    $form->field($model, 'text')->textarea(['maxlength' => 255, 'rows' => 4]),

    $form->field($model, 'privacy', [
      'options' => ['class' => 'form-group form-group--transparent form-group--small form-group--separated']
    ])->dropDownList(PrivacyHelper::getPrivacyList()),

    $form->field($model, 'privacy_comments', [
      'options' => ['class' => 'form-group form-group--transparent form-group--small']
    ])->dropDownList(PrivacyHelper::getPrivacyList());

    /* Additional fields for photoalbum edit mode */
    if ($form_type == 'edit') {
        echo $form->field($model, 'id')->hiddenInput(['maxlength' => 11])->label(false);

        /* Edit photoalbum submit button */
        echo Html::tag('div',
            Html::submitButton(
                Yii::t('app', 'Save changes'),
                [
                  'id' => $form_type.'photoalbum-submit',
                  'class' => 'c-button u-small i-'.$form_type.'photoalbum',
                  'title' => Yii::t('app', 'Save changes')
                ]
            ),
            ['class' => 'u-letter-box--large clearfix']
        );
    } else {

      /* Create new photoalbum submit button */
      echo Html::tag('div',
          Html::submitButton(
              Yii::t('app', 'Create album'),
              [
                'id' => $form_type.'photoalbum-submit',
                'class' => 'c-button u-small i-'.$form_type.'photoalbum u-pull-right',
                'title' => Yii::t('app', 'Create album')
              ]
          ),
          ['class' => 'modal__footer modal__footer--centered u-letter-box--large clearfix']
      );
    }

  ActiveForm::end();

echo Html::endTag('div');

$this->endBody();
$this->endPage();

echo Html::endTag('div');
