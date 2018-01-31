var video_container = '#uservideo';

function userShowVideoModal() {
    jQuery(video_container+' .modal__iframe').empty();
    jQuery(video_container).trigger('openModal');
    appendNotification(video_container); /* outstyle.notifications.js */
}

jQuery(video_container).on('closeModal', function(e) {
    jQuery(video_container+' .modal__iframe').empty();
    removeNotification(video_container); /* outstyle.notifications.js */
});
