var video_container = '#uservideo';

function userShowVideoModal() {
    unloadOldVideo();
    jQuery(video_container).trigger('openModal');
    appendNotification(video_container);
}
jQuery(video_container).on('closeModal', function(e) {
    unloadOldVideo();
    removeNotification(video_container);
});
function unloadOldVideo() {
    jQuery(video_container+' .modal__iframe').empty();
}
