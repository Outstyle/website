<?php
   
    use yii\helpers\Html;
    use yii\helpers\Url;
    use frontend\widgets\WidgetProfileUserMenu;
    use yii\widgets\ActiveForm;
    use frontend\models\UserAvatar;
    use frontend\models\UserDescription;
    use frontend\models\User;
    use frontend\models\Photo;
    use frontend\models\EventsCategory;

    /* @var $this yii\web\View */
    $this->title = Yii::t('app', 'Редактирование события');
    $this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
	//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Главная'), 'url' => ['/main/index']];
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Каталог событий'), 'url' => ['index']];

	$this->params['breadcrumbs'][] = $this->title;
?>

<h1>Добавление события</h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title') ?>
	<?= $form->field($model, 'category')->dropDownList(
	                                                    EventsCategory::getCategory(),
	                                                    ['prompt'=> Yii::t('app', 'Не указана')]
	                                                )?>

	<!-- пойдет в additional Надо сделать передачу date -->
	<?= $form->field($model, 'date') ?>

	<?= $form->field($model, 'description')->textArea(['rows' => 5])?>

	<!-- Поле загрузки фоток -->
	<?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
	<!-- Поле загрузки фоток -->

    <div class="form-group">
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Отменить', ['class' => 'btn btn-danger']) ?>
    </div>

<?php ActiveForm::end(); ?>

