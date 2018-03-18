<?php

use common\components\helpers\ElementsHelper;
use common\components\helpers\SEOHelper;

/**
 * Main news grid, that must be wrapped by #ajax for Intercooler
 *
 * @var $this            yii\web\View
 * @var $modelNews              common/models/News
 * @var $newsCategories         common/models/News
 * @var $page                   common/models/News
 * @var $outstyle_news_height   common/models/News  needed for Packery layout
 */



SEOHelper::setMetaInfo($this);
SEOHelper::setCanonicalForPage($this);


echo ElementsHelper::ajaxGridWrap(Yii::$app->controller->id, 'o-grid--no-gutter',
    $this->render('_newsgrid',
    [
      'modelNews' => $modelNews,
      'newsCategories' => $newsCategories,
      'page' => $page,
      'outstyle_news_height' => $outstyle_news_height,
      'category' => $category ?? '',
    ])
 );
