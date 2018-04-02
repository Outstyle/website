<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\ElementsHelper;

/*
 * Single news block view
 *
 * For triggering 'loading more' event we're using Intercooler:
 * http://intercoolerjs.org/examples/infinitescroll.html in last element's 'news__footer' div
 */

$controllerId = Yii::$app->controller->id;
$itemClass = ''; // initial items marker (needed for packeryjs) /* TODO: Get rid of this crap */

if (isset($modelNews)) {

    /* Check if our items are 'first-generation' ones :D This is needed for removing them after AJAX calls */
    if ($page == 1) {
        $itemClass = ' news__item--initial';
    }

    foreach ($modelNews as $news) {

        /* Base vars for each news instance */
        $newsUrl = Url::toRoute($news['url']);
        $newsCategoryUrl = Url::toRoute($controllerId.'/'.$news['categoryUrl']);

        if ($newsUrl) {
            echo
            Html::tag('div',

              # image
              Html::img(
                Yii::$app->params['preloaderPictureBase64'],
                [
                  'class'       => "{$controllerId}__image block__image o-image",
                  'alt'         => Html::encode($news['title']),
                  'data-echo'   => $news['img']
                ]
              ).

              # overlay with all the elements
              Html::tag('div',

                # element: filter button
                Html::tag('div',
                  '',
                  [
                    'class' => "{$controllerId}__filter-button block__filter-button color-{$news['categoryUrl']}--bg "
                  ]
                ).

                # element: category title
                Html::tag('div',
                  ElementsHelper::linkElement('category', $news['category'], $newsCategoryUrl, false),
                  [
                    'class' => "{$controllerId}__category block__category u-window-box--small c-text--shadow"
                  ]
                ).

                # element: link to single instance of school
                ElementsHelper::linkElement('title', $news['title'], $newsUrl).

                # element: separator/divider [horizontal]
                Html::tag('hr', '', ['class' => 'u-letter-box--small']).

                # element: block body with main text
                Html::tag('div',
                  $news['text'] ?? '',
                  [
                    'class' => "{$controllerId}__body block__body u-pillar-box--medium c-text--darkshadow"
                  ]
                ).

                # element: block footer
                Html::tag('div',

                  # actions box (like, view count, comment buttons)
                  Html::tag('div',
                    Html::tag('div',
                      ElementsHelper::likeButton($controllerId, $news['id'], $news['likesCount'], $news['myLike']),
                      ['class' => "u-pull-left"]
                    ).
                    Html::tag('div',
                      ElementsHelper::linkElement('comment', $news['countComments'], $newsUrl, 'zmdi-comment-text-alt zmdi-hc-small'),
                      ['class' => "u-pull-left u-pillar-box--large"]
                    ).
                    Html::tag('div',
                      ElementsHelper::linkElement('readmore', false, $newsUrl, 'zmdi-chevron-right zmdi-hc-lg'),
                      ['class' => 'u-pull-right']
                    ),
                    ['class' => "{$controllerId}__actionbox block__actionbox"]
                  ),
                  ['class' => "{$controllerId}__footer block__footer u-window-box--medium c-text--darkshadow o-shadow-bottomtop"]
                ),

                ['class' => "{$controllerId}__overlay overlay"]
              ),
              ['class' => "o-grid__cell o-grid__cell--{$news['img_block_size']} {$controllerId}__item{$itemClass} {$controllerId}__item--{$news['categoryUrl']} block__item block__item--bordered"]
            );
        }
    }

    if ($page) {
      if($outstyle_news_height) {
        $outstyle_news_height = $outstyle_news_height-1300;
      }
      if ($page == 1) {
            $outstyle_news_height=10;
      }
        echo
        Html::tag('div',
          ElementsHelper::loadMore(Url::toRoute('news/show'), '#outstyle_news .news', '{"page":'.(int) $page.',"category":'.(int) $category.'}'),
          ['style' => "top:{$outstyle_news_height}px;position:absolute;z-index:10000;"]
        );
    }

}
