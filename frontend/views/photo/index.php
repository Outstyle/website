<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

use frontend\widgets\UserPhotosBlock;

use common\components\helpers\ElementsHelper;
use common\components\helpers\SEOHelper;
use common\components\helpers\html\LoadersHelper;

/**
 * User photos page
 * This page is an entry point and can have seo meta tags
 *
 * @var $this                    yii\web\View
 * @var $photos                  @frontend/models/Photo
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
*/

SEOHelper::setMetaInfo($this);

echo ElementsHelper::ajaxGridWrap(Yii::$app->controller->id, 'o-grid--no-gutter',

    # USER PHOTOALBUMS SIDEBAR - 25%
    Html::tag('div',

      # Photoalbums list
      LoadersHelper::loaderDiv('albums_area', 'smallest').
      Html::beginTag('div', ['id' => 'albums_area']).
        $this->render('../photoalbum/index', [
          'photoalbums' => $photoalbums
        ]).
      Html::endTag('div'),

    [
      'class' => 'o-grid__cell o-grid__cell--width-25 photos__albums'
    ]).

    # USER PHOTOS AREA - 75%
    Html::tag('div',
      $this->render('../photoalbum/view', [
        'photos' => $photos,
        'album_name' => Yii::t('app', 'All photos'),
        'album_id' => 0
      ]),
    [
      'id' => 'photos_area',
      'class' => 'o-grid__cell o-grid__cell--width-75 photos__list'
    ]),

    ['class' => 'photos__container']
);

# Modals for work with photoalbums - must be outside the wrap so to be on all album pages
echo $this->render('@modals/userPhotoalbumCreate');
echo $this->render('@modals/userPhotoalbumDelete');

/* JS: @see js/outstyle.user.photoalbums.js */
?>
<script>jQuery(document).ready(function(){photoalbumsInit()});</script>
