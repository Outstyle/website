<?php

use common\components\helpers\ElementsHelper;
use common\components\helpers\SEOHelper;

/**
 * Main events grid, that must be wrapped in #ajax for Intercooler
 *
 * @var $this        yii\web\View
 * @var $model       common/models/Events
 * @var $categories  common/models/Events
 * @var $page        common/models/Events
 */

SEOHelper::setMetaInfo($this);

echo ElementsHelper::ajaxGridWrap(
    Yii::$app->controller->id,
    'o-grid--no-gutter',
    $this->render(
        '_eventsgrid',
        [
            'modelEvents' => $modelEvents,
            'eventsCategories' => $eventsCategories,
            'page' => $page,
            'contentHeight' => $contentHeight,
            'category' => $category ?? ''
        ]
    )
);
