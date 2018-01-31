<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\ElementsHelper;
use frontend\widgets\UserVideosBlock;

/**
 * Attachments preview
 *
 * @var object  $this                   yii\web\View
 * @var array   $model                  @frontend/controllers/AttachmentsController
 * @var integer $elem_type              @frontend/controllers/AttachmentsController
*/

# VIDEOS widget | @frontend/widgets/UserVideosBlock.php
echo UserVideosBlock::widget([
  'videos' => $model,
  'options' => [
    'class' => 'o-grid o-grid--wrap',
    'cell_class' => (count($model) > 1) ? 'o-grid__cell user__attachment' : 'o-grid__cell--no-wrap user__attachment',
    'widgetButton' => [
      'action' => 'delete',
      'position' => 'topright',
      'size' => '2x'
    ],
    'attachment' => [
      'elem_type' => $elem_type
    ]
  ]
]);
