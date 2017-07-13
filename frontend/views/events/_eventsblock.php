<?php

use yii\helpers\Html;
use yii\helpers\Url;

//use common\components\helpers\HashtagHelper; /* DO WE NEED HASHTAGS IN EVENTS? */
use common\components\helpers\ElementsHelper;
use common\components\helpers\StringHelper;
use common\components\helpers\PriceHelper;

/**
 * Single event view
 * This is a partial view file.
 *
 * @var views\events\index
 */

/* Check if our items are 'first-generation' ones :D This is needed for removing them after AJAX calls */
$itemClass = '';

if ($page == 1) {
    $itemClass = ' event__item--initial';
}

if (isset($modelEvents)) {
    echo ElementsHelper::loadMore(Url::toRoute('events/show'), '#outstyle_events .events__body', '{"page":'.(int) $page.',"category":'.(int) $category.'}');

    foreach ($modelEvents as $key => $event) {
        $eventsCategoryUrl = Url::toRoute(Yii::$app->controller->id.'/'.$event['categoryUrl']);

      /* Odd or even */
      $eventCellClass = ($key % 2) ? ' even' : ' odd';

        echo
        Html::tag('div',

          /* Filter round button */
          Html::tag('div',
            '',
            ['class' => "event__filter-button color-{$event['categoryUrl']}--bg"]
          ).

          /* Event image block */
          Html::tag('div',

            /* Centered div box with image title and filter button */
            Html::tag('div',
              Html::tag('div',
                ElementsHelper::linkElement('title', $event['title'], Url::toRoute('events/'.$event['id'])),
                ['class' => 'u-center-block__content u-pillar-box--medium event__title']
              ),
              ['class' => 'u-center-block h-fixed-125 event__title-wrap']
            ).

            /* Category title */
            Html::tag('div',
              ElementsHelper::linkElement('category', $event['category'], $eventsCategoryUrl, false),
              [
                'class' => "event__category block__category c-text--shadow"
              ]
            ).

            /* Event image */
            Html::img(
              Yii::$app->params['preloaderPictureBase64'],
              [
                'class' => 'o-image event__image',
                'alt' => Html::encode($event['title']),
                'data-echo' => $event['img'],
              ]
            ),

            [
              'class' => 'o-grid__cell o-grid__cell--top o-grid__cell--width-35 event__image',
            ]
          ).

          /* Event datetime block */
          Html::tag('div',
            Html::tag('div', '', ['class' => 'decoration_1']).
            Html::tag('div',
              Html::tag('div',
                '<h4 class="c-text__color--redshadow">'.StringHelper::convertTimestampToHuman(strtotime($event['events_date'])).'</h4>',
                [
                  'class' => 'u-center-block__content u-full-width',
                ]
              ),
              [
                'class' => 'u-center-block h-fixed-125',
              ]
            ),
            [
              'class' => 'o-grid__cell o-grid__cell--width-15 event__datetime',
            ]
          ).

          /* Event description block */
          Html::tag('div',
            Html::tag('div',
              Html::tag('div',

                  /* Event address */
                  Html::tag('div',
                    '<h2 class="c-text__color--redshadow">'.$event['geolocation']['name'].'</h2>',
                    ['class' => 'datebox__address']
                  ).

                  /* Event country and city */
                  Html::tag('div',
                    $event['geolocation']['formatted_address'],
                    ['class' => 'datebox__city']
                  ),

                [
                  'class' => 'u-center-block__content u-full-width datebox',
                ]
              ),
              [
                'class' => 'u-center-block h-fixed-125',
              ]
            ),
            [
              'class' => 'o-grid__cell o-grid__cell--width-35 event__description',
            ]
          ).

          /* Event price block */
          Html::tag('div',

            /* Event price */
            Html::tag('div',
              Html::tag('div',
                Html::tag('span',
                  '<sup>'.Yii::t('app', 'from').'</sup>'.
                  $event['price'].
                  '<sup>'.$event['price_currency'].'</sup>',
                  ['class' => 'c-text__color--redshadow c-text--loud']
                ),
                ['class' => 'u-center-block__content u-full-width']
              ),
              ['class' => 'u-center-block h-fixed-125']
            ),

            [
              'class' => 'o-grid__cell o-grid__cell--width-15 event__price '.PriceHelper::getPriceVisualClass($event['price_visual']),
            ]
          ),

          [
            'class' => 'o-grid o-grid--wrap o-grid--top o-grid--no-gutter event'.$eventCellClass.$itemClass,
          ]
        );
    }
}
