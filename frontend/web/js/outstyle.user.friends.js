/**
 * Outstyle Messages JS Module
 * Depends on: JQuery, Intercoolerjs
 * Author: <scsmash3r@gmail.com>
 * Copyright (c) 2018 [SC]Smash3r; Beerware
 * @preserve
 */

/**
 * JSHint options
 * @see https://jshint.com/docs/options/
 */
/* globals jQuery: false,
           Intercooler: false,
           _log: false,
           OUTSTYLE_GLOBALS: false */
/* jshint esversion: 6 */
/* jshint maxparams: 3 */
/* jshint undef: true */
/* jshint unused: true */
/* jshint browser: true */

/* Define global namespaces */
if ("undefined" == typeof outstyle) {
    var outstyle = {};
}
if (!outstyle.friends) {
    outstyle.friends = {};
}

jQuery(document).ready(function() {
    (function() {

        /**
         * Indicates in which paths this module is being used. Relies on popstate change
         * One module can be used in various paths
         * @see `handle.onpopstate.ic` below to understand what this variable is used for
         * @type {Array}
         */
        var _paths = [
            '/friends',
            '/messages'
        ];

        /* ! --- GLOBAL BINDS --- */

        /* --- Global 'ondocumentready' binds for calling out the function from other modules or for IC --- */
        jQuery("body").on("friendsInit", function() {
            init();
        });

        /* ! --- INTERCOOLER BINDS --- */

        /* Reinit dialogs after each time URL is changed */
        jQuery(document).on("pushUrl.ic", function() {
            if (window.location.pathname.indexOf(_paths[1]) === 0) {
                init();
            }
        });

        /* @see: https://developer.mozilla.org/ru/docs/Web/Events/popstate */
        jQuery(document).on("handle.onpopstate.ic", function() {
            if (window.location.pathname.indexOf(_paths[0]) === 0 || window.location.pathname.indexOf(_paths[1]) === 0) {
                window.setTimeout(function() {
                    init();
                }, 200);
            }

            _log('[FRIENDS] popstate triggered');
        });
        /* --- INTERCOOLER BINDS END --- */

        /* --- GLOBAL BINDS END --- */

        /**
         * Init function for friends
         * Must be called everytime a new set of friends-related nodes was requested via AJAX
         * @return null
         */
        var init = function() {
            var DOM = {
                'forElement': 'friends',
            };
            _bindLocalEvents(DOM);

            _log('[FRIENDS] init finished');
        };

        /**
         * Local events are meant to be binded onto DOM nodes that are not in global scope
         * (means that elements are not 'body' nor 'document')
         * !!! IMPORTANT !!! DON'T FORGET to switch off active events to prevent event binding duplication!
         * (make event .off().on())
         * @param  {Object} [DOM={}] Current DOM nodes
         */
        var _bindLocalEvents = function() {

            /* ! FRIEND SELECT CHECKBOX - When friend is selected for adding into new dialogue */
            jQuery(".friend-selection-checkbox").off('change').on('change', function() {
                var thisCheckbox = jQuery(this),
                    friendId = parseInt(thisCheckbox.val());

                if (jQuery.isNumeric(friendId)) {
                    if (thisCheckbox.is(":checked")) {
                        OUTSTYLE_GLOBALS.owner.friends.selected.push(friendId);
                    } else {
                        OUTSTYLE_GLOBALS.owner.friends.selected.splice(jQuery.inArray(friendId, OUTSTYLE_GLOBALS.owner.friends.selected), 1);
                    }
                    OUTSTYLE_GLOBALS.owner.friends.count.selected = OUTSTYLE_GLOBALS.owner.friends.selected.length;
                    if (OUTSTYLE_GLOBALS.owner.friends.count.selected > 0) {
                        jQuery('#dialog-create-new, #dialog-add-members').removeAttr('disabled');
                    } else {
                        jQuery('#dialog-create-new, #dialog-add-members').attr('disabled', true);
                    }
                }

                _log('[FRIENDS] Selection checkbox triggered');
            });

            /* ! FRIEND SELECT CHECKBOX - Uncheck all on events init */
            var checkedFriends = jQuery(".friend-selection-checkbox");
            checkedFriends.prop('checked', false);

            _log('[FRIENDS] local events binding finished');
        };

        /* --- Take out only needed functions to global scope --- */
        this.init = init;

    }).call(outstyle.friends);

    _log('[JQREADY] outstyle.friends object created');
});







/* TODO Wrap it up into outstyle.friends module */
/* FIXME Move this to separate loaders module */
var friends_loader = '<div class="loader--smallest"></div>';
/* FIXME Move this to separate tooltips module */
var friendsTooltipContentClass = '.tooltipster-content .friend_options_tooltip_content';

/* FIXME Take this out to another file */
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
            jQuery('body').trigger('friendsInit');
            jQuery('body').trigger('setScrollbarOnElement', [jQuery('#friends_in_dialogs_area')]);
        }
        if (data.triggeredBy == 'friends_in_dialogs_search') {
            jQuery('body').trigger('friendsInit');
            friendsCheckForAlreadySelected();
        }
    }, 25);
});



function friendsCheckForAlreadySelected() {
    if (!jQuery.isEmptyObject(OUTSTYLE_GLOBALS.owner.friends.selected)) {
        jQuery.each(OUTSTYLE_GLOBALS.owner.friends.selected, function(index, friendId) {
            var alreadySelectedFriend = jQuery("#friendbox-" + friendId);
            if (alreadySelectedFriend.length) {
                alreadySelectedFriend
                    .find('.friend-selection-checkbox')
                    .prop("checked", true);
            }
        });
    }
    _log('friendsCheckForAlreadySelected() triggered');
}

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
function friendsInit() {}