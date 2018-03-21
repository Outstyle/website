var photoalbum_delete_modal = '#userphotoalbumdelete';

/* On succesfull album deletion, when submitting confirmation modal */
jQuery("body").on("photoalbumDelete", function(event, data) {

  jQuery(photoalbum_delete_modal).trigger('closeModal');

  if (jQuery.type(data) === "number") {
    var albumToDelete = jQuery("#album-" + data);
    albumToDelete
      .addClass('album__deleted')
      .parent()
      .animate({
        height: 'toggle',
        opacity: 'toggle'
      }, 'slow')
      .promise()
      .done(function() {
        Intercooler.triggerRequest('#menu__item-photo a');
      });
  }

});

/**
 * Fires on photoalbum delete modal window
 */
function userShowPhotoalbumDeleteModal(albumId) {
  var activeAlbum = jQuery("#album-" + albumId);
  var activeAlbumTitle = activeAlbum.find('.album__title span').html();

  jQuery(photoalbum_delete_modal).trigger('openModal');
  jQuery(photoalbum_delete_modal).find('h3').html(activeAlbumTitle);

  jQuery(photoalbum_delete_modal)
    .find('#userphotoalbumdelete-confirm')
    .attr({
      'ic-include': '{"album":"' + albumId + '"}'
    });
}

function userHidePhotoalbumDeleteModal() {
  jQuery(photoalbum_delete_modal).trigger('closeModal');
}