<?php


use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Category;
use common\models\News;
use common\models\User;
use backend\models\StatusPublication;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

/* TODO: Rewrite this shit v */

$controllerId = Yii::$app->controller->id;

$this->title = Yii::t('app', ucfirst($controllerId));
$this->params['breadcrumbs'][] = $this->title;
$categories = Category::getCategories(['id' => News::NEWS_CATEGORIES], '', true);
$users = User::usersSelect();
$status = StatusPublication::getStatusList();

?>
<div class="<?= $controllerId; ?>-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class="u-pull-right">
        <?= Html::a('<i class="glyphicon glyphicon-plus-sign"></i>', ['create'], ['class' => 'btn btn-info btn-sm']) ?>
    </p>

    <?= GridView::widget([
        'tableOptions' => [
            'class' => 'table table-striped table-bordered table-notdborder table-mini',
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions' => [
                    'style' => 'width:40px;max-width: 40px;',
                ],
            ],
            'name',
            [
                'attribute' => 'category',
                'value' => function ($model) {
                    return $model->categories->name;
                },
                'filter' => $categories,
            ],
            [
                'attribute' => 'user',
                'value' => function ($model) {
                    return (isset($model->user->username)) ? $model->user->username : '';
                },
                'filter' => $users,
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return StatusPublication::getStatus($model->status);
                },
                'filter' => $status,
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>