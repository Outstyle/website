function articlesGridInit() {

  var checkboxes = jQuery('.checkbox__wrap'),
      fakeCheckboxes = jQuery('.checkbox__wrap--disabled');

  function init_articles() {

    way.restore();

    setTimeout(function(){
      echo.init({
        offset:1250,
        callback: function (element, op) {

          jQuery(element).load(function(){
            jQuery(element).next("img.article__image--overlay").show();
          });

          /* --- If we have 'broken' images - hide 'em --- */
          jQuery(element).error(function(){
            jQuery(this).hide();
            jQuery(this).next("img.article__image--overlay").hide();
          });

          /* --- This stuff is needed for triggering 'scroll-on-view' element, so it could be on a user's viewport! --- */
          jQuery('#outstyle_articles .article__item--initial').hide();
        }
      });
    },250);

    /* --- Restoring from way.js storage and modifying elements --- */
    var categories = way.get("article.filter.categories");
    if (categories) {

      jQuery.each(categories, function(key, value) {

        /* For usual checkboxes */
        jQuery("#article-filter-form input[type=checkbox][value="+value+"]").attr("checked", true);
        jQuery("#article-filter-form input[type=checkbox][value="+value+"]").parent().addClass('active');
        jQuery("#article-filter-form div[data-fake-id="+value+"]").parent().addClass('active');

      });

    }

    /* When all the stuff is finally done, we can hide fake checks and show active ones */
    fakeCheckboxes.hide();
    checkboxes.show();

    /* --- Also we need to prepend filter containter back to prevent it's disappearing after AJAX call --- */
    jQuery("#articles-filter").prependTo("#outstyle_articles").css({
      'visibility':'visible'
    });

  }

  init_articles();

  /* --- Getting stored values from way.js storage before sending our ajax request --- */
  jQuery(document).off("beforeAjaxSend.ic").on("beforeAjaxSend.ic", function(event, settings) {

      var article = way.get("article.filter");
      if (article) {
          article = jQuery.param(article);
          settings.data = settings.data+'&'+article;
      }

  });

  /**
   * Working with masked checkboxes
   * Triggering on 'change' event and toggling element's classes to show 'fake' checkbox element
   * This is needed to prevent multiple AJAX sends
   * Basically this substitutes an elements to a fake 'noninteractable' elements during AJAX call
   *
   * Since we are having two filters with same behaviour, we must connect the events to trigger each other
   * See line 104
   */
  jQuery('.checkbox__wrap input[type=checkbox]').off('change').on('change', function() {

      var otherFilter = jQuery(this).val();
      var activeCheckbox = jQuery("#article-filter-form input[type=checkbox][value="+otherFilter+"]");

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
   * Triggering on 'article' event from ArticleController
   * See X-IC-Trigger headers: http://intercoolerjs.org/reference.html
   */
  jQuery("body").off("article").on("article", function(event, data) {
    if (data.page) {
      jQuery('#page').val(data.page);
    }

    init_articles();
  });

  /* Error handling: no user image is available */
  jQuery("img.avatar").on('error', function () {
    jQuery(this).unbind("error").attr("src", "/css/i/broken/avatar_128x128.png");
  });


}
