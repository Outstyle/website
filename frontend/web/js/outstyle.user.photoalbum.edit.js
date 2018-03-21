var photoalbum_edit_area = '#photos_area .photoalbum__edit';
var photoalbum_edit_form = '#form-edit-photoalbum';
var photoalbum_area = '#albums_area';
var photoalbum_loader = '<div class="loader--smallest u-pull-right"></div>';

/* Edit photoalbum event FIXME TODO: Make this via IC, not jqAjax */
function photoalbumEditFormInit() {
  jQuery(photoalbum_edit_form).on('beforeSubmit', function(event, jqXHR, settings) {

    var form = jQuery(this);
    if (form.find('.has-error').length) {
      return false;
    }

    var csrfToken = jQuery('meta[name="csrf-token"]').attr("content");

    jQuery.ajax({
      url: form.attr('action'),
      type: 'post',
      data: form.serialize() + '&_csrf=' + csrfToken,

      beforeSend: function() {
        jQuery('#createphotoalbum-submit')
          .hide()
          .after(photoalbum_loader);
      },

      complete: function() {
        jQuery('#createphotoalbum-submit')
          .show()
          .next()
          .remove();
      },

      success: function(data, status, jqXHR) {

        var contentType = jqXHR.getResponseHeader('Content-type');

        /* IF data is JSON | TODO: Make all requests JSON, untie from HTML parts */
        if (contentType == 'application/json; charset=UTF-8') {

          /* Prevent any actions if albums limit reached - show notice instead */
          if (data.photoalbumsLimit) {
            jQuery('#ohsnap').css({
              'z-index': 90000000
            });
            ohSnapX();
            ohSnap(data.photoalbumsLimit[0], {
              'color': 'red'
            });
          }

          return;
        }

      }
    });

    return false;
  });
}


jQuery("body").on("photoalbumView", function(evt, data) {
  setTimeout(function() {
    photoalbumEditFormInit();
  }, 85);
});

/**
 * Fires on photoalbum edit event
 */
function userPhotoalbumEdit() {
  jQuery(photoalbum_edit_area).toggleClass('active');
  jQuery('.photo__add').toggle();
}