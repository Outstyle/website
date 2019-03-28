/* ! TODO Move this to separate loaders module */
var friends_loader = '<div class="loader--smallest"></div>';
/* ! TODO Move this to separate tooltips module */
var friendsTooltipContentClass = '.tooltipster-content .friend_options_tooltip_content';

/* Take this out to another file */
function userFriendsAvatarsInit() {
    jQuery("img.friend__avatar").on("error", function() {
        jQuery(this).unbind("error").attr("src", "/css/i/broken/avatar_128x128.png");
    });
}

function hideFriendsFromList(friendsArray) {
    var counter = friendsArray.length;
    setTimeout(function() {
        if (counter) {
            friendsArray.forEach(function(friendId, i) {
                jQuery("#friendbox-" + friendId).hide();
                setTimeout(function() {
                    counter -= 1;
                    if (counter === 0) {
                        jQuery('.search__friends').show();
                        jQuery('#outstyle_loader').hide();
                    }
                }, friendId);
            });
        } else {
            jQuery('.search__friends').show();
            jQuery('#outstyle_loader').hide();
        }
    }, 250);
}

function moveFriendFromPendingToActive(friendId) {
    OUTSTYLE_GLOBALS.owner.friends.active.push(friendId);
    OUTSTYLE_GLOBALS.owner.friends.pending.splice(jQuery.inArray(friendId, OUTSTYLE_GLOBALS.owner.friends.pending), 1);
    OUTSTYLE_GLOBALS.owner.friends.count.pending = OUTSTYLE_GLOBALS.owner.friends.pending.length;
}

jQuery("body").on("friendsFindError friendsFilterError", function(evt, data) {
    ohSnapX();
    jQuery.each(data, function(key, value) {
        ohSnap(decodeURIComponent(value).replace(/\+/g, " "), {
            'color': 'yellow'
        });
    });
});

jQuery("body").on("newFriendAddedSuccess", function(evt, addedFriendId) {
    jQuery("#friendbox-" + addedFriendId)
        .addClass('friend__added')
        .slideUp('slow');
    jQuery('#friendbox-' + addedFriendId + ' .friend__options').tooltipster('close');
    OUTSTYLE_GLOBALS.owner.friends.active.push(addedFriendId);
});

jQuery("body").on("newFriendAlreadyAdded", function(evt, data) {
    ohSnapX();
    jQuery.each(data, function(key, value) {
        ohSnap(decodeURIComponent(value).replace(/\+/g, " "), {
            'color': 'yellow'
        });
    });
});

jQuery("body").on("newFriendshipOnesided", function(evt, addedFriendId) {
    jQuery("#friendbox-" + addedFriendId)
        .addClass('friend__added')
        .slideUp('slow');
    moveFriendFromPendingToActive(addedFriendId);
});

jQuery("body").on("newFriendshipApproved", function(evt, addedFriendId) {
    jQuery("#friendbox-" + addedFriendId)
        .removeClass('friend__pending')
        .addClass('friend__active');
    jQuery("#friendbox-" + addedFriendId).find('.friend__actions').empty();
    moveFriendFromPendingToActive(addedFriendId);
});

/* When IC returned successfull AJAX callback */
jQuery("body").on("friendsFindSuccess", function(evt, data) {
    if (data.triggeredBy != 'loadmore') {
        jQuery("#friendsList .search__friends").remove();
    }
    if (jQuery.isNumeric(data.page)) {
        jQuery("#page").val(data.page);
    }

    jQuery('#outstyle_loader').show();

    hideFriendsFromList(OUTSTYLE_GLOBALS.owner.friends.pending);
    hideFriendsFromList(OUTSTYLE_GLOBALS.owner.friends.active);
});

/* When IC returned successfull AJAX callback */
jQuery("body").on("friendsFilterSuccess", function(evt, data) {
    if (data.triggeredBy != 'loadmore') {
        jQuery("#friendsList .search__friends").remove();
    }
    if (jQuery.isNumeric(data.page)) {
        jQuery("#page").val(data.page);
    }
    setTimeout(function() {
        jQuery('.search__friends').show();
        jQuery('#outstyle_loader').hide();
        if (data.triggeredBy == 'friends__loadonce') {
            jQuery('body').trigger('setScrollbarOnElement', [jQuery('#friends_in_dialogs_area')]);
        }
    }, 25);
});


function friendsBeforeSearchActions() {
    jQuery('#outstyle_loader').show();
    jQuery("#friendsList .search__friends--notfound").remove();
}

function friendsAfterSearchActions() {
    jQuery('#outstyle_loader').hide();
}

function friendBeforeAddNewFriend() {
    jQuery(friendsTooltipContentClass)
        .hide()
        .after(friends_loader);
}

function friendAfterAddNewFriend() {
    jQuery('.tooltipster-content')
        .find('.loader--smallest')
        .remove();
    jQuery(friendsTooltipContentClass)
        .show();
}

function friendShowOptionsTooltip(friendId) {
    jQuery('#friendId').val(friendId);
    friendTooltipInit('#friendbox-' + friendId + ' .friend__options');
    jQuery(friendsTooltipContentClass).show();
    jQuery(this).tooltipster('open');

}

/* ! TODO Trigger event attach directly on elements? */
jQuery("body").on("loadMoreFriends", function(event, elt) {
    if ("undefined" == typeof elt) {
        elt = '#friends-search-form';
    }
    Intercooler.triggerRequest(elt);
});

function friendsSearchFormInit() {

    jQuery('#friends-search-form').on('keypress', function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            jQuery('body').trigger('loadMoreFriends', '.friends-form-trigger');
        }
    });

}

/**
 * Initialize friends. This needs to be done after every AJAX call
 * @see: friends/view
 */
function friendsInit() {

}