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

# Widget wrapper
echo Html::beginTag('div', ['class' => $options['class']]);

# Working with each image
if (!empty($photos)) {
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
              Photo::getByPrefix('//i.t2.local/'.$photo['img'], '210x126_'), ['class' => 'o-image u-full-width user__photothumbnail']
            )
          ).

          # Photo title and link
          ElementsHelper::photoLink($photo['id'], $photo['name']).

          # Photo date and provider
          Html::tag('div',

            Yii::t('app', '{photo_date} via outstyle', [
              'photo_date' => Yii::$app->formatter->asDateTime(strtotime($photo['created']), Yii::$app->params['date'])
            ]),

            [
              'class' => 'user__photodate'
            ]
          ),

          [
            'class' => trim($options['cell_class'].' user__photo'),
            'data-lc-key' => $options['attachment']['elem_type'], /* Data for working with localstorage attachment */
            'data-lc-elem' => $options['attachment']['elem_key']+$key /* Data for working with localstorage attachment */
          ]
        );
    }
}

# Widget wrapper END
echo Html::endTag('div');
