<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\components\helpers\ElementsHelper;
use common\components\helpers\StringHelper;
use common\models\geolocation\Geolocation;

use frontend\widgets\WidgetComments;

/* Registering GoogleMaps JS file for map to be shown only on this pages */
$this->registerJsFile('//maps.googleapis.com/maps/api/js?key='.Yii::$app->params['googleMapsApiKey'].'&amp;extension=.js', ['position' => yii\web\View::POS_HEAD]);

echo
Html::tag('div',

  // BREADCRUMBS
  Breadcrumbs::widget(
    [
      'tag' => 'ol',
      'homeLink' => false,
      'options' => [
        'class' => 'c-breadcrumbs u-cf',
      ],
      'itemTemplate' => '<li class="c-breadcrumbs__crumb">{link}<i class="icon-right-open-big zmdi-hc-small"></i></li>',
      'links' => [
          [
            'label' => Yii::t('app', ucfirst(Yii::$app->controller->id)),
            'url' => ['/'.Yii::$app->controller->id],
            'ic-get-from' => Url::to('/'.Yii::$app->controller->id),
            'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
          ],
          [
            'label' => $modelEvents[0]['category'],
            'url' => [Yii::$app->controller->id.'/'.$modelEvents[0]['categoryUrl']],
            'ic-get-from' => Url::toRoute(Yii::$app->controller->id.'/'.$modelEvents[0]['categoryUrl']),
            'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
          ],
          [
            'label' => $modelEvents[0]['title'],
          ],
      ],
    ]
  ),

  [
    'class' => 'o-grid__cell o-grid__cell--width-100',
  ]
);

// SEPARATOR
echo ElementsHelper::separatorDiamond(Yii::t('app', 'Event author')),

// AUTHORBOX WITH LIKES AND HASHTAGS
Html::tag('div',
  Html::tag('div',
    Html::tag('div',

      //cell 1
      Html::tag('div',

        //category circle icon
        Html::tag('i',
          '',
          ['class' => "zmdi zmdi-circle color-{$modelEvents[0]['categoryUrl']}"]
        ).

        //category name
        Html::tag('span',
          Yii::t('app', $modelEvents[0]['category']),
          ['class' => 'authorbox__category']
        ),

        ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--bottom o-grid__cell--width-25 color-default u-l']
      ).

      //cell 2
      Html::tag('div',

        //username and avatar
        Html::a(
          Html::img(
            $modelEvents[0]['userAvatar'],
            [
              'class' => "roundborder color-{$modelEvents[0]['userCulture']}--border avatar avatar--smallest",
              'alt' => Yii::t('app', 'Аватар пользователя {user}', ['user' => $modelEvents[0]['userName']]),
            ]
          ).
          $modelEvents[0]['userName'],
          Url::toRoute('profile/'.$modelEvents[0]['userId']),
          [
            'class' => "user-name user-name--withavatar color-{$modelEvents[0]['userCulture']} authorbox__author",
          ]
        ).

        //hashtags
        Html::tag('div',
          /*HashtagHelper::processHashtagsFromText($events['text'])*/'#hashTag1 #hashTag 2',
          [
            'class' => 'authorbox__hashtags u-letter-box--medium color-default u-c',
          ]
        ),

        ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-50 u-c']
      ).

      //cell 3
      Html::tag('div',
        Html::tag('div',
          ElementsHelper::linkElement('views', 0, false, 'zmdi-eye').
          ElementsHelper::linkElement('comment', $modelEvents[0]['countComments'], Url::toRoute(Yii::$app->controller->id.'/'.$modelEvents[0]['id']), 'zmdi-comment-text-alt').
          ElementsHelper::likeButton(Yii::$app->controller->id, $modelEvents[0]['id'], $modelEvents[0]['likesCount'], $modelEvents[0]['myLike']),
          ['class' => 'authorbox__actions u-pull-left']
        ).
        Html::tag('div',
          ElementsHelper::linkElement('repost', '', '#', 'icon-megaphone'),
          ['class' => 'authorbox__actions u-pull-right']
        ),

        ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--bottom o-grid__cell--width-25 color-default u-r authorbox__actionbox']
      ),

      ['class' => 'o-grid o-grid--wrap o-grid--center u-window-box--large']
    ),
    ['class' => 'u-pillar-box--super authorbox']
  ),

  ['class' => 'o-grid__cell o-grid__cell--width-100']
),

