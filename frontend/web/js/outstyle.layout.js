/**
 * Outstyle Layout JS Functions
 * Depends on: JQuery, Intercoolerjs, OverlayScrollbars
 * This file must contain all the functions, related to overall Outstyle layout
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
           _log: false */
/* jshint esversion: 6 */
/* jshint maxparams: 3 */
/* jshint undef: true */
/* jshint unused: true */
/* jshint browser: true */

/* Define global namespaces */
if ("undefined" == typeof outstyle) {
    var outstyle = {};
}
if (!outstyle.layout) {
    outstyle.layout = {};
}

jQuery(document).ready(function() {
    (function() {
        "use strict";

        /* ! --- GLOBAL BINDS --- */

        /* --- Global 'ondocumentready' binds for calling out the function from other modules or for IC --- */
        jQuery('body').on('layoutInit', function(event, DOM) {
            init(DOM);
        });

        jQuery('body').on('layoutSwitch', function(event, dataSwitchLayout) {
            _layoutSwitch(dataSwitchLayout);
        });

        jQuery('body').on('setScrollbarOnElement', function(event, $element) {
            _setScrollbarOnElement($element);
        });

        jQuery('body').on('detachScrollbarFromElement', function(event, $element) {
            _detachScrollbarFromElement($element);
        });

        var DOM = {
            '$header': jQuery('.social__header')
        };

        /* --- GLOBAL BINDS END --- */

        /**
         * Recalculate layout, depending on passing element with DOM nodes (jqObj)
         * @param  {Object} [DOM={}]     jqObj with 'forElement' attr
         * @return null
         */
        var init = function(DOM = {}) {
            if (!jQuery.isEmptyObject(DOM)) {

                var $elements = '';
                var toHeight = 0;

                /* ! --- Layout init for messages section --- */
                if (DOM.forElement === 'messages') {

                    /* Setting equal heights for dialogs and messages blocks */
                    $elements = jQuery('#conversations_area, #messages_area');
                    var offset = outstyle.layout.DOM.$header.height() + DOM.$messagesBottomPanel.height();
                    _setEqualHeightForElements($elements, offset);

                    /* Setting equal width for whole messages area and botom pane (flexbox stuff) */
                    $elements = DOM.$messagesContainer.add(DOM.$messagesBottomPanel);
                    _setEqualWidthForElements($elements, true);

                    /* Setting proper width for messages list, excluding messages header block */
                    if (DOM.$messagesHeader.length > 0) {
                        DOM.$messagesList.css({
                            'height': 'calc(100% - ' + DOM.$messagesHeader.outerHeight() + 'px)'
                        });
                    }

                    /* If we're not in dialogue itself - hiding certain elements*/
                    if (outstyle.dialogs.isInDialogue()) {
                        DOM.$messagesSendbox.css({
                            'visibility': 'visible'
                        });
                    } else {
                        DOM.$messagesSendbox.css({
                            'visibility': 'hidden'
                        });
                    }

                    DOM.$messagesBottomPanel.show();
                }

                /* ! --- Layout init for dialogs section --- */
                if (DOM.forElement === 'dialogs') {

                    $elements = jQuery("#dialog_createnew");
                    toHeight = jQuery("#messages_bottompanel").css('height');
                    _setEqualHeightForElements($elements, 0, toHeight);

                    /* While switching to friends search for a new dialogue, friends search area must also be brought
                       to #messages_area height */
                    $elements = jQuery("#messages_area, #friends_in_dialogs_area");
                    toHeight = jQuery("#messages_area").css('height');
                    _setEqualHeightForElements($elements, 0, toHeight);
                }

                _log('[LAYOUT] recalculation for ' + DOM.forElement + ' finished');
            }
        };

        /**
         * Toggle elements visibility, showing one set of elements and hiding another set of elements
         *
         * @param  {string} [dataSwitchLayout='']  Unique ID, stored in 'data-switch-layout' attr
         * @return null
         */
        var _layoutSwitch = function(dataSwitchLayout = '') {
            var hiderClass = 'u-i';
            if (dataSwitchLayout !== typeof undefined) {
                jQuery('[data-switch-layout="' + dataSwitchLayout + '"]').toggleClass(hiderClass);
            }

            _log('[LAYOUT] hide/show switch for set of elements with data \'' + dataSwitchLayout + '\' finished');
        };

        /**
         * Sets an equal height for elements
         * @param  {Object} [elements={}] jqObj - List of selectors
         * @param  {Number} [offset=0]    Substracted height
         * @param  {Number} [toHeight=0]  Brings all elements to specified height
         * @return {null}
         */
        var _setEqualHeightForElements = function($elements = {}, offset = 0, toHeight = 0) {

            if (toHeight !== 0) {
                var heightNum = parseInt(toHeight, 10);
                $elements.css({
                    'height': heightNum + 'px'
                });
            } else {
                $elements.css({
                    'height': 'calc(100vh - ' + offset + 'px)'
                });
            }

            _log('[LAYOUT] equal heights set');
        };

        /**
         * Sets an equal width for elements
         * @param  {Object} [elements={}] jqObj - List of selectors
         * @param  {Boolean} [setSecondToFirstWidth=false] If set to 'true', all elements height will be brought to 1st el height
         * @return {[null]}
         */
        var _setEqualWidthForElements = function($elements = {}, setSecondToFirstWidth = false) {
            if (setSecondToFirstWidth === true) {
                var firstElementWidth = jQuery($elements.get(0)).width();
                $elements.splice(0, 1); /* Removing 1st el to prevent width reassign */
                $elements.css({
                    'width': firstElementWidth + 'px'
                });
            } else {

            }
        };

        /**
         * Sets scrollbars on particular layout element
         * @param  {obj} $el    jqObj
         * @return null
         */
        var _setScrollbarOnElement = function($el) {
            /* ! [REFRESH] Scrollbars (AJAXED call, i.e. - existing instance) */
            if ($el.overlayScrollbars()) {
                if ($el.attr('id') === 'messages_list') {
                    $el.overlayScrollbars({})
                        .overlayScrollbars()
                        .scroll({
                            y: "100%"
                        }, 500);
                }

                if ($el.attr('id') === 'friends_in_dialogs_area') {
                    $el.overlayScrollbars({})
                        .overlayScrollbars()
                        .scroll({
                            y: "0"
                        });
                }

                if ($el.attr('id') === 'conversations_area') {
                    $el.overlayScrollbars({})
                        .overlayScrollbars()
                        .scroll({
                            y: "0"
                        });
                }

                _log('[LAYOUT] scrollbar is refreshed for #' + $el.attr('id'));
            }

            /* If element does not have any instances of overlayscrollbar yet - setting new instance */
            /* ! [INIT] Scrollbars (new instance) */
            if (!$el.hasClass('os-host') && typeof $el.attr('id') !== typeof undefined) {

                /** @see: https://kingsora.github.io/OverlayScrollbars/#!documentation/initialization-jquery */
                var osInstance = $el.overlayScrollbars({});
                var collideDetector = osInstance.find('.os-content-glue');

                /* ! --- [INIT] Scrollbars for messages list (chat) --- */

                /**
                 * Since we're using Intercooler with attached on-element events, we must ensure that we will trigger certain elements when they are appearing in overlay-scrollbars viewport.
                 * OS has '.os-content-glue' element - it serves as a first trigger-point for colliding.
                 * Second element to check for collision must be any active element inside OS content.
                 * For the last element that collider triggers, IC request must be initiated.
                 *
                 * @see outstyle.user.messages.js -> _highlightUnreadMessages()
                 */
                if (osInstance.attr('id') === 'messages_list') {
                    osInstance.overlayScrollbars({
                        'callbacks': {
                            'onScrollStop': function() {
                                var unreadMessages = osInstance.find('.message-unread');
                                var unreadMessageLast = osInstance.find('.message-last');
                                if (unreadMessages.length) {
                                    jQuery.each(unreadMessages, function() {
                                        if (isColliding(collideDetector, jQuery(this))) {
                                            jQuery('body').trigger('messageMarkAsRead', jQuery(this));
                                        }
                                    });
                                }
                                if (unreadMessageLast.length) {
                                    if (isColliding(collideDetector, unreadMessageLast)) {
                                        jQuery(this).removeClass('message-last').addClass('read');
                                        jQuery('body').trigger('messagesLastUnreadReached');
                                        Intercooler.processNodes(osInstance);
                                    }
                                }
                            }
                        }
                    });

                    /* Autoscroll chat to last message or to the bottom, if no unread messages on page */
                    var hasUnreadMessage = osInstance.find('.message-unread:first');
                    if (hasUnreadMessage.length > 0) {
                        osInstance.overlayScrollbars({})
                            .overlayScrollbars()
                            .scroll({
                                el: osInstance.find('.message-unread:first'),
                                scroll: "ifneeded",
                                block: ["begin", "end"],
                                margin: 20
                            });
                    } else {
                        osInstance.overlayScrollbars({})
                            .overlayScrollbars()
                            .scroll({
                                y: "100%"
                            });
                    }
                }

                /* ! --- [INIT] Scrollbars for dialogs list (friends search mode) --- */
                if (osInstance.attr('id') === 'friends_in_dialogs_area') {
                    osInstance.overlayScrollbars({}).overlayScrollbars();
                }

                /* ! --- [INIT] Scrollbars for conversations sidebar --- */
                if (osInstance.attr('id') === 'conversations_area') {
                    osInstance.overlayScrollbars({
                        scrollbars: {
                            autoHide: 'leave',
                            autoHideDelay: 100
                        }
                    });

                    /* ! Autoscroll to active dialog/conversation */
                    osInstance.overlayScrollbars().scroll({
                        el: osInstance.find('.dialog__box.active'),
                        scroll: "ifneeded",
                        block: ["begin", "end"],
                        margin: 0
                    });

                }

                _log('[LAYOUT] scrollbar attached on #' + $el.attr('id'));
            }
        };

        var _detachScrollbarFromElement = function($el) {
            var messagesScrollbarInstance = $el.overlayScrollbars();
            if (messagesScrollbarInstance !== undefined) {
                $el.overlayScrollbars().destroy();

                _log('[LAYOUT] scrollbar detached from #' + $el.attr('id'));
            }
        };

        /* ! --- MISC. LAYOUT FUNCTIONS --- */
        /**
         * Detects if two elements are colliding
         *
         * Credit goes to BC on Stack Overflow, cleaned up a little bit
         *
         * @link http://stackoverflow.com/questions/5419134/how-to-detect-if-two-divs-touch-with-jquery
         * @param $div1
         * @param $div2
         * @returns {boolean}
         */
        var isColliding = function($div1, $div2) {
            // Div 1 data
            var d1_offset = $div1.offset();
            var d1_height = $div1.outerHeight(true);
            var d1_width = $div1.outerWidth(true);
            var d1_distance_from_top = d1_offset.top + d1_height;
            var d1_distance_from_left = d1_offset.left + d1_width;

            // Div 2 data
            var d2_offset = $div2.offset();
            var d2_height = $div2.outerHeight(true);
            var d2_width = $div2.outerWidth(true);
            var d2_distance_from_top = d2_offset.top + d2_height;
            var d2_distance_from_left = d2_offset.left + d2_width;

            var not_colliding = (d1_distance_from_top < d2_offset.top || d1_offset.top > d2_distance_from_top || d1_distance_from_left < d2_offset.left || d1_offset.left > d2_distance_from_left);

            // Return whether it IS colliding
            return !not_colliding;
        };

        /* --- Take out only needed functions to global scope --- */
        this.init = init;
        this.DOM = DOM;
        this.isColliding = isColliding;

    }).call(outstyle.layout);

    _log('[JQREADY] outstyle.layout object created');
});