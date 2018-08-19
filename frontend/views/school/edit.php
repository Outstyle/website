<?php
   
    use yii\helpers\Html;
    use yii\helpers\Url;
    use frontend\widgets\WidgetProfileUserMenu;
    use yii\widgets\ActiveForm;
    use frontend\models\UserAvatar;
    use frontend\models\UserDescription;
    use frontend\models\User;
    use frontend\models\Photo;
    use frontend\models\Country;
    use frontend\models\City;
    use frontend\models\SchoolCategory;

    /* @var $this yii\web\View */
    $this->title = Yii::t('app', 'Редактирование школы');
    $this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
	//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Главная'), 'url' => ['/main/index']];
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Каталог школ'), 'url' => ['index']];

	$this->params['breadcrumbs'][] = $this->title;
?>

<h1>Добавление школы</h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title') ?>
	<?= $form->field($model, 'category')->dropDownList(
	                                                    SChoolCategory::getCategory(),
	                                                    ['prompt'=> Yii::t('app', 'Не указана')]
	                                                )?>
    <?= $form->field($model, 'country')->dropDownList(
	                                                    Country::getToSelect(),
	                                                    ['prompt'=> Yii::t('app', 'Не указана')]
	                                               	 )?>
    <?=$form->field($model, 'city')->dropDownList(
    													City::getCityList(),
                                                    	['prompt'=> Yii::t('app', 'Не указана')]
    												);?>
	<?= $form->field($model, 'address') ?>
	<?= $form->field($model, 'phone') ?>
	<?= $form->field($model, 'price') ?>
	<?= $form->field($model, 'site') ?>

	<!-- пойдет в additional -->
	<?= $form->field($model, 'trainingTime') ?>
	<?= $form->field($model, 'square') ?>
	<?= $form->field($model, 'floor') ?>


	<?= $form->field($model, 'mirrors')->checkboxlist([0 =>'нет', 1 =>'Да']);?>

	<?= $form->field($model, 'traininer') ?>
	<?= $form->field($model, 'equipment') ?>
	<?= $form->field($model, 'trains') ?>

	<?= $form->field($model, 'materials')->checkboxlist([0 =>'Свои', 1 =>'За счет школы']);?>
	<?= $form->field($model, 'soundSoft')->checkboxlist([0 =>'нет', 1 =>'Да']);?>

	<?= $form->field($model, 'description')->textArea(['rows' => 5])?>

	<!-- Поле загрузки фоток -->
	<?//= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
	<!-- Поле загрузки фоток -->

    <div class="form-group">
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('отменить', ['class' => 'btn btn-danger']) ?>
    </div>

<?php ActiveForm::end(); ?>

