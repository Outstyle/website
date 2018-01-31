<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\ElementsHelper;
use frontend\widgets\UserVideosBlock;

/**
 * Attachments to choose preview (in modal window)
 *
 * @var $this                   yii\web\View
 * @var $model                  @frontend/controllers/AttachmentsController
 * @var $elem_type              @frontend/controllers/AttachmentsController
 * @var $elem_type_parent       @frontend/controllers/AttachmentsController
*/

echo ElementsHelper::ajaxGridWrap(Yii::$app->controller->id.'_video', 'o-grid--no-gutter u-window-box--medium',

    # VIDEOS widget | @frontend/widgets/UserVideosBlock.php
    UserVideosBlock::widget([
      'videos' => $model,
      'options' => [
        'class' => 'o-grid o-grid--wrap',
        'cell_class' => 'o-grid__cell o-grid__cell--width-25',
        'view' => 'userVideosAttachment',
        'attachment' => [
          'elem_type' => $elem_type,
          'elem_type_parent' => $elem_type_parent,
        ]
      ]
    ])

);
