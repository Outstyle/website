function userFriendsAvatarsInit() {

  /* Error handling: no user image is available */
  jQuery("img.friend__avatar").error(function () {
    jQuery(this).unbind("error").attr("src", "/css/i/broken/avatar_128x128.png");
  });
}
