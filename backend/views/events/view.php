<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Events */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="events-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            'geolocation_id',
            [
                'attribute' => 'category',
                'value' => $model->getCategories()->one()->name,
            ],
            [
                'attribute' => 'events_date',
                'value' => $model->events_date,
            ],
            'price',
            'phones',
            'site',
            'email',
            'description',
        ],
    ]);

    if (strpos($model->getImageSrc(), 'noimage') === false) {
        $form = ActiveForm::begin([
          'id' => 'form-events-create',
          'enableAjaxValidation' => true,
          'options' => ['enctype' => 'multipart/form-data'],
        ]);

        echo $form->field($model, 'img')->widget('demi\image\FormImageWidget',
          [
            'imageSrc' => $model->getImageSrc(),
            'deleteUrl' => ['deleteImage', 'id' => $model->primaryKey],
            'cropUrl' => ['cropImage', 'id' => $model->primaryKey],
          ]);

        ActiveForm::end();
    }

    ?>

</div>