// EVENT DATE BOX
Html::tag('div',
  Html::tag('div',
    Html::tag('div',
      Html::tag('div',

        //cell 1
        Html::tag('div',

          //date number
          Html::tag('b',
            StringHelper::convertTimestampToHuman(strtotime($modelEvents[0]['events_date']), 'd'),
            ['class' => 'c-text__color--redshadow number']
          ).

          //date month
          Html::tag('span',
            StringHelper::convertTimestampToHuman(strtotime($modelEvents[0]['events_date']), 'F'),
            ['class' => 'c-text__color--blackshadow month']
          ).

          //date day
          Html::tag('span',
            StringHelper::convertTimestampToHuman(strtotime($modelEvents[0]['events_date']), 'l'),
            ['class' => 'color-default day']
          ).

          //date H:i
          Html::tag('span',
            StringHelper::convertTimestampToHuman(strtotime($modelEvents[0]['events_date']), 'H:i'),
            ['class' => 'hour']
          ),

          ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-25 datebox__date']
        ).

        //cell 2
        Html::tag('div',
          Html::tag('div',
            $modelEvents[0]['title'],
            ['class' => 'u-pillar-box--large']
          ),
          ['class' => 'o-grid__cell o-grid__cell--width-50 c-text__color--blackshadow c-text__color--darklight u-c datebox__title']
        ).

        //cell 3 TODO: link to geolocation (place, school, etc - must be a link)
        Html::tag('div',

          //hostname
          Html::tag('div',
            empty($modelEvents[0]['geolocation']['name']) ? Yii::t('app', 'Unknown location') : $modelEvents[0]['geolocation']['name'],
            ['class' => 'c-text__color--redshadow datebox__host']
          ).

          //address
          Html::tag('div',
            $modelEvents[0]['geolocation']['address'],
            ['class' => 'c-text__color--blackshadow datebox__address']
          ).

          //country and city
          Html::tag('div',
            $modelEvents[0]['geolocation']['city'].', '.
            $modelEvents[0]['geolocation']['country'],
            ['class' => 'color-default datebox__city']
          ),

          ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-25 datebox__place']
        ),

        ['class' => 'o-grid o-grid--wrap o-grid--center datebox__wrap']
      ),

      ['class' => 'u-window-box--large u-bordered-box u-bordered-box--top datebox']
    ).

    // GOOGLE MAP PLACEHOLDER
    Html::tag('div',
      '',
      [
        'id' => 'map__canvas',
        'data-country' => $modelEvents[0]['geolocation']['country'],
        'data-city' => $modelEvents[0]['geolocation']['city'],
        'data-address' => $modelEvents[0]['geolocation']['address'],
        'data-lat' => $modelEvents[0]['geolocation']['lat'],
        'data-lng' => $modelEvents[0]['geolocation']['lng'],
      ]
    ).

    // SEPARATOR / MAP SHOW HIDE
    ElementsHelper::separatorDiamond(
      '<b>'.Yii::t('app', 'Show map').'</b>'.
      '<b>'.Yii::t('app', 'Hide map').'</b>',
      '', false, 'right toggleable'
    ),

    ['class' => 'u-pillar-box--super']
  ),
  ['class' => 'o-grid__cell o-grid__cell--width-100 map']
),

/* Event main area */
Html::tag('div',
  Html::tag('div',

    /* Event main big image */
    Html::img(
      $modelEvents[0]['img_big'],
      ['class' => 'o-image events__bigimage']
    ).

    /* Event main text */
    Html::tag('div',
      $modelEvents[0]['description'],
      ['class' => 'u-letter-box--medium events__body']
    ),

    ['class' => 'u-window-box--super']
  ),
  ['class' => 'o-grid__cell o-grid__cell--width-100 c-text--usual']
);

/* RECOMMENDED EVENTS */
if (isset($modelEvents[0]['recommended'])) {
    echo Html::beginTag('div',[
      'class' => 'recommended-bottom-wrap'
    ]);
  // SEPARATOR
  echo ElementsHelper::separatorDiamond(Yii::t('app', 'Recommended events')),

  // SIMILAR EVENTS WRAP BEGIN
  Html::beginTag('div',
    [
      'id' => Yii::$app->controller->id.'-single-recommended',
      'class' => 'o-grid o-grid--wrap u-full-width u-window-box--medium recommended',
      'ic-target' => '#'.ElementsHelper::DEFAULT_TARGET_ID,
      'ic-push-url' => 'true',
      'ic-select-from-response' => '#'.ElementsHelper::DEFAULT_AJAX_ID,
    ]
  );

  // Cycle through each recommended event to show it
  foreach ($modelEvents[0]['recommended'] as $recommendedEvents) {
      $url = Url::toRoute(Yii::$app->controller->id.'/'.$recommendedEvents['id']);

      //recommended events single instance
      echo
      Html::tag('div',
        Html::tag('div',
          Html::a('<img class="o-image grayscale" src="'.$recommendedEvents['img'].'"><span class="recommended__title">'.$recommendedEvents['title'].'</span>',
            $url,
            [
              'class' => 'recommended__link',
              'ic-get-from' => $url,
              'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
            ]
          ),
          ['class' => 'u-window-box--small']
        ),
        ['class' => 'o-grid__cell o-grid__cell--no-gutter']
      );
  }

  // SIMILAR EVENTS WRAP END
  echo Html::endTag('div');
  echo Html::endTag('div');
}

// SHARE LINE
echo
Html::tag('div',
  Html::tag('div',
    Html::tag('div',

      //sharetext
      Html::tag('div',
        Yii::t('app', 'Enjoyed reading this stuff? Tell your man to check it out too or leave your opinion!'),
        ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--center o-grid__cell--width-75']
      ).

      //buttons
      Html::tag('div',
        Html::tag('div',
          ElementsHelper::linkElement('comment', $modelEvents[0]['countComments'], Url::toRoute($modelEvents[0]['id']), 'zmdi-comment-text-alt').
          ElementsHelper::likeButton(Yii::$app->controller->id, $modelEvents[0]['id'], $modelEvents[0]['likesCount'], $modelEvents[0]['myLike']).
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

# Comments
echo WidgetComments::widget([
  'elem_id' => $modelEvents[0]['id'] ?? ''
]);


/* JS: @see js/outstyle.portal.event.js */
?>
<script>jQuery(document).ready(function(){eventInit();});</script>
