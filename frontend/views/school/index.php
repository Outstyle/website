<?php

use common\components\helpers\ElementsHelper;
use common\components\helpers\SEOHelper;

/**
 * Main schools grid, that must be wrapped in #ajax for Intercooler
 *
 * @var $this            yii\web\View
 * @var $model           common/models/School
 * @var $categories      common/models/School
 * @var $page            common/models/School
 */

SEOHelper::setMetaInfo($this);

echo ElementsHelper::ajaxGridWrap(
    'school',
    'o-grid--no-gutter padding-mobile',
    $this->render(
        '_schoolgrid',
        [
            'model' => $model,
            'categories' => $categories,
            'page' => $page,
            'contentHeight' => $contentHeight,
        ]
    )
);
