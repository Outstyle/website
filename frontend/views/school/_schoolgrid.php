<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\ElementsHelper;

/**
 * @see school/index.php for @var used
 */

/* --- ONE SCHOOL BLOCK --- */
echo $this->render('_schoolblock', [
    'model' => $model,
    'page' => $page,
    'contentHeight' => $contentHeight,
    'category' => $category ?? '',
]);

/* This input is for sending pages */
echo Html::hiddenInput('page', $page, ['id' => 'page']);

/* This input is needed for smooth Packery init after each AJAX call */
echo Html::hiddenInput('contentHeight', '', ['id' => 'contentHeight']);

/* Pass controller ID for JS to rely on */
echo '<script>var CURRENT_CONTROLLER_ID = "' . Yii::$app->controller->id . '";</script>';


/**
 * Filter block 500x250
 * Filter box (small tooltip-alike dropdown)
 * TODO: on that filter @ _newsgrid
 */
if ($categories) {
    echo ElementsHelper::filterBlock('geolocation', Yii::$app->controller->id, $categories);
    echo ElementsHelper::filterBox($categories, 'category[]', Url::toRoute('school/show'), $target_el = '#outstyle_school .school', $include = '#contentHeight,#school-filter-form');
}
