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
           autosize: false,
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
if (!outstyle.messages) {
    outstyle.messages = {};
}

jQuery(document).ready(function() {
    (function() {
        "use strict";

        var _path = '/messages';

        /* ! --- GLOBAL BINDS --- */

        /* --- Global 'ondocumentready' binds for calling out the function from other modules or for IC --- */
        jQuery("body").on("messagesInit", function() {
            init();
        });

        jQuery("body").on("messageMarkAsRead", function(event, $message) {
            var currentDialogueId = outstyle.dialogs.getDialogueId();
            OUTSTYLE_GLOBALS.owner.messages.unread[currentDialogueId] -= 1;
            OUTSTYLE_GLOBALS.owner.messages.count.unread -= 1;
            jQuery($message).removeClass('message-unread').addClass('read');

            jQuery('body').trigger('appendBadge', {
                'id': '#dialogbox-' + currentDialogueId + ' .dialog__info',
                'color': 'blue',
                'type': 'ordinary',
                'text': OUTSTYLE_GLOBALS.owner.messages.unread[currentDialogueId]
            });
        });

        jQuery("body").on("messagesHighlightUnread", function(event, $messagesList) {
            _highlightUnreadMessages($messagesList);
        });

        jQuery("body").on("messagesLastUnreadReached", function() {
            jQuery('.chat-thread .chat-thread-separator').fadeOut("slow", function() {
                jQuery(this).remove();
            });

            jQuery('body').trigger('appendBadge', {
                'id': '#menu__item-messages a',
                'color': 'red',
                'type': 'shaded',
                'text': OUTSTYLE_GLOBALS.owner.messages.count.unread
            });

        });

        /* When user sends a message, we need to re-trigger chatbox immediately to receive that message */
        jQuery("body").on("newMessageAdded", function() {
            jQuery('#message')
                .removeAttr('disabled')
                .val('')
                .focus();
            autosize.update(jQuery('#message'));
            jQuery('.chat-thread .conversations__new').remove();
            Intercooler.triggerRequest("#messages_area");
        });

        /* Timeout is needed so new node could be inserted into DOM for manipulation (i.e. scrollbars reinit -> scroll.y) */
        jQuery("body").on("messageNew", function() {
            window.setTimeout(function() {
                init();
            }, 200);
        });

        jQuery("body").on("messagesAddError", function(event, data) {
            jQuery('body').trigger('showErrors', data);
            jQuery('#message').removeAttr('disabled').focus();
        });

        /* ! --- INTERCOOLER BINDS --- */

        /* Reinit messages after each time URL is changed (dialog navigation i.e.) */
        jQuery(document).on("pushUrl.ic", function() {
            if (window.location.pathname.indexOf(_path) === 0) {
                init();
            }
        });

        /* Triggered before a snapshot is taken for history - unwire custom JS here, restore initial page state */
        jQuery(document).on("beforeHistorySnapshot.ic", function() {
            jQuery('#messages_area').hide(); /* To prevent blinking while attaching scrollbars */

            /* TODO: move this */
            jQuery('#dialog-create-new').attr('disabled', true);
            OUTSTYLE_GLOBALS.owner.friends.selected.length = 0;

            if (window.location.pathname.indexOf(_path) === 0) {
                jQuery('body').trigger('detachScrollbarFromElement', [jQuery('#messages_list')]);
            }
        });

        /* @see: https://developer.mozilla.org/ru/docs/Web/Events/popstate */
        jQuery(document).on("handle.onpopstate.ic", function() {
            if (window.location.pathname.indexOf(_path) === 0) {
                window.setTimeout(function() {
                    init();
                }, 200);
            }

            _log('[MESSAGES] popstate triggered');
        });

        /* Before any request is fired up by IC */
        jQuery(document).on("beforeAjaxSend.ic", function(event, settings) {
            if (settings.url == '/api/messages/get') {
                if (outstyle.dialogs.isInDialogue() && !outstyle.dialogs.hasUnreadMessagesInDialogue()) {
                    settings.data = settings.data + '&dialog=' + outstyle.dialogs.getDialogueId();
                } else {
                    settings.cancel = 'true';
                }
            }

            if (settings.url == '/api/messages/add') {
                jQuery('#message').attr('disabled', true);
                if (outstyle.dialogs.isInDialogue()) {
                    settings.data = settings.data + '&dialog=' + outstyle.dialogs.getDialogueId();
                }
            }
        });

        /* --- INTERCOOLER BINDS END --- */

        /* --- GLOBAL BINDS END --- */

        /**
         * Init function for messages
         * Must be called everytime $messagesContainer is rerendered (i.e. by IC ajax)
         * @return null
         */
        var init = function() {
            var $messagesContainer = jQuery('#outstyle_messages');

            if ($messagesContainer.length) {
                var DOM = {
                    'forElement': 'messages',
                    '$messagesContainer': $messagesContainer,
                    '$messagesArea': $messagesContainer.find('#messages_area'),
                    '$messagesHeader': $messagesContainer.find('#messages_header'),
                    '$messagesList': $messagesContainer.find('#messages_list'),
                    '$messagesBottomPanel': $messagesContainer.find('#messages_bottompanel'),
                    '$messagesSendbox': $messagesContainer.find('#messages_sendbox'),
                    '$messagesDialogName': $messagesContainer.find('input[name=dialog_name]'),
                    '$messagesDialogOptionsButton': $messagesContainer.find('.dialog__settingsbutton'),
                    '$messageTextarea': $messagesContainer.find('#message'),
                };

                DOM.$messagesArea.show();

                _bindKeyEvents(DOM);
                _bindLocalEvents(DOM);

                jQuery('body').trigger('layoutInit', DOM);
                jQuery('body').trigger('tooltipsInit', DOM);
                jQuery('body').trigger('messagesHighlightUnread', [DOM.$messagesList]);
                jQuery('body').trigger('setScrollbarOnElement', [DOM.$messagesList]);


                _log('[MESSAGES] init finished');
            }
        };

        var _highlightUnreadMessages = function($messagesList) {
            var currentDialogueId = outstyle.dialogs.getDialogueId(),
                lastUnreadMessagesAmount = OUTSTYLE_GLOBALS.owner.messages.unread[currentDialogueId];
            if (outstyle.dialogs.hasUnreadMessagesInDialogue()) {
                var $lastUnreadMessages = $messagesList
                    .find('li.chat-thread-message')
                    .slice(-lastUnreadMessagesAmount);
                $lastUnreadMessages
                    .addClass('message-unread')
                    .last()
                    .addClass('message-last')
                    .attr('name', 'message-last')
                    .attr('ic-trigger-on', 'scrolled-into-view');

                jQuery('<li class="chat-thread-separator"></li>')
                    .insertBefore($lastUnreadMessages[0]);
            }
        };

        /**
         * Key bindings in messages section
         * !!! IMPORTANT !!! DON'T FORGET to switch off active events to prevent event binding duplication!
         * (make event .off().on())
         * @param  {Object} [DOM={}] Current DOM nodes
         */
        var _bindKeyEvents = function(DOM = {}) {

            var enterKey = 13;

            /* ! --- BIND EVENTS FOR MESSAGES TEXTAREA --- */
            DOM.$messageTextarea.off('keydown').on('keydown', function(e) {
                if (e.keyCode === enterKey) {
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
            }).off('keypress').on('keypress', function(e) {
                if (e.keyCode === enterKey) {
                    if (!e.shiftKey && !e.ctrlKey) {
                        Intercooler.triggerRequest("#message-send-submit");
                        return false;
                    }
                }
            });

            _log('[MESSAGES] key binding finished');
        };

        /**
         * Local events are meant to be binded onto DOM nodes that are not in global scope
         * (means that elements are not 'body' nor 'document')
         * !!! IMPORTANT !!! DON'T FORGET to switch off active events to prevent event binding duplication!
         * (make event .off().on())
         * @param  {Object} [DOM={}] Current DOM nodes
         */
        var _bindLocalEvents = function(DOM = {}) {

            DOM.$messageTextarea.off('focus').on('focus', function() {
                autosize(DOM.$messageTextarea);
            });

            DOM.$messagesDialogName.off('focus').on('focus', function() {
                jQuery(this).removeClass('c-field--editable');
            });

            DOM.$messagesDialogName.off('focusout').on('focusout', function() {
                jQuery(this).addClass('c-field--editable');
            });

            /* Fires up everytime chat box height is changed */
            DOM.$messageTextarea.off('autosize:resized').on('autosize:resized', function() {
                jQuery('body').trigger('layoutInit', DOM);
            });

            _log('[MESSAGES] local events binding finished');
        };

        /* --- Take out only needed functions to global scope --- */
        this.init = init;

    }).call(outstyle.messages);

    _log('[JQREADY] outstyle.messages object created');
});