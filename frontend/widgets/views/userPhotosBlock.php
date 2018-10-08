<?php
/**
 * User photos block view
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
use common\models\Photo;

/* @see @frontend/widgets/UserPhotosBlock for vars */
/* @var $photos */
/* @var $options */

# Working with each image
if (!empty($photos)) {

    # Widget wrapper
    if ($options['class']) {
        echo Html::beginTag('div', ['class' => $options['class']]);
    }

    foreach ($photos as $key => $photo) {
        echo Html::tag('div',

          # Widget settings button
          ElementsHelper::widgetButton(
            $options['widgetButton']['action'],
            $options['widgetButton']['position'],
            $options['widgetButton']['size'],
            $options['widgetButton']['indicator']
          ).

          # Photo image
          ElementsHelper::photoLink(
            $photo['id'],
            Html::img(
              Photo::getByPrefixAndServiceId(
                $photo['img'],
                $photoSize = '210x126_',
                $photo['service_id'],
                Photo::PHOTO_TYPE_DEFAULT,
                $photo['user']
              ),
              [
                'class' => 'o-image u-full-width user__photothumbnail'
              ]
            )
          ).

          # Photo date and provider
          Html::tag('div',

            Yii::t('app', '{photo_date} via outstyle', [
              'photo_date' => Yii::$app->formatter->asDateTime(strtotime($photo['created']), Yii::$app->params['date'])
            ]),

            [
              'class' => 'user__photodate u-pillar-box--small c-text--shadowed'
            ]
          ),

          [
            'class' => trim($options['cell_class'].' user__photo'),
            'data-lc-key' => $options['attachment']['elem_type'], /* Data for working with localstorage attachment */
            'data-lc-elem' => $options['attachment']['elem_key']+$key /* Data for working with localstorage attachment */
          ]
        );
    }

    # Widget wrapper END
    if ($options['class']) {
        echo Html::endTag('div');
    }
}
