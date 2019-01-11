<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\ElementsHelper;
use common\models\geolocation\Geolocation;
use frontend\widgets\WidgetCommentsDisqus;
use frontend\widgets\WidgetComments;

/**
 * Single school block view.
 *
 * @see school/view.php for @var used
 */
$controllerId = Yii::$app->controller->id;
$school_url = Url::toRoute($school[0]['id']);

/*
 * Main school block with preview picture 500x500
 * TODO: 'Photo' model (attach?)
 */
echo
Html::tag('div',

  Html::tag('div',

    /* Image */
    Html::img(
      Yii::$app->params['preloaderPictureBase64'],
      [
        'class' => "{$controllerId}__image block__image o-image",
        'alt' => Html::encode($school[0]['title']),
        'data-echo' => $school[0]['img'],
      ]
    ).

    /* Overlay with all the elements */
    Html::tag('div',

      /* Filter button */
      Html::tag('div', '',
        ['class' => "{$controllerId}__filter-button block__filter-button block__filter-button--inactive color-{$school[0]['categoryUrl']}--bg"]
      ).

    /* Category title */
    Html::tag('div',
      Yii::t('app', 'School').' ⟶ '.$school[0]['category'],
      ['class' => "{$controllerId}__category block__category u-window-box--small c-text--shadow"]
    ).

    /* Link to single instance of school */
    Html::tag('h1',
      '<span>'.$school[0]['title'].'</span>',
      ['class' => 'title c-text--shadow block__title school__title']
    ).

    /* Separator/divider [horizontal] */
    Html::tag('hr', '',
    ['class' => 'u-letter-box--small']
    ).

    /* Block footer */
    Html::tag('div',

      /* Actions box (like, view count, comment buttons) */
      Html::tag('div',
        Html::tag('div',
          ElementsHelper::likeButton($controllerId, $school[0]['id'], $school[0]['likesCount'], $school[0]['myLike']),
          ['class' => 'u-pull-left']
        ).
        Html::tag('div',
          ElementsHelper::linkElement('comment', $school[0]['countComments'], $school_url, 'zmdi-comment-text-alt zmdi-hc-small'),
          ['class' => 'u-pull-left u-pillar-box--large']
        ),
        ['class' => "{$controllerId}__actionbox block__actionbox"]
      ),

      ['class' => "{$controllerId}__footer block__footer u-window-box--medium c-text--darkshadow o-shadow-bottomtop"]
    ),

      ['class' => "{$controllerId}__overlay overlay"]
    ),

    ['class' => 'block__item--500x500 block__item--abs']
  ),

  ['class' => "o-grid__cell o-grid__cell--width-50 {$controllerId}__item {$controllerId}__item--{$school[0]['categoryUrl']} block__item image"]
);

/*
 * Map block 500x500
 * GOOGLE MAP PLACEHOLDER
 */
echo
Html::tag('div',
  '',
  [
    'id' => 'map__canvas--single',
    'class' => "o-grid__cell o-grid__cell--500x500 o-grid__cell--width-50 {$controllerId}__item {$controllerId}__item--{$school[0]['categoryUrl']} block__item map",
    'data-country' => $school[0]['geolocation']['country'],
    'data-city' => $school[0]['geolocation']['city'],
    'data-address' => $school[0]['geolocation']['address'],
    'data-lat' => $school[0]['geolocation']['lat'],
    'data-lng' => $school[0]['geolocation']['lng'],
  ]
);
/*
 * Mobile map block
 * Button visible map
 */
echo
Html::tag('div','Показать карту',[
    'class'=>'mobile map__canvas__visible',
    'data-toggle-text' => 'Скрыть карту'
]);
/*
 * MAIN SCHOOL TEXT
 */
