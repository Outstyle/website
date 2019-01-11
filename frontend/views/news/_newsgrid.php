<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\ElementsHelper;

/* --- Filter block, that shows up on 'news__filter-button' click event
TODO: $include should be avoided - instead need to put child elements into ::filterBox itself
 --- */
if (empty($category)) {
    echo ElementsHelper::filterBox($newsCategories, 'categories[]', Url::toRoute('news/show'), $target_el = '#outstyle_news .news', $include = '#outstyle_news_height,#news-filter-form');
    echo '<h1>'.Yii::t('seo', Yii::$app->controller->id.'.h1').'</h1>';
} else {
    foreach ($newsCategories as $c) {
        if ($c->id == $category) {
            echo '<h1>'.Yii::t('seo', Yii::$app->controller->id.'.'.$c->url.'.h1').'</h1>';
        }
    }
}

/* --- ONE NEWS BLOCK --- */
echo $this->render('_newsblock', [
  'modelNews' => $modelNews,
  'page' => $page,
  'outstyle_news_height' => $outstyle_news_height,
  'category' => $category,
]);

/* This input is needed for smooth Packery init after each AJAX call */
echo Html::hiddenInput('outstyle_news_height', '', ['id' => 'outstyle_news_height']);

/* This input is for sending pages */
echo Html::hiddenInput('page', $page, ['id' => 'page']);

/*
    JS stuff, that is related ONLY to this view
    Used:
    - echoJS for lazy load images:      https://www.npmjs.com/package/echo-js
    - packery for grid layout:          http://packery.metafizzy.co/
    - PreciseTextResize for text:       @frontend/web/js/misc/preciseTextResize.js
    - wayjs for two-way data-binding:   https://github.com/gwendall/way.js

    TODO: Redo this using ic-scroll-offset: http://intercoolerjs.org/attributes/ic-scroll-offset.html
    I also need to mention, that we have some really big stuck with Packery+'scrolled-in-view' event for loading more news
    So the possible solution could be in destroying Packery instance every time after ajax event and setting it up again instead 'reloadItems'
    Otherwise we will get continious AJAX requests.

    jQuery "news" trigger fires after ajaxComplete request when certain Intercooler header was accepted.
    See 'X-IC-Trigger' in 'NewsController'

    !!! IMPORTANT !!! DON'T FORGET to switch off active events to prevent event binding duplication! (make event .off().on())
*/

?>
<script>
jQuery(document).ready(function () {

  function init_news() {

    way.restore(); // https://github.com/gwendall/way.js - We need to restore our values for page

    /* --- This stuff is needed for triggering 'scroll-on-view' element, so it could be on a user's viewport! --- */
    jQuery('#outstyle_news .news__item--initial').css({'height':50,'width':50,'position':'static'}).hide();

    /* --- We need to initialize Packery at start --- */
    jQuery('#outstyle_news .news')
    .packery({
        itemSelector: '.news__item',
        gutter: 0,
        percentPosition: true
    })
    .packery('layout');

    /* --- If Packery has finally loaded - initiating some other events --- */
    jQuery('#outstyle_news .news').off('layoutComplete').on('layoutComplete', function() {
      /* --- Fit text size for each Packery block --- */
      jQuery('.news__title').preciseTextResize({
        parent: '.news__overlay',
        widthOffset: 1,
        heightOffset: 1
      });

      jQuery('.news__overlay').show();
      jQuery('#outstyle_news').css({
        'visibility':'visible'
      });
    });

    /* --- Now to init images lazy loading --- */
    echo.init({offset:1000});

    /* --- Bind some events to this page elements --- */
    jQuery(".news__filter-button").on("click", function() {
      jQuery(this).after(
        jQuery('#filter-box').slideDown('fast')
      );
    });
    jQuery('#news-filter-form input[type=checkbox]').on("change", function() {
      if(this.checked) {
        jQuery(this).next('i').removeClass('zmdi-circle-o').addClass('zmdi-circle');
      } else {
        jQuery(this).next('i').removeClass('zmdi-circle').addClass('zmdi-circle-o');
      }
    });

    /* --- Restoring from way.js storage after some timeout and modifying elements --- */
    var categories = way.get("news.filter.categories");
    if (categories) {
      jQuery.each(categories, function(key, value) {
        jQuery("#news-filter-form input[type=checkbox][value="+value+"]").attr("checked", true);
      });
    }
    jQuery("#news-filter-form input[type=checkbox]").each(function() {
      if(this.checked) {
        jQuery(this).next('i').removeClass('zmdi-circle-o').addClass('zmdi-circle');
      }
    });

    /* --- Finally hiding the preloader --- */
    jQuery("#cool_loader").hide();
  }

  init_news();

  /* --- Some bindings to the news view page --- */
  /* off.on is necessary to prevent event duplicate, when getting from another page to this one and back and so on */
  jQuery("body").off("news").on("news", function(event, data) {
    if (data.outstyle_news_height) {
      jQuery('#outstyle_news_height').val(data.outstyle_news_height);
      jQuery('#outstyle_news').css({'min-height':data.outstyle_news_height+'px'});
    }
    if (data.page) {
      jQuery('#page').val(data.page);
    }

    setTimeout(function(){
      jQuery('#outstyle_news .news')
      .packery()
      .packery('destroy');

      init_news();
    },50);

  });

  /* --- Before sending our Intercooler AJAX request, we check for stored values from way.js and pass them too --- */
  jQuery(document).off("beforeAjaxSend.ic").on("beforeAjaxSend.ic", function(event, settings) {

    var outstyle_news_height = jQuery('#content').height();
    settings.data = settings.data+'&outstyle_news_height='+outstyle_news_height;

    var categories = way.get("news.filter");
    if (categories) {
      categories = jQuery.param(categories);
      settings.data = settings.data+'&'+categories;
    }

    /* --- Also we need to prepend filter containter back to prevent it's disappearing after AJAX call --- */
    jQuery("#filter-box").prependTo("#outstyle_news").hide();

    /* --- Some neat loader for the news page, showing before each filtering event --- */
    if(jQuery('#cool_loader').length === 0) {
      jQuery("#outstyle_news").before('<img src="/frontend/web/images/images/breakdance_loader.gif" class="news__loader" id="cool_loader">');
    }
  });
});
</script>