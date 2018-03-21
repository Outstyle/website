var video_container = '#uservideo';

function userShowVideoModal() {
  jQuery(video_container + ' .modal__iframe').empty();
  jQuery(video_container).trigger('openModal');
}

jQuery(video_container).on('closeModal', function(e) {
  jQuery(video_container + ' .modal__iframe').empty();
});