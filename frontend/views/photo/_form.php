<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\components\helpers\html\UploadHelper;
use common\models\Photo;

/**
 * @var $this        yii\web\View
 * @var $model       common\models\Photoalbum
 * @var $photos      common\models\Photo
 * @var $album_id    PhotoalbumController -> actionView() | $_POST param
**/

/**
* Model for form to work with
* @var object $model
*/
$model = new Photo();
$photosCounter = (!empty($photos) ? count($photos) : 0);

$form = ActiveForm::begin(
  [
    'id' => 'form-upload-to-photoalbum',
    'action' => Url::toRoute(['api/photo/upload']),
    'enableAjaxValidation' => false,
    'options' => [
      'class' => 'dm-uploader',
      'enctype' => 'multipart/form-data'
    ],
  ]
);

  echo UploadHelper::uploadBox(
    '<i class="zmdi zmdi-cloud-upload zmdi-hc-5x color-dj"></i><p><span>'.
      Yii::t('app', 'This album has no photos yet.').'<br></span>'.
      Yii::t('app', 'Drag and drop files into this area to upload').
    '</p>'.

    /* Add new photos widget */
    $form->field($model, 'img')->widget('demi\image\FormImagesWidget')->label(false).
    $form->field($model, 'album')->hiddenInput(['value'=> $album_id ?? 0])->label(false).
    $form->field($model, 'album_photos_count')->hiddenInput(['value'=> $photosCounter])->label(false)

  );

ActiveForm::end();

echo UploadHelper::uploadFilesTemplate();
