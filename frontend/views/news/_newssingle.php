<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\components\helpers\ElementsHelper;
use frontend\widgets\WidgetComments;

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
            'label' => $modelNews[0]['category'],
            'url' => [Yii::$app->controller->id.'/'.$modelNews[0]['categoryUrl']],
            'ic-get-from' => Url::toRoute(Yii::$app->controller->id.'/'.$modelNews[0]['categoryUrl']),
            'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
          ],
          [
            'label' => $modelNews[0]['name'],
          ],
      ],
    ]
  ),

  [
    'class' => 'o-grid__cell o-grid__cell--width-100',
  ]
);

// RECOMMENDED NEWS
if (isset($modelNews[0]['recommended'])) {

    // SEPARATOR
    echo ElementsHelper::separatorDiamond(Yii::t('app', 'Recommended'));

    echo Html::beginTag('div',
      [
        'id' => 'news-single-recommended',
        'class' => 'o-grid o-grid--wrap u-full-width u-window-box--medium recommended',
      ]
    );

    foreach ($modelNews[0]['recommended'] as $recommendedNews) {
        $url = Url::toRoute($recommendedNews['url']);

      /* Recommended news single instance */
      echo
      Html::tag('div',
        Html::tag('div',
          Html::a('<img class="o-image grayscale" src="'.$recommendedNews['img'].'"><span class="recommended__title">'.$recommendedNews['name'].'</span>',
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

    echo Html::endTag('div');
}

// AUTHORBOX WITH LIKES AND HASHTAGS
echo
Html::tag('div',
  Html::tag('div',
    Html::tag('div',

      //cell 1
      Html::tag('div',

        //category circle icon
        Html::tag('i',
          '',
          ['class' => "zmdi zmdi-circle color-{$modelNews[0]['categoryUrl']}"]
        ).

        //category name
        Html::tag('span',
          Yii::t('app', $modelNews[0]['category']),
          ['class' => 'authorbox__category']
        ).

        //time icon
        Html::tag('i',
          '',
          ['class' => 'zmdi zmdi-time']
        ).

        //time text
        Html::tag('span',
          $modelNews[0]['created'],
          ['class' => 'authorbox__date']
        ),

        ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--bottom o-grid__cell--width-25 color-default u-l']
      ).

      //cell 2
      Html::tag('div',

        //username and avatar GET ACTIVE AVATAR to model
        Html::a(
          Html::img(
            $modelNews[0]['userAvatar'],
            [
              'class' => "roundborder color-{$modelNews[0]['userCulture']}--border avatar avatar--smallest",
              'alt' => Yii::t('app', 'Аватар пользователя {user}', ['user' => $modelNews[0]['userName']]),
            ]
          ).
          $modelNews[0]['userName'],
          //Url::toRoute('profile/'.$modelNews[0]['userId']),
          'javascript:void(0)',
          [
            'class' => "user-name user-name--withavatar color-{$modelNews[0]['userCulture']} news__author",
          ]
        ).

        //hashtags
        Html::tag('div',
          /*HashtagHelper::processHashtagsFromText($news['text'])*/'#hashTag1 #hashTag 2',
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
          ElementsHelper::linkElement('comment', $modelNews[0]['countComments'], '#comments_section', 'zmdi-comment-text-alt').
          ElementsHelper::likeButton(Yii::$app->controller->id, $modelNews[0]['id'], $modelNews[0]['likesCount'], $modelNews[0]['myLike']),
          ['class' => 'news__actions u-pull-left']
        ).
        Html::tag('div',
          ElementsHelper::linkElement('repost', '', '#', 'icon-megaphone'),
          ['class' => 'news__actions u-pull-right']
        ),

        ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--bottom o-grid__cell--width-25 color-default u-r authorbox__actionbox']
      ),

      ['class' => 'o-grid o-grid--wrap o-grid--center u-window-box--large u-bordered-box u-bordered-box--topbottom authorbox']
    ),
    ['class' => 'u-pillar-box--super']
  ),
  ['class' => 'o-grid__cell o-grid__cell--width-100']
);

// MAIN TITLE AND TEXT
echo
Html::tag('div',
  Html::tag('div',
    Html::tag('h1',
      $modelNews[0]['name'],
      ['class' => 'news-single__title u-c']
    ).
    Html::tag('div',
      $modelNews[0]['text'],
      ['class' => 'news-single__text']
    ),
    ['class' => 'u-window-box--super']
  ),
  ['class' => 'o-grid__cell o-grid__cell--width-100 c-text--usual news-single__body']
);

// SIMILAR NEWS
if (isset($modelNews[0]['similar'])) {

  // SEPARATOR
  ElementsHelper::separatorDiamond(Yii::t('app', 'Similar news'));

    echo Html::beginTag('div',
      [
        'id' => 'news-single-similar',
        'class' => 'o-grid o-grid--wrap u-full-width u-window-box--medium similar',
      ]
    );

    foreach ($modelNews[0]['similar'] as $similarNews) {
        $url = Url::toRoute($similarNews['url']);

        //similar news single instance
        echo
        Html::tag('div',
          Html::tag('div',
            Html::a('<img class="o-image grayscale" src="'.$similarNews['img'].'"><span class="similar__title">'.$similarNews['name'].'</span>',
              $url,
              [
                'class' => 'similar__link',
                'ic-get-from' => $url,
                'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
              ]
            ),
            ['class' => 'u-window-box--small']
          ),
          ['class' => 'o-grid__cell o-grid__cell--no-gutter']
        );
    }

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
          ElementsHelper::linkElement('comment', $modelNews[0]['countComments'], Url::toRoute($modelNews[0]['url']), 'zmdi-comment-text-alt').
          ElementsHelper::likeButton(Yii::$app->controller->id, $modelNews[0]['id'], $modelNews[0]['likesCount'], $modelNews[0]['myLike']).
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
  'elem_id' => $modelNews[0]['id'] ?? ''
]);

/* JS: @see js/outstyle.portal.news.js */
?>
<script>jQuery(document).ready(function(){newsInit();});</script>
