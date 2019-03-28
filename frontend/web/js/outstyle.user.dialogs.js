/**
 * Outstyle Dialogs JS Module
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
if (!outstyle.dialogs) {
    outstyle.dialogs = {};
}

jQuery(document).ready(function() {
    (function() {
        "use strict";

        var _path = '/messages'; /* Since dialogs are a part of messages and are IN messages section */

        /* ! --- GLOBAL BINDS --- */

        /* Global 'ondocumentready' bind for calling out the function from other modules */
        jQuery("body").on("dialogsInit", function() {
            init();
        });

        /* When dialogues list are loaded into dialogs sidebar (header passed from server) */
        /* X-IC-Trigger @ DialogsController */
        jQuery("body").on("dialogsLoaded", function() {
            jQuery('#conversations__loadmore').remove();
            window.setTimeout(function() {
                jQuery('body').trigger('dialogsInit');
                jQuery('body').trigger('messagesInit');
            }, 150);
        });

        jQuery("body").on("createNewDialog", function() {
            window.alert('Get checked friends and initiate new dialogue or conversation');
        });

        /* @ dialog/search view */
        jQuery("body").on("dialogsSearchModeSwitch", function() {
            jQuery('body').trigger('layoutInit', {
                'forElement': 'dialogs'
            });
            jQuery('body').trigger('layoutSwitch', 'friends_dialogs');
            Intercooler.triggerRequest("#friends__loadonce");
        });

        jQuery("body").on("highlightCurrentDialogBox", function() {
            _highlightCurrentDialogBox();
        });


        /* ! --- INTERCOOLER BINDS --- */

        /* Reinit dialogs after each time URL is changed */
        jQuery(document).on("pushUrl.ic", function() {
            if (window.location.pathname.indexOf(_path) === 0) {
                init();
            }
        });

        /* Triggered before a snapshot is taken for history - unwire custom JS here, restore initial page state */
        jQuery(document).on("beforeHistorySnapshot.ic", function() {
            if (window.location.pathname.indexOf(_path) === 0) {
                jQuery('.dialog__box').removeClass('active');
                jQuery('body').trigger('detachScrollbarFromElement', [jQuery('#friends_in_dialogs_area')]);
            }
        });

        /* @see: https://developer.mozilla.org/ru/docs/Web/Events/popstate */
        jQuery(document).on("handle.onpopstate.ic", function() {
            if (window.location.pathname.indexOf(_path) === 0) {
                window.setTimeout(function() {
                    init();
                }, 200);
            }

            _log('[DIALOGS] popstate triggered');
        });

        /* --- INTERCOOLER BINDS END --- */

        /* --- GLOBAL BINDS END --- */

        /**
         * Init function for dialogs
         * Depends on: messages section
         * @return null
         */
        var init = function() {
            var $dialogsContainer = jQuery('#dialogs_area');

            if ($dialogsContainer.length) {

                var DOM = {
                    'forElement': 'dialogs',
                    '$dialogsContainer': $dialogsContainer,
                    '$dialogsSearchForm': $dialogsContainer.find('#dialogs_search'),
                    '$friendsSearchForm': $dialogsContainer.find('#friends_in_dialogs_search'),
                    '$friendsInDialogsArea': $dialogsContainer.find('#friends_in_dialogs_area'),
                };

                _bindLocalEvents(DOM);
                _setupDialogBadges();
                _highlightCurrentDialogBox();

                jQuery('body').trigger('setScrollbarOnElement', [DOM.$friendsInDialogsArea]);

                _log('[DIALOGS] init finished');
            }
        };

        /**
         * Local events are meant to be binded onto DOM nodes that are not in global scope
         * (means that elements are not 'body' nor 'document')
         * !!! IMPORTANT !!! DON'T FORGET to switch off active events to prevent event binding duplication!
         * (make event .off().on())
         */
        var _bindLocalEvents = function(DOM = {}) {

            /* ! --- BIND EVENTS FOR DIALOGS AREA --- */
            jQuery(DOM.$friendsSearchForm).off('submit').on("submit", function(e) {
                e.preventDefault();
            });

            _log('[DIALOGS] local events binding finished');
        };

        var _highlightCurrentDialogBox = function() {
            var $el = jQuery('#dialogbox-' + getDialogueId());
            if ($el !== undefined) {
                $el.addClass('active');
            }
        };

        var _setupDialogBadges = function() {
            if (!jQuery.isEmptyObject(OUTSTYLE_GLOBALS.owner.messages.unread)) {
                jQuery.each(OUTSTYLE_GLOBALS.owner.messages.unread, function(dialogId, unreadMessagesAmount) {
                    var dialogData = {
                        'id': '#dialogbox-' + dialogId + ' .dialog__info',
                        'color': 'blue',
                        'type': 'ordinary',
                        'text': unreadMessagesAmount
                    };
                    jQuery('body').trigger('appendBadge', dialogData);
                });
            }
        };

        var isInDialogue = function() {
            return (!isNaN(_getLastElementFromURI())) ? true : false;
        };

        var getDialogueId = function() {
            if (isInDialogue()) {
                return _getLastElementFromURI();
            }
        };

        var hasUnreadMessagesInDialogue = function() {
            var dialogId = getDialogueId();
            if (!isNaN(OUTSTYLE_GLOBALS.owner.messages.unread[dialogId]) && OUTSTYLE_GLOBALS.owner.messages.unread[dialogId] !== 0) {
                return true;
            }
            return false;
        };

        function _getLastElementFromURI() {
            return window.location.pathname.split("/").pop();
        }

        /* --- Take out only needed functions to global scope --- */
        this.init = init;
        this.isInDialogue = isInDialogue;
        this.getDialogueId = getDialogueId;
        this.hasUnreadMessagesInDialogue = hasUnreadMessagesInDialogue;

    }).call(outstyle.dialogs);

    _log('[JQREADY] outstyle.dialogs object created');
});