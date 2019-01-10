/**
 * Outstyle Messages JS Module
 * Depends on: JQuery, Intercoolerjs
 * Author: <scsmash3r@gmail.com>
 * Copyright (c) 2018 [SC]Smash3r; Beerware
 * @preserve
 */
/* jshint esversion: 6 */

/* Define global namespaces */
if ("undefined" == typeof outstyle) {
    var outstyle = {};
}
if (!outstyle.messages) {
    outstyle.messages = {};
}

jQuery(document).ready(function() {
    (function() {
        "use strict";

        var _path = '/messages';

        /* --- GLOBAL BINDS --- */

        /* --- Global 'ondocumentready' binds for calling out the function from other modules or for IC --- */
        jQuery("body").on("messagesInit", function(event, data) {
            init();
        });

        /* Bind events for messages only once (.one) to prevent repeating of attaching handlers */
        jQuery("body").one("messagesBindEvents", function(event, DOM) {
            _bindKeyEvents(DOM);
            _bindLocalEvents(DOM);
        });

        /* Reinit messages after each time URL is changed (dialog navigation i.e.) */
        jQuery(document).on("pushUrl.ic", function(event, target, data) {
            if (window.location.pathname.indexOf(_path) === 0) {
                init();
            }
        });

        /* Triggered before a snapshot is taken for history - unwire custom JS here, restore initial page state */
        jQuery(document).on("beforeHistorySnapshot.ic", function(evt, target) {
            jQuery('#messages_area').hide(); /* To prevent blinking while attaching scrollbars */
            if (window.location.pathname.indexOf(_path) === 0) {
                jQuery('body').trigger('detachScrollbarFromElement', [jQuery('#messages_list')]);
            }
        });

        /* @see: https://developer.mozilla.org/ru/docs/Web/Events/popstate */
        jQuery(document).on("handle.onpopstate.ic", function(evt) {
            if (window.location.pathname.indexOf(_path) === 0) {
                setTimeout(function() {
                    init();
                }, 200);
            }

            _log('[MESSAGES] popstate triggered');
        });

        jQuery("body").on("messagesAddError", function(evt, data) {
            jQuery('body').trigger('showErrors', data);
        });

        /* --- GLOBAL BINDS END --- */

        /**
         * Init function for messages
         * Must be called everytime $messagesContainer is rerendered (i.e. by IC ajax)
         * @return null
         */
        var init = function() {
            var $messagesContainer = jQuery('#outstyle_messages');

            /* Init messages only if #ID is on the page */
            if ($messagesContainer.length) {
                var DOM = {
                    'for': 'messages',
                    '$messagesContainer': $messagesContainer,
                    '$messagesArea': $messagesContainer.find('#messages_area'),
                    '$messagesList': $messagesContainer.find('#messages_list'),
                    '$messagesSendbox': $messagesContainer.find('#messages_sendbox'),
                    '$messageTextarea': $messagesContainer.find('#message-text'),
                };

                DOM.$messagesArea.show();
                jQuery('body').trigger('messagesBindEvents', DOM);
                jQuery('body').trigger('layoutInit', DOM);
                jQuery('body').trigger('setScrollbarOnElement', [DOM.$messagesList]);
                jQuery('body').trigger('highlightCurrentDialogBox');

                // sidebarHighlightActiveMenuItem(outstyle_messages.DOM.sidebarMenuItem);
                _log('[MESSAGES] init finished');
            }
        };

        var _bindKeyEvents = function(DOM = {}) {
            DOM.$messageTextarea.keydown(function(e) {
                if (e.keyCode === 13) {
                    if (e.ctrlKey) {
                        jQuery(this).val(function(i, val) {
                            return val + "\n";
                        });
                        autosize.update(DOM.$messageTextarea);
                        return false;
                    }
                    if (e.shiftKey) {
                        jQuery(this).val(function(i, val) {
                            return val + "\n";
                        });
                        autosize.update(DOM.$messageTextarea);
                        return false;
                    }
                }
            }).keypress(function(e) {
                if (e.keyCode === 13) {
                    if (!e.shiftKey && !e.ctrlKey) {
                        alert('submit');
                        return false;
                    }
                }
            });

            _log('[MESSAGES] key binding finished');
        };

        var _bindLocalEvents = function(DOM = {}) {
            autosize(DOM.$messageTextarea);

            /* Fires up everytime chat box height is changed */
            DOM.$messageTextarea.on('autosize:resized', function() {
                jQuery('body').trigger('layoutInit', DOM);
            });

            _log('[MESSAGES] local events binding finished');
        };

        /* --- Take out only needed functions to global scope --- */
        this.init = init;

    }).call(outstyle.messages);

    _log('[JQREADY] outstyle.messages object created');
});