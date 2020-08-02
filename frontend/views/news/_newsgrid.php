<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\ElementsHelper;

/* --- Filter block, that shows up on 'news__filter-button' click event
TODO: $include should be avoided - instead need to put child elements into ::filterBox itself
 --- */

if (empty($category)) {
    echo ElementsHelper::filterBox(
        $newsCategories,
        'categories[]',
        Url::toRoute('news/show'),
        $target_el = '#outstyle_news .news',
        $include = '#news-filter-form'
    );
    echo '<h1>' . Yii::t('seo', Yii::$app->controller->id . '.h1') . '</h1>';
} else {
    foreach ($newsCategories as $c) {
        if ($c->id == $category) {
            echo '<h1>' . Yii::t('seo', Yii::$app->controller->id . '.' . $c->url . '.h1') . '</h1>';
        }
    }
}

/* --- ONE NEWS BLOCK --- */
echo $this->render('_newsblock', [
    'modelNews' => $modelNews,
    'page' => $page,
    'contentHeight' => $contentHeight,
    'category' => $category,
]);

/* This input is for sending pages */
echo Html::hiddenInput('page', $page, ['id' => 'page']);

/* This input is needed for smooth Packery init after each AJAX call */
echo Html::hiddenInput('contentHeight', '', ['id' => 'contentHeight']);

/* Pass controller ID for JS to rely on */
echo '<script>var CURRENT_CONTROLLER_ID = "' . Yii::$app->controller->id . '";</script>';
