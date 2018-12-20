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

        /* --- Global 'ondocumentready' binds for calling out the function from other modules or for IC --- */
        jQuery("body").on("messagesInit", function(event, data) {
            init();
        });

        jQuery("body").one("bindEvents", function(event, DOM) {
            _bindKeyEvents(DOM);
            _bindLocalEvents(DOM);
        });

        /* Reinit messages after each time URL is changed (dialog navigation i.e.) */
        jQuery(document).on("pushUrl.ic", function(event, target, data) {
            if (window.location.pathname.indexOf(_path) === 0) {
                init();
            }
        });

        /* Messages history.back() events */
        jQuery(document).on("beforeHistorySnapshot.ic", function(evt, target) {
            if (window.location.pathname.indexOf(_path) === 0) {
                var messagesScrollbarInstance = jQuery('#messages_list').overlayScrollbars();
                if (messagesScrollbarInstance !== undefined) {
                    jQuery('#messages_list').overlayScrollbars().destroy();
                }
            }
        });

        jQuery(document).on("handle.onpopstate.ic", function(evt) {
            if (window.location.pathname.indexOf(_path) === 0) {
                jQuery('#messages_area').show();
            }
        });

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
                jQuery('body').trigger('bindEvents', DOM);
                jQuery('body').trigger('layoutInit', DOM);
                jQuery('body').trigger('setScrollbarOnElement', [DOM.$messagesList]);

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