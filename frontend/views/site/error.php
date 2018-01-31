<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $exception->statusCode;
?>
<div id="banksy_was_here">
  <h1><?php
  if (isset($exception->statusCode)) {
      echo $exception->statusCode.' / '.Yii::t('app', $exception->getMessage());
  } else {
      echo '500! Что-то тут пошло не так... Попробуйте зайти позже.';
  } ?></h1>
  <img src="<?php echo Yii::$app->homeUrl; ?>css/i/outstyle_404_overlay.png">
  <?=Html::a('← '.Yii::t('app', 'Get back to tha hood!'), Url::to(['/']), ['id' => 'steal_this_link']);?>
</div>
