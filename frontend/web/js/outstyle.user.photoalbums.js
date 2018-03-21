var photoalbums_area = '#albums_area';

/*
  Initialize photoalbums. This needs to be done after every AJAX call
  @see: photoalbum/index
*/
function photoalbumsInit() {

  /* Check if we already have our scrollbars initialized (we need to rely on setTimeout for history support) */
  /* @see: https://github.com/KingSora/OverlayScrollbars */
  setTimeout(function() {
    var photosAlbumsInstance = jQuery(photoalbums_area).overlayScrollbars({}).overlayScrollbars();
    photoalbumsTooltipsInit();
    uploadPhotoFormInit();

    /* Hide loader, show photoalbums area */
    jQuery(photoalbums_area)
      .show()
      .prev()
      .remove();

  }, 85);

  sidebarHighlightActiveMenuItem('#menu__item-photo');

}

/* On succesfull opening of album, when clicking the album div */
jQuery("body").on("photoalbumView", function(event, data) {

  photoalbumsInit();

  var activeOpenedAlbum = jQuery("#album-" + data.album);
  jQuery(photoalbums_area)
    .find('.album')
    .parent()
    .removeClass('album-active');
  activeOpenedAlbum
    .parent()
    .addClass('album-active');
});

/* Photoalbums history.back() events */
jQuery(document).on("beforeHistorySnapshot.ic", function(evt, target) {

  /* We need to destroy OverlayScrollbars instance in order to reinitialize it back from history cache */
  var photosAlbumsInstance = jQuery(photoalbums_area).overlayScrollbars({}).overlayScrollbars();
  if (typeof photosAlbumsInstance !== 'undefined') {
    photosAlbumsInstance.destroy();
  }

});