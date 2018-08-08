function userFriendsAvatarsInit() {
  /* Error handling: no user image is available */
  jQuery("img.friend__avatar").on("error", function() {
    jQuery(this).unbind("error").attr("src", "/css/i/broken/avatar_128x128.png");
  });
}

jQuery("body").on("friendsFindError friendsFilterError", function(evt, data) {
  ohSnapX();
  jQuery.each(data, function(key, value) {
    ohSnap(decodeURIComponent(value).replace(/\+/g, " "), {
      'color': 'yellow'
    });
  });
});

function friendsBeforeSearchActions() {
  jQuery(".search__friends")
    .hide()
    .empty();
}

function friendsAfterSearchActions() {
  jQuery(".search__friends").show();
}


function friendsSearchFormInit() {

  jQuery('#friends-search-form').on('keypress', function(e) {
    if (e.keyCode == 13) {
      e.preventDefault();
      Intercooler.triggerRequest('#friends-search-form');
    }
  });

}