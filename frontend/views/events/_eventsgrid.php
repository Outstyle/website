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
      echo Html::beginForm('', '',
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

    echo ElementsHelper::separatorDiamond('<h1>'.Yii::t('seo', Yii::$app->controller->id.'.h1').'</h1>');
} else {
    echo
   Html::tag('div',

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
               'url' => ['/'.Yii::$app->controller->id],
               'ic-get-from' => Url::to('/'.Yii::$app->controller->id),
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
            echo ElementsHelper::separatorDiamond('<h1>'.Yii::t('seo', Yii::$app->controller->id.'.'.$c->url.'.h1').'</h1>');
        }
    }
}

echo Html::a('<i class="zmdi zmdi-plus-circle-o zmdi-hc-3x"></i>', '#googleforms_add_event',
  [
    'class' => 'btn btn__addnew roundcorners modal-open',
    'title' => 'Предложить событие'
  ]);
echo $this->render('@modals/google/GoogleFormsAddEvent');

/*
* --- Single event block ---
*/

echo
Html::tag('div',
  Html::tag('div',
    $this->render('_eventsblock',
      [
        'modelEvents' => $modelEvents,
        'page' => $page,
        'category' => $category,
      ]),
    ['class' => 'u-window-box--super events__body']),
['class' => 'o-grid__cell o-grid__cell--width-100 events-single']);

/* This input is for sending pages */
echo Html::hiddenInput('page', $page, ['id' => 'page']);

/*
    JS stuff, that is related ONLY to this view
    Used:
    - echoJS for lazy load images:      https://www.npmjs.com/package/echo-js
    - PreciseTextResize for text:       @frontend/web/js/misc/preciseTextResize.js
    - wayjs for two-way data-binding:   https://github.com/gwendall/way.js

    !!! IMPORTANT !!! DON'T FORGET to switch off active events to prevent event binding duplication! (make event .off().on())
*/
?>
<script>
jQuery(document).ready(function () {

  var checkboxes = jQuery('.checkbox__wrap'),
      fakeCheckboxes = jQuery('.checkbox__wrap--disabled');

  function init_events() {

    way.restore();

    jQuery('.event__title').preciseTextResize({
      parent: '.event__title-wrap',
      grid : [{
        0 : {125 : {1:46,4:40,6:34,10:32,15:26,20:24,25:21,30:18,35:16}}
      }],
    });

    setTimeout(function(){
      echo.init({
        offset:1250,
        callback: function (element, op) {

          jQuery(element).load(function(){
            jQuery(element).next("img.events__image--overlay").show();
          });

          /* --- If we have 'broken' images - hide 'em --- */
          jQuery(element).error(function(){
            jQuery(this).hide().next("img.events__image--overlay").hide();
          });

          /* --- This stuff is needed for triggering 'scroll-on-view' element, so it could be on a user's viewport! --- */
          jQuery('#outstyle_events .event__item--initial').hide();
        }
      });
    },250);

    /* --- Restoring from way.js storage and modifying elements --- */
    var categories = way.get("events.filter.categories");
    if (categories) {

      jQuery.each(categories, function(key, value) {

        /* For usual checkboxes */
        jQuery("#events-filter-form input[type=checkbox][value="+value+"]").attr("checked", true);
        jQuery("#events-filter-form input[type=checkbox][value="+value+"]").parent().addClass('active');
        jQuery("#events-filter-form div[data-fake-id="+value+"]").parent().addClass('active');

      });

    }

    /* When all the stuff is finally done, we can hide fake checks and show active ones */
    fakeCheckboxes.hide();
    checkboxes.show();

    /* --- Also we need to prepend filter containter back to prevent it's disappearing after AJAX call --- */
    jQuery("#events-filter").prependTo("#outstyle_events").css({
      'visibility':'visible'
    });

  }

  init_events();

  /* --- Getting stored values from way.js storage before sending our ajax request --- */
  jQuery(document).off("beforeAjaxSend.ic").on("beforeAjaxSend.ic", function(event, settings) {

      var events = way.get("events.filter");
      if (events) {
          events = jQuery.param(events);
          settings.data = settings.data+'&'+events;
      }

  });

  /**
   * Working with masked checkboxes
   * Triggering on 'change' event and toggling element's classes to show 'fake' checkbox element
   * This is needed to prevent multiple AJAX sends
   * Basically this substitutes an elements to a fake 'noninteractable' elements during AJAX call
   *
   * Since we are having two filters with same behaviour, we must connect the events to trigger each other
   */
  jQuery('.checkbox__wrap input[type=checkbox]').off('change').on('change', function() {

      var otherFilter = jQuery(this).val();
      var activeCheckbox = jQuery("#events-filter-form input[type=checkbox][value="+otherFilter+"]");

      if (jQuery(this).is(":checked")) {
        jQuery(this).parent().addClass('active');
        jQuery(this).parent().next().addClass('active');
        activeCheckbox.next('i').removeClass('zmdi-circle-o').addClass('zmdi-circle');
      } else {
        jQuery(this).parent().removeClass('active');
        jQuery(this).parent().next().removeClass('active');
        activeCheckbox.next('i').removeClass('zmdi-circle').addClass('zmdi-circle-o');
      }
      /* --- Preventing other checkboxes to be triggered, while AJAX request is active --- */
      checkboxes.hide();
      fakeCheckboxes.show();

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