echo
Html::tag('div',
  Html::tag('div',

    /* Description block body */
    Html::tag('div',
      Html::tag('div',
        isset($school[0]['description']) ? $school[0]['description'] : '',
        ['class' => "u-window-box--super"]
      ),
      ['class' => "{$controllerId}-single__body c-text--usual"]
    ).

    /* Separator */
    ElementsHelper::separatorDiamond($school[0]['title'], 'super', true).

    /* School geolocation and phone/site */
    Html::ul(
      [
        Yii::t('app', 'Country').': '.$school[0]['geolocation']['country'],
        Yii::t('app', 'City').': '.$school[0]['geolocation']['city'],
        Yii::t('app', 'Address').': '.$school[0]['geolocation']['address'],
        Yii::t('app', 'Phone').': '.$school[0]['phone'],
        isset($school[0]['site']) ? Yii::t('app', 'Website').': '.$school[0]['site'] : '',
        isset($school[0]['trainingTime']) ? Yii::t('app', 'Training time').': '.$school[0]['trainingTime'] : '',
      ],
      [
        'class' => 'u-window-box--xlarge',
      ]
    ).

    /* Separator */
    ElementsHelper::separatorDiamond(Yii::t('app', 'Equipment'), 'super', true).

    /* School detailed description */
    Html::tag('div',

      /* School parameters */
      Html::tag('div',
        Html::ul(
          [
            isset($school[0]['price']) ? Yii::t('app', 'Price').': от '.$school[0]['price'].' '.$school[0]['price_currency'] : Yii::t('app', 'Price').': '.Yii::t('app', 'Specify'),
            isset($school[0]['square']) ? Yii::t('app', 'Training room size').': '.$school[0]['square'] : '',
            isset($school[0]['floor']) ? Yii::t('app', 'Floor surface').': '.$school[0]['floor'] : '',
            isset($school[0]['mirrors']) ? Yii::t('app', 'Mirrors').': '.$school[0]['mirrors'] : '',
            isset($school[0]['traininer']) ? Yii::t('app', 'Coaches').': '.$school[0]['traininer'] : '',
            isset($school[0]['trains']) ? Yii::t('app', 'Instructor').': '.$school[0]['trains'] : '',
            isset($school[0]['equipment']) ? Yii::t('app', 'Equipment').': '.$school[0]['equipment'] : '',
            isset($school[0]['materials']) ? Yii::t('app', 'Materials').': '.$school[0]['materials'] : '',
            isset($school[0]['soundSoft']) ? Yii::t('app', 'Soundworks').': '.$school[0]['soundSoft'] : '',
          ],
          ['class' => 'u-window-box--xlarge']
        ),

        ['class' => 'o-grid__cell o-grid__cell--width-40 o-grid__cell--center school__parameters']
      ).

      /* School gallery */
      Html::tag('div',
        Html::tag('div',
          isset($school[0]['gallery']) ? ElementsHelper::galleryBlock($school[0]['gallery']) : '',
          ['class' => 'owl-carousel owl-theme']
        ),
        ['class' => 'o-grid__cell o-grid__cell--width-60 o-grid__cell--center school__gallery']
      ),

      ['class' => 'o-grid o-grid--no-gutter']
    ),

    [
      'class' => 'school__details',
    ]
  ),
  [
    'class' => 'o-grid__cell o-grid__cell--width-100 school__body',
  ]
);

/*
 * SHARE LINE
 */
echo
Html::tag('div',
  Html::tag('div',
    Html::tag('div',

      /* Share line text */
      Html::tag('div',
        Yii::t('app', 'What do you think about this school? Leave your opinion!'),
        ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--center o-grid__cell--width-75']
      ).

      /* Share line action buttons */
      Html::tag('div',
        Html::tag('div',
          ElementsHelper::linkElement('comment', $school[0]['countComments'], Url::toRoute($school[0]['id']), 'zmdi-comment-text-alt').
          ElementsHelper::likeButton(Yii::$app->controller->id, $school[0]['id'], $school[0]['likesCount'], $school[0]['myLike']).
          ElementsHelper::linkElement('repost', '', '#', 'icon-megaphone'),
          ['class' => 'news__actions u-pull-right']
        ),
        ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--center o-grid__cell--width-25 color-default u-r']
      ),

      ['class' => 'o-grid o-grid--wrap o-grid--center']
    ),
    ['class' => 'u-window-box--medium color-lighter--bg']
  ),
  ['class' => 'o-grid__cell o-grid__cell--width-100 u-letter-box--xlarge shareline branded']
);


echo
Html::tag('div',

  # Comments
  /*WidgetComments::widget([
    'elem_id' => $school[0]['id'] ?? ''
  ]),*/
  WidgetCommentsDisqus::widget(),


[
  'class' => 'o-grid o-grid__cell--width-100 o-grid--wrap o-grid--no-gutter comments',
]
);
/* JS: @see js/outstyle.portal.school.js */
?>
<script>
    jQuery(document).ready(function(){
        schoolInit();
        jQuery(function(){

            jQuery('.map__canvas__visible').on('click', function(){
                var toggleText = jQuery(this).data('toggle-text');
                jQuery(this).data('toggle-text', jQuery(this).text())
                    .text(toggleText);

                jQuery('#map__canvas--single').toggleClass('visible');
            });
        });
    });

</script>
