<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\HashtagHelper;
use common\components\helpers\StringHelper;
use common\components\helpers\ElementsHelper;

/*
 * Single news block view, representing an article
 * This is a partial view file.
 *
 * @var views\article\index
 */

/* Check if our items are 'first-generation' ones :D This is needed for removing them after AJAX calls */
$itemClass = '';

if ($page == 1) {
    $itemClass = ' article__item--initial';
}

if (isset($modelNews)) {
    echo ElementsHelper::loadMore(Url::toRoute('article/show'), '#outstyle_articles .articles__body', '{"page":'.(int) $page.',"category":'.(int) $category.'}');
    foreach ($modelNews as $article) {
        echo

        /*
         * Article picture block
         */

        //article box begin
        Html::beginTag('div', [
          'class' => 'o-grid o-grid--wrap article__item article__box'.$itemClass,
        ]),
            //article box image wrap
            Html::beginTag('div',[
                    'class'=>'o-grid__cell article__image__wrap'
            ]),



              //article box image cell
              Html::tag('div',


                ElementsHelper::linkElement('link',
                  //article image
                  Html::img(
                    Yii::$app->params['preloaderPictureBase64'],
                    [
                      'class' => 'o-image article__image',
                      'alt' => Html::encode($article['name']),
                      'data-echo' => $article['img'],
                    ]
                  ).



                  //article image overlay (border)
                  Html::img(
                    Url::toRoute('/css/i/outstyle_article_overlay.png'),
                    [
                      'class' => 'o-image noselect article__image--overlay',
                      'title' => Html::encode($article['name']),
                      'draggable' => 'false',
                    ]
                  ),

                  Url::toRoute($article['url'])).
                //mobile repost button and category article
                Html::beginTag('div',[
                    'class'=>'o-grid__cell o-grid__cell--bottom o-grid__cell--width-100 mobile'
                ]).
                  Html::tag('div',
                        ElementsHelper::linkElement('repost', '', '#', 'icon-megaphone'),
                        [
                            'class' => 'u-pull-right',
                        ]
                    ).
                  Html::tag('div',

                      # Category circle icon
                      Html::tag('i',
                          '',
                          ['class' => "zmdi zmdi-circle color-{$article['categoryUrl']}"]
                      ).

                      # Category link
                      ElementsHelper::linkElement('category', $article['category'], Url::toRoute(Yii::$app->controller->id.'/'.$article['categoryUrl']), false),

                      [
                          'class' => 'article__category u-pull-left',
                      ]
                  ).

                  Html::endTag('div'),

                [
                  'class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-fixed w-fixed-250 article__image_section',
                ]
              ),

            Html::endTag('div'),

          /*
           * Title and main text
           */

          Html::tag('div',
            Html::tag('div',
              Html::tag('div',

                //title
                Html::tag('h2',
                  ElementsHelper::linkElement('title', $article['name'], Url::toRoute($article['url'])),
                  []).

                //main text
                Html::tag('p',
                  strip_tags(StringHelper::cutString($article['text'], 520), '<code><p>'),
                [
                  'class' => 'article__text',
                ]).
                //mobile hashtags
                Html::beginTag('div',[
                    'class'=>'mobile color-default u-pull-right'
                ]).
                Html::tag('p',
                    /*HashtagHelper::processHashtagsFromText($article['text'])*/'#hashTag1 #hashTag 2',
                    [
                        'class' => 'hashtags',
                    ]
                ).
                Html::endTag('div').
                //mobile action buttons (views,like,comment)
                Html::tag('div',
                    ElementsHelper::linkElement('views', 0, false, 'zmdi-eye').
                    ElementsHelper::linkElement('comment', $article['countComments'], Url::toRoute($article['url']), 'zmdi-comment-text-alt').
                    ElementsHelper::likeButton(Yii::$app->controller->id, $article['id'], $article['likesCount'], $article['myLike']),
                    [
                        'class' => 'mobile article__actions u-pull-left',
                    ]
                ),

              [
                'class' => 'o-grid__cell o-grid__cell--width-100',
              ]).

              //datetime and category
              Html::tag('div',
                Html::tag('div',

                  # Category circle icon
                  Html::tag('i',
                    '',
                    ['class' => "zmdi zmdi-circle color-{$article['categoryUrl']}"]
                  ).

                  # Category link
                  ElementsHelper::linkElement('category', $article['category'], Url::toRoute(Yii::$app->controller->id.'/'.$article['categoryUrl']), false),

                  [
                    'class' => 'article__category u-pull-left',
                  ]
                ).

                Html::tag('div', $article['created'], ['class' => 'article__date u-pull-right']).
                //mobile avatar and author name
                Html::a(
                    Html::img(
                        $article['userAvatar'],
                        [
                            'class' => "roundborder color-{$article['userCulture']}--border avatar avatar--smallest",
                            'alt' => Yii::t('app', 'Аватар пользователя {user}', ['user' => $article['userName']]),
                        ]
                    ).
                    $article['userName'],
                    Url::toRoute('profile/'.$article['userId']),
                    [
                        'class' => "user-name user-name--withavatar color-{$article['userCulture']} article__author u-pull-left mobile",
                    ]
                ) ,
              [
                'class' => 'o-grid__cell o-grid__cell--bottom o-grid__cell--width-100 color-default article__misc',
              ]),

            [
              'class' => 'o-grid o-grid--no-gutter o-grid--wrap article__excerpt',
            ]),
          [
            'class' => 'o-grid__cell article__body',
          ]),

          /*
           * Author and tags
           */

          Html::tag('div',
            Html::tag('div',
              Html::tag('div',

                //author avatar and link
                Html::a(
                  Html::img(
                    $article['userAvatar'],
                    [
                      'class' => "roundborder color-{$article['userCulture']}--border avatar avatar--smallest",
                      'alt' => Yii::t('app', 'Аватар пользователя {user}', ['user' => $article['userName']]),
                    ]
                  ).
                  $article['userName'],
                  Url::toRoute('profile/'.$article['userId']),
                  [
                    'class' => "user-name user-name--withavatar color-{$article['userCulture']} article__author",
                  ]
                ).

                //hashtags
                Html::tag('p',
                  /*HashtagHelper::processHashtagsFromText($article['text'])*/'#hashTag1 #hashTag 2',
                  [
                    'class' => 'hashtags',
                  ]
                ),

                [
                  'class' => 'o-grid__cell o-grid__cell--width-100',
                ]
              ).

              //actions box (like, view count, comment buttons)
              Html::tag('div',
                Html::tag('div',
                  ElementsHelper::linkElement('views', 0, false, 'zmdi-eye').
                  ElementsHelper::linkElement('comment', $article['countComments'], Url::toRoute($article['url']), 'zmdi-comment-text-alt').
                  ElementsHelper::likeButton(Yii::$app->controller->id, $article['id'], $article['likesCount'], $article['myLike']),
                  [
                    'class' => 'article__actions u-pull-left',
                  ]
                ).
                Html::tag('div',
                  ElementsHelper::linkElement('repost', '', '#', 'icon-megaphone'),
                  [
                    'class' => 'u-pull-right',
                  ]
                ),
                [
                  'class' => 'o-grid__cell o-grid__cell--bottom article__actionbox o-grid__cell--width-100',
                ]
              ),

              [
                'class' => 'o-grid o-grid--wrap color-default article__info w-fixed-250 h-fixed-250',
              ]
            ),
            [
              'class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-fixed article__author__section',
            ]
          ),
        Html::endTag('div');
    }
}

/* JS: @see js/outstyle.portal.article.js */
?>
<script>jQuery(document).ready(function(){articleInit();});</script>
