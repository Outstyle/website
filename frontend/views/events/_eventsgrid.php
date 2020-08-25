<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\components\helpers\ElementsHelper;

/*
 * Events grid file
 * This is a partial view file.
 *
 * @var $modelEvents          views\events\index
 * @var $modelCategories      views\events\index
 * @var $modelPage            views\events\index
 */

/*
 * Filter box form wrap for events (checkboxes)
 */

if (empty($category)) {
    echo
        Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter', 'id' => 'events-filter']);

    // Filter form with way-data (https://github.com/gwendall/way.js)
    echo Html::beginForm(
        '',
        '',
        [
            'id' => 'filter-form'
        ]
    );

    /*
      * Getting all the categories for filtering
      * @var $modelNews    common/models/News  -> getNews()
      */
    foreach ($eventsCategories as $c) {
        echo ElementsHelper::ajaxedCheckbox('categories[]', $c->id, Yii::t('app', $c->name), Url::toRoute('events/show'), '#outstyle_events .events__body', '#filter-form');
    }

    echo Html::endForm(),
        Html::endTag('div');

    echo ElementsHelper::separatorDiamond('<h1>' . Yii::t('seo', Yii::$app->controller->id . '.h1') . '</h1>');
} else {
    echo
        Html::tag(
            'div',

            /* BREADCRUMBS */
            Breadcrumbs::widget(
                [
                    'tag' => 'ol',
                    'homeLink' => false,
                    'options' => ['class' => 'c-breadcrumbs u-cf'],
                    'itemTemplate' => '<li class="c-breadcrumbs__crumb">{link}<i class="icon-right-open-big zmdi-hc-small"></i></li>',
                    'links' => [
                        [
                            'label' => Yii::t('app', ucfirst(Yii::$app->controller->id)),
                            'url' => ['/' . Yii::$app->controller->id],
                            'ic-get-from' => Url::to('/' . Yii::$app->controller->id),
                            'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
                        ],
                        [
                            'label' => $modelEvents[0]['category'],
                        ],
                    ],
                ]
            ),

            [
                'class' => 'o-grid__cell o-grid__cell--width-100',
            ]
        );

    foreach ($eventsCategories as $c) {
        if ($c->id == $category) {
            echo ElementsHelper::separatorDiamond('<h1>' . Yii::t('seo', Yii::$app->controller->id . '.' . $c->url . '.h1') . '</h1>');
        }
    }
}

// echo Html::a(
//     '<i class="zmdi zmdi-plus-circle-o zmdi-hc-3x"></i>',
//     '#googleforms_add_event',
//     [
//         'class' => 'btn btn__addnew roundcorners modal-open',
//         'title' => 'Предложить событие'
//     ]
// );
// echo $this->render('@modals/google/GoogleFormsAddEvent');

/*
* --- Single event block ---
*/

echo
    Html::tag(
        'div',
        Html::tag(
            'div',
            $this->render(
                '_eventsblock',
                [
                    'modelEvents' => $modelEvents,
                    'page' => $page,
                    'category' => $category,
                    'contentHeight' => $contentHeight,
                ]
            ),
            ['class' => 'u-window-box--super events__body']
        ),
        ['class' => 'o-grid__cell o-grid__cell--width-100 events-single']
    );

/* This input is for sending pages */
echo Html::hiddenInput('page', $page, ['id' => 'page']);

/* This input is needed for smooth Packery init after each AJAX call */
echo Html::hiddenInput('contentHeight', '', ['id' => 'contentHeight']);

/* Pass controller ID for JS to rely on */
echo '<script>var CURRENT_CONTROLLER_ID = "' . Yii::$app->controller->id . '";</script>';
