<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\StringHelper;
use common\components\helpers\ElementsHelper;

use app\models\Photo;

/**
 * User photoalbums list
 *
 * @var $this                    yii\web\View
 * @var $photoalbums             @frontend/models/Photoalbum
*/

foreach ($photoalbums as $photoalbum) {
    $photos_count = isset($photoalbum['photo']) ? count($photoalbum['photo']) : 0;

    /* TODO: Make this code more consistent */
    foreach ($photoalbum['photo'] as $photo) {
        if ($photoalbum['cover'] == $photo['id']) {
            $photoalbum['cover'] = Photo::getByPrefix($photo['img'], '210x126_');
        }
    }
    echo $photoalbum['cover'];
    echo Html::tag('div',

      # Album edit button
      Html::button(
          Html::tag('i', '', [
            'class' => "zmdi zmdi-edit zmdi-hc-lg",
          ]),
        [
          'class' => 'zmdi-icon--hoverable i-widgetbutton i-widgetbutton--topleft',
          'title' => Yii::t('app', 'Edit'),
          'ic-action' => 'userPhotoalbumEdit',
        ]
      ).

      # Album delete button
      Html::button(
          Html::tag('i', '', [
            'class' => "zmdi zmdi-close zmdi-hc-lg",
          ]),
        [
          'class' => 'zmdi-icon--hoverable i-widgetbutton i-widgetbutton--topright',
          'title' => Yii::t('app', 'Delete'),
          'ic-action' => 'userShowPhotoalbumDeleteModal:'.$photoalbum['id'],
        ]
      ).

      # Album container (active IC click)
      Html::tag('div',

        # Album image
        $photoalbum['cover'].

        # Album title
        Html::tag('div',
          '<span>'.StringHelper::cutString($photoalbum['name'], 26).'</span>'.
          '<div><i class="zmdi zmdi-collection-folder-image zmdi-hc-lg"></i>&nbsp;<span>'.$photos_count.'</span></div>',
        [
          'class' => 'album__title',
          'title' => $photoalbum['name']
        ]),

      [
        'id' => 'album-'.$photoalbum['id'],
        'class' => 'album',
        'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
        'ic-target' => '#photos_area',
        'ic-include' => '{"album_name":"'.$photoalbum['name'].'","album_id":'.$photoalbum['id'].',"album_count":'.$photos_count.'}',
        'ic-post-to' => Url::toRoute(['photoalbum/view']),
        'ic-push-url' => 'false',
        'ic-select-from-response' => '#photos_list'
      ]),


      ['class' => 'album-wrap']
    );
}
