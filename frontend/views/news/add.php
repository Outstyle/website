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
use frontend\models\NewsCategory;

use backend\widgets\imperavi\Widget;


/* @var $this yii\web\View */
// $this->title = (($model->title)?$model->title:$model->name).' - '.Yii::$app->name;
// $this->registerMetaTag(['name' => 'description', 'content' => (($model->description)?$model->description:$model->name)]);
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Новости'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->categories->name, 'url' => ['/news/category/'.$model->categories->url]];
// $this->params['breadcrumbs'][] = $model->name;

//$NewsComments = new NewsComments();

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Добавление новости');
$this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Главная'), 'url' => ['/main/index']];
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Новости'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php $this->head() ?>
<div class="form-common">
    <!-- <h1>Добавление новости</h1> -->

        <?php $form = ActiveForm::begin([
                        'id' => 'form-news-add',
                        'enableAjaxValidation' => true,
                        'options' => ['enctype' => 'multipart/form-data']
                    ]); 
        ?>

        <div class="news-add-name">
          <?= $form->field($model, 'name')->textInput(['maxlength' => 60]) ?>
          <p class="news-add-after">
            Не более 60 символов, только буквы и цифры
          </p>
        </div>
        <div class="news-add-category">
          <?= $form->field($model, 'category')->dropDownList(
                                                          NewsCategory::getCategory(),
                                                          ['prompt' => Yii::t('app', '-- выбрать категорию --')]
                                                          )?>
        </div>
        <?//= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
        <?//= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>
        <?//= $form->field($model, 'small')->textarea(['rows' => 8]) ?>
        <?= $form->field($model, 'text')->textarea(['rows' => 8])->textarea(['rows' => 12, 'id' => 'my-textarea-id'])->widget(Widget::className(), [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 300,
                'pastePlainText' => true,
                'plugins' => [
                    'clips',
                    'fullscreen'
                ],
                'imageUpload' => Url::to(['/news/imageupload'])
            ]
        ]) ;?>
        
        <div class="news-add-img">
          <?= $form->field($model, 'image')->fileInput() ?>
          <p>Оптимальный размер загружаемого изображения 1000Х350 в формате .jpg или .png</p>
        </div>

        <div class="article-bottom">
            <a href="/news/">« Вернуться назад</a>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Предложить новость') : Yii::t('app', 'Сохранить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
</div>
