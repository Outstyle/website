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

      success: function(data, status, jqXHR) {

        userPhotoalbumEdit();
        changePhotoalbumTitle(data.name, data.id);

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

function changePhotoalbumTitle(title, id) {
  jQuery('#album-' + id).find('.album__title>span').html(title);
  jQuery('#album-' + id).find('.album__title').attr('title', title);
  jQuery('#photos_area').find('h1').html(title);
}