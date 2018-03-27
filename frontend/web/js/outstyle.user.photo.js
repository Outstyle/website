var photo_container = '#userphoto';

function userShowPhotoModal() {
  jQuery(photo_container + ' .modal__iframe').empty();
  jQuery(photo_container).trigger('openModal');
}