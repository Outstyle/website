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
            'id' => 'events-filter-form',
            'way-data' => 'events.filter',
            'way-persistent' => 'true',
        ]
    );

    /*
      * Getting all the categories for filtering
      * @var $modelNews    common/models/News  -> getNews()
      */
    foreach ($eventsCategories as $c) {
        echo ElementsHelper::ajaxedCheckbox('categories[]', $c->id, Yii::t('app', $c->name), Url::toRoute('events/show'), '#outstyle_events .events__body', '#events-filter-form');
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
                ]
            ),
            ['class' => 'u-window-box--super events__body']
        ),
        ['class' => 'o-grid__cell o-grid__cell--width-100 events-single']
    );

/* This input is for sending pages */
echo Html::hiddenInput('page', $page, ['id' => 'page']);

?>
<script>
    jQuery(document).ready(function() {

        function init_events() {

            jQuery('.event__title').preciseTextResize({
                parent: '.event__title-wrap',
                grid: [{
                    0: {
                        125: {
                            1: 42,
                            4: 32,
                            6: 26,
                            10: 24,
                            13: 22,
                            15: 21,
                            20: 20,
                            25: 18,
                            30: 16,
                            35: 14
                        }
                    },
                }],
            });


            /* --- Also we need to prepend filter containter back to prevent it's disappearing after AJAX call --- */
            jQuery("#events-filter").prependTo("#outstyle_events").css({
                'visibility': 'visible'
            });

        }

        init_events();

        /* --- Getting stored values from way.js storage before sending our ajax request --- */
        jQuery(document).off("beforeAjaxSend.ic").on("beforeAjaxSend.ic", function(event, settings) {

            var events = way.get("events.filter");
            if (events) {
                events = jQuery.param(events);
                settings.data = settings.data + '&' + events;
            }

        });


        /**
         * Triggering on 'events' event from ArticleController
         * See X-IC-Trigger headers: http://intercoolerjs.org/reference.html
         */
        jQuery("body").off("events").on("events", function(event, data) {
            if (data.page) {
                jQuery('#page').val(data.page);
            }

            init_events();

        });

    });
</script>