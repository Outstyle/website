var conversations_area = '#conversations_area';
var messages_area = '#messages_area';
var messages_list = '#messages_list';

/**
 * Initialize photoalbums. This needs to be done after every AJAX call
 * @see: photoalbum/index
 */
function messagesInit() {

    jQuery(conversations_area).show();

    setTimeout(function() {
        messagesCalculateEqualHeight();
        jQuery(messages_area).fadeIn('slow');
    }, 85);

    sidebarHighlightActiveMenuItem('#menu__item-messages');

}

/**
 * Init scrollbars for photoalbums area
 * @see: https://github.com/KingSora/OverlayScrollbars
 */
function messagesScrollbarInit() {
    jQuery(conversations_area).overlayScrollbars({}).overlayScrollbars();
    jQuery(messages_list).overlayScrollbars({}).overlayScrollbars();
}

/**
 * Recalculates height for photoalbums sidebar and photos area so they could be equal (UI issues)
 */
function messagesCalculateEqualHeight() {
    var h = window.innerHeight;
    var messagesHeight = jQuery(messages_list).height();
    if (messagesHeight > h) {
        jQuery(conversations_area + ', .conversations__new, ' + messages_area).css({
            'height': messagesHeight + 'px'
        });
    } else {
        jQuery(conversations_area + ', .conversations__new, ' + messages_area).css({
            'height': 'calc(100vh - 90px)'
        });
    }
}

/**
 * On succesfull dialogs load more
 */
jQuery("body").on("messagesDialogsLoadMore", function(event, data) {
    jQuery('#conversations__loadmore').hide();
    setTimeout(function() {
        messagesCalculateEqualHeight();
        messagesScrollbarInit();
    }, 50);
});

/**
 * On messages load
 * @see @frontend -> MessagesController -> actionView
 */
jQuery("body").on("messagesListLoaded", function(event, data) {
    setTimeout(function() {
        messagesCalculateEqualHeight();
        messagesScrollbarInit();
    }, 50);
});