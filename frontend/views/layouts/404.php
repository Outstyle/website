<?php
/**
 * 404 page layout
 * Questions? Feel free to ask: <scsmash3r@gmail.com> / skype: smash3rs / Telegram: @scsmash3r
 * Github: https://github.com/scsmash3r
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

use yii\widgets\Spaceless;

$this->beginPage();
Spaceless::begin();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo Html::encode($this->title); ?></title>
<?php $this->head(); ?>
<?php echo Html::csrfMetaTags(); ?>
<link rel="shortcut icon" type="image/png" href="/css/favicon.png">
<link href="<?php echo Yii::$app->homeUrl; ?>css/font/fonts.css" rel="stylesheet">
<link href="<?php echo Yii::$app->homeUrl; ?>css/outstyle.404.css" rel="stylesheet">
<style>
  #banksy_was_here {
    background: #fff url(/css/i/404/outstyle_404_<?=rand(0, 9);?>.jpg) no-repeat center center
  }
</style>
</head>
<body class="error">
<?php $this->beginBody(); ?>
  <?=$content;?>
<?php $this->endBody(); ?>
</body>
</html>
<?php
Spaceless::end();
$this->endPage(); ?>
