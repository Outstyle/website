var photoalbums_area = '#albums_area';
var photos_area = '#photos_area';

/**
 * Initialize photoalbums. This needs to be done after every AJAX call
 * @see: photoalbum/index
 */
function photoalbumsInit() {

  jQuery(photoalbums_area).show();

  setTimeout(function() {
    photoalbumsCalculateEqualHeight();
    photoalbumsTooltipsInit();
    uploadPhotoFormInit();
  }, 85);

  sidebarHighlightActiveMenuItem('#menu__item-photo');

}

/**
 * Init scrollbars for photoalbums area
 * @see: https://github.com/KingSora/OverlayScrollbars
 */
function photoalbumsScrollbarInit() {
  jQuery(photoalbums_area).overlayScrollbars({}).overlayScrollbars();
}

/**
 * Recalculates height for photoalbums sidebar and photos area so they could be equal (UI issues)
 */
function photoalbumsCalculateEqualHeight() {
  var h = window.innerHeight;
  var photosHeight = jQuery('.photoalbum__photos').height();
  photosHeight = photosHeight + 60;
  if (photosHeight > h) {
    jQuery(photoalbums_area + ',' + photos_area).css({
      'height': photosHeight + 'px'
    });
  } else {
    jQuery(photoalbums_area + ',' + photos_area).css({
      'height': 'calc(100vh - 45px)'
    });
  }
}

/**
 * On succesfull opening of album, when clicking the album div
 * FIXME: Get rid from setTimeout
 */
jQuery("body").on("photoalbumView", function(event, data) {
  var activeOpenedAlbum = jQuery("#album-" + data.album);
  jQuery(photoalbums_area)
    .find('.album')
    .parent()
    .removeClass('album-active');
  activeOpenedAlbum
    .parent()
    .addClass('album-active');

  setTimeout(function() {
    photoalbumsCalculateEqualHeight();
    photoalbumsTooltipsInit();
    uploadPhotoFormInit();
    var firstImageAsCover = jQuery('#photos_area').find('.user__photothumbnail:first-child').attr('src');
    activeOpenedAlbum.find('.album__cover').attr('src', firstImageAsCover);
  }, 85);

});

/**
 * On succesfull prepending of photoalbums
 * @see: http://intercoolerjs.org/attributes/ic-prepend-from.html
 * @see: PhotoalbumController -> actionGet()
 */
jQuery("body").on("photoalbumGet", function(event, data) {
  jQuery('#photoalbums__loadmore').hide();
  setTimeout(function() {
    photoalbumsCalculateEqualHeight();
    photoalbumsScrollbarInit();
  }, 100);
});

/**
 * On succesfull photos load more
 */
jQuery("body").on("photoalbumPhotosLoadMore", function(event, data) {
  setTimeout(function() {
    photoalbumsCalculateEqualHeight();
    photoalbumsScrollbarInit();
  }, 50);
});


/* Photoalbums history.back() events */
jQuery(document).on("beforeHistorySnapshot.ic", function(evt, target) {

  if (window.location.pathname.indexOf("/photos") !== 0) {
    closeAllModals();
    jQuery('#albums_area').empty();
    jQuery('#photo__editbutton').tooltipster('destroy');
    jQuery('#photoalbums__loadmore').show();
  }

});