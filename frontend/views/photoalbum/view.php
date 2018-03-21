<?php

use yii\helpers\Html;
use yii\helpers\Url;

use frontend\widgets\UserPhotosBlock;
use yii\widgets\Spaceless;

use common\components\helpers\html\TooltipsHelper;

/**
 * Album photos list
 *
 * @var $this                     yii\web\View
 * @var $model                    @frontend/models/Photoalbum
 * @var $photos                   @frontend/models/Photo
 * @var $album_id                 PhotoalbumController -> actionView() | $_POST param or 0
 * @var $album_name               PhotoalbumController -> actionView() | $_POST param or string
*/

Spaceless::begin();

echo Html::beginTag('div', ['id' => 'photos_list']);

    # Album photos title
    echo Html::tag('h1', htmlentities($album_name));

    # Widget "+" tooltip container, shown on click (@see: outstyle.user.photoalbums.js)
    echo Html::button('<i class="zmdi zmdi-plus zmdi-hc-lg"></i>', [
      'id' => 'photo__editbutton',
      'class' => 'c-button c-button--white c-button--round tooltip u-pull-right',
      'title' => Yii::t('app', 'Edit'),
    ]);

    # Tooltip data for "+" button
    echo TooltipsHelper::tooltipContainerForPhotoalbum();


    # PHOTOALBUM edit form
    if (isset($model)) {
        echo Html::tag('div',

          # Photoalbum cover
          Html::tag('div',
            '123',
          [
            'class' => 'o-grid__cell o-grid__cell--width-30 o-grid__cell--no-gutter'
          ]).

          # Photoalbum description
          Html::tag('div',
            $this->render('_form', [
              'model' => $model,
              'form_type' => 'edit',
            ]),
          [
            'class' => 'o-grid__cell o-grid__cell--width-70'
          ]),

        [
          'class' => 'o-grid o-grid--wrap o-grid--lightbg photoalbum__edit'
        ]);
    }


    # PHOTOS widget | @frontend/widgets/UserPhotosBlock.php
    echo UserPhotosBlock::widget([
      'photos' => $photos,
      'options' => [
        'class' => 'o-grid o-grid--wrap photoalbum__photos',
        'cell_wrap' => 'o-grid o-grid--wrap u-window-box--small photoalbum__wrap',
        'cell_class' => 'o-grid__cell o-grid__cell--width-33 u-window-box--small',
        'widgetButton' => [
          'action' => 'edit',
          'position' => 'bottomright',
          'size' => '2x'
        ],
      ]
    ]);


    # PHOTOS ADD FORM
    # We also need to pass $album_id, cause photo can not be added without being tied to photoalbum
    echo Html::tag('div',

      $this->render('../photo/_form', [
        'photos' => $photos,
        'album_id' => $album_id,
      ]),

    [
      'class' => 'photo__add'
    ]);

echo Html::endTag('div');

Spaceless::end();
