<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody();
    NavBar::begin([
        'brandLabel' => '<img src="/css/img/logo_adm.png">',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse',
        ],
    ]);
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Yii::t('app', 'Вход'), 'url' => ['/site/login']];
    } else {
        $menuItems[] = ['label' => Yii::t('app', 'Пользователи'), 'url' => ['/user/index']];
        $menuItems[] = [
            'label' => Yii::t('app', 'Materials'),
            'itemsOptions' => ['class' => 'dropdown-submenu'],
            'submenuOptions' => ['class' => 'dropdown-menu'],
            'items' => [
                ['label' => Yii::t('app', 'News'), 'url' => ['/news']],
                ['label' => Yii::t('app', 'Articles'), 'url' => ['/article']],
                ['label' => Yii::t('app', 'Video'), 'url' => ['/videoz']],
                ['label' => Yii::t('app', 'Releases'), 'url' => ['/releases']],
                ['label' => Yii::t('app', 'Reviews'), 'url' => ['/reviews']],
            ],
        ];
        $menuItems[] = ['label' => Yii::t('app', 'Школы'), 'url' => ['/school']];
        $menuItems[] = ['label' => Yii::t('app', 'События'), 'url' => ['/events']];
        $menuItems[] = [
            'label' => Yii::t('app', 'Выход ({user})', ['user' => Yii::$app->user->identity->username]),
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
    <div class="wrap">
        <div class="container">
            <?= Breadcrumbs::widget([
                'homeLink' => false,
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; <?= Yii::$app->name ?> <?= date('Y') ?></p>
        </div>
    </footer>
    <div id="ohsnap"></div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>