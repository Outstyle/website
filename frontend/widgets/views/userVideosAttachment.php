<?php
/**
 * User videos attachments view
 * Part of Outstyle network
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @version 1.0
 *
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\ElementsHelper;
use common\components\helpers\html\AttachmentsHelper;

/* @see @frontend/widgets/UserVideosBlock for vars */
/* @var $videos */
/* @var $options */

echo Html::beginTag('div', ['class' => $options['class']]);

  foreach ($videos as $video) {
      echo Html::tag('div',

        # Video attachment image
        AttachmentsHelper::attachmentAddLink($video, $options['attachment']['elem_type'], '').
        $video['video_title'].

        # Video date and provider
        Html::tag('div',

          Yii::t('app', '{video_date} via {video_provider}', [
            'video_date' => Yii::$app->formatter->asDateTime(strtotime($video['created_at']), Yii::$app->params['date']),
            'video_provider' => $video['service_id'],
          ]),

          [
            'class' => 'user__videodate'
          ]
        ),

        [
          'class' => trim($options['cell_class'].' u-letter-box--medium user__video')
        ]
      );
  }

echo Html::endTag('div');
