<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = Yii::t('app', 'Create news');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$controllerId = Yii::$app->controller->id;
?>
<div class="<?= $controllerId; ?>-create">

    <h1><?= Html::encode($this->title) ?><span class="label label-primary pull-right"><?= $controllerId; ?></span></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>