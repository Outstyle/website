<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = Yii::t('app', 'Create ' . Yii::$app->controller->id);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', ucfirst(Yii::$app->controller->id)),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Yii::$app->controller->id; ?>-create">

    <h1>
        <?= Html::encode($this->title) ?>
        <span class="label label-primary pull-right">
            <?= Yii::$app->controller->id; ?>
        </span>
    </h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>