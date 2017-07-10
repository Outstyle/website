<?php
/**
 * Away page layout
 * Questions? Feel free to ask: <scsmash3r@gmail.com> / skype: smash3rs / Telegram: @scsmash3r
 * Github: https://github.com/scsmash3r
 */

use yii\helpers\Html;
use yii\helpers\Url;

use yii\widgets\Spaceless;

$this->beginPage();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title></title>
<?php $this->head(); ?>
<?php echo Html::csrfMetaTags(); ?>
</head>
<body>
<?php
$this->beginBody();

$script = <<< JS
document.addEventListener('DOMContentLoaded', function() {
    ga('send', 'event', {
       eventCategory: 'away_page',
       eventAction: 'click',
       eventLabel: '{$url}'
     });
    location.replace('{$url}');
});
JS;

$this->registerJsFile(Url::to('/js/outstyle.analytics.js', true), ['position' => yii\web\View::POS_END]);
$this->registerJs($script, yii\web\View::POS_END);
$this->endBody();
?>
</body>
</html>
<?php
$this->endPage() ?>
