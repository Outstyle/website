<?php
use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\ElementsHelper;

/**
 * Single school block view
 */

$controllerId = Yii::$app->controller->id;
$itemClass = ''; /* TODO: Get rid of this crap */

/**
 * We need to add some sort of 'block', that is represented as a 'filter' (UI/UX req)
 * Since we are having Packery initiated, that block must be added manually
 *
 * Although, we won't do anything, if the element in array is the only one (single)
 */

if (isset($model[1])) {
    array_splice($model, 1, 0, array($model[1]));
    foreach ($model[1] as $k => $v) {
        $model[1][$k] = '';
    }
}


if (isset($model)) {

    if ($page) {
      if ($page_height) {
        $page_height = $page_height-500;
      }
      if ($page == 1) {
        $page_height = 10;
      }
        echo
        Html::tag('div',
          ElementsHelper::loadMore(Url::toRoute('school/show'), '#outstyle_school .school', '{"page":'.(int) $page.',"category":'.(int) $category.'}'),
          ['style' => "top:{$page_height}px;position:absolute;z-index:10000;"]
        );
    }

    /* Check if our items are 'first-generation' ones :D This is needed for removing them after AJAX calls --- TODO: REDO */
    if ($page == 1) {
        $itemClass = ' school__item--initial';
    }

    foreach ($model as $position => $school) {


        /* --- We need to have predefined sizes for our grid here --- */
        if ($position == 0) {
            $school['img_block_size'] = '500x500';
        }

        /**
         * We also need to add some sort of 'block', that is represented as a filter
         * Since we are having Packery initiated, that block must be added manually
         */
        if ($position == 1) {
            $school['img_block_size'] = '500x250';
        }

        /* Base vars for each school instance */
        $schoolUrl = Url::toRoute($school['id']);

        # if we have school ID set, show block
        if ($schoolUrl) {
            echo
            Html::tag('div',

              # image
              Html::img(
                Yii::$app->params['preloaderPictureBase64'],
                [
                  'class'       => "{$controllerId}__image block__image o-image",
                  'alt'         => Html::encode($school['title']),
                  'data-echo'   => $school['img']
                ]
              ).

              # overlay with all the elements
              Html::tag('div',

                # element: filter button
                Html::tag('div',
                  '',
                  [
                    'class' => "{$controllerId}__filter-button block__filter-button color-{$school['categoryUrl']}--bg "
                  ]
                ).

                # element: category title
                Html::tag('div',
                  ElementsHelper::linkElement('category', $school['category'], $schoolUrl, false),
                  [
                    'class' => "{$controllerId}__category block__category u-window-box--small c-text--shadow"
                  ]
                ).

                # element: link to single instance of school
                ElementsHelper::linkElement('title', $school['title'], $schoolUrl).

                # element: separator/divider [horizontal]
                Html::tag('hr', '', ['class' => 'u-letter-box--small']).

                # element: block body with main text/school description
                Html::tag('div',
                  $school['description'] ?? '',
                  [
                    'class' => "{$controllerId}__body block__body u-pillar-box--medium c-text--darkshadow"
                  ]
                ).

                # element: block footer
                Html::tag('div',

                  # actions box (like, view count, comment buttons)
                  Html::tag('div',
                    Html::tag('div',
                      ElementsHelper::likeButton($controllerId, $school['id'], $school['likesCount'], $school['myLike']),
                      ['class' => "u-pull-left"]
                    ).
                    Html::tag('div',
                      ElementsHelper::linkElement('comment', $school['countComments'], $schoolUrl, 'zmdi-comment-text-alt zmdi-hc-small'),
                      ['class' => "u-pull-left u-pillar-box--large"]
                    ).
                    Html::tag('div',
                      ElementsHelper::linkElement('readmore', false, $schoolUrl, 'zmdi-chevron-right zmdi-hc-lg'),
                      ['class' => 'u-pull-right']
                    ),
                    ['class' => "{$controllerId}__actionbox block__actionbox"]
                  ),
                  ['class' => "{$controllerId}__footer block__footer u-window-box--medium c-text--darkshadow o-shadow-bottomtop"]
                ),

                ['class' => "{$controllerId}__overlay overlay"]
              ),
              ['class' => "o-grid__cell o-grid__cell--{$school['img_block_size']} {$controllerId}__item{$itemClass} {$controllerId}__item--{$school['categoryUrl']} block__item block__item--bordered"]
            );

        # space for cities/towns filter
        } else {
            echo
            Html::tag('div',
              '',
              [
                'id' => 'block__filter--empty',
                'class' => "o-grid__cell o-grid__cell--{$school['img_block_size']} {$controllerId}__item{$itemClass} {$controllerId}__item--{$school['categoryUrl']} block__item block__item--bordered block__filter--empty"
              ]
            );
        }
    }
}
