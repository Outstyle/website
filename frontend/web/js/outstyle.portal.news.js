function newsInit() {

  echo.init({
    offset:500,
    callback: function (element, op) {
      jQuery("img").error(function(){
        jQuery(this).hide();
      });
    }
  });

  jQuery("#news-single-recommended .grayscale, #news-single-similar .grayscale").hover(function() {
    jQuery(this).toggleClass('grayscale');
  });

  /* Error handling: no user image is available */
  jQuery("img.avatar").error(function () {
    jQuery(this).unbind("error").attr("src", "/css/i/broken/avatar_128x128.png");
  });

}
