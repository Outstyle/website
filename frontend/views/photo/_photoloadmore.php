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

/**
 * Photos loadmore view
 *
 * @var $this               yii\web\View
 * @var $photos      array  PhotoController -> actionGet()
 * @var $page        int    PhotoController -> actionGet()
 * @var $album_id    int    PhotoController -> actionGet()
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
*/

echo UserPhotosBlock::widget([
  'photos' => $photos,
  'options' => [
    'class' => false,
    'cell_wrap' => 'o-grid o-grid--wrap u-window-box--small photoalbum__wrap',
    'cell_class' => 'o-grid__cell o-grid__cell--width-33 u-window-box--small',
    'widgetButton' => [
      'action' => 'edit',
      'position' => 'bottomright',
      'size' => '2x'
    ],
  ]
]);

echo ElementsHelper::loadMore(Url::toRoute('api/photo/get'), '.photoalbum__photos', '{"page":'.(int)$page.',"album_id":'.(int)$album_id.'}');
