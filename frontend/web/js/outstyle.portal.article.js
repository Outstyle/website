function articleInit() {

  /* Error handling: no user image is available */
  jQuery("img.avatar").on('error', function () {
    jQuery(this).unbind("error").attr("src", "/css/i/broken/avatar_128x128.png");
  });


}
