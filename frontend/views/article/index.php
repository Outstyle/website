<?php

use common\components\helpers\ElementsHelper;
use common\components\helpers\SEOHelper;

/**
 * Main articles grid, that must be wrapped in #ajax for Intercooler
 * Here we are using 'news' model since articles is only a representation of news, having the same data
 *
 * @var $this                   yii\web\View
 * @var $modelNews              common/models/News
 * @var $categories             common/models/News
 * @var $categorу               common/models/News
 * @var $page                   common/models/News
 * @var $contentHeight   common/models/News  needed for Packery layout
 */

SEOHelper::setMetaInfo($this);

echo ElementsHelper::ajaxGridWrap(
    'articles',
    'news',
    $this->render('//article/_articlegrid', [
        'modelNews' => $modelNews,
        'newsCategories' => $newsCategories,
        'page' => $page,
        'contentHeight' => $contentHeight,
        'category' => $category ?? '',
    ])
);
