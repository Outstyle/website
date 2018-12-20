/**
 * Outstyle Layout JS Functions
 * This file must contain all the functions, related to overall Outstyle layout
 * Author: <scsmash3r@gmail.com>
 * Copyright (c) 2018 [SC]Smash3r; Beerware
 * @preserve
 */
/* jshint esversion: 6 */

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

        /* --- Global 'ondocumentready' binds for calling out the function from other modules or for IC --- */
        jQuery('body').on('layoutInit', function(event, DOM) {
            init(DOM);
        });

        jQuery('body').on('setScrollbarOnElement', function(event, element) {
            _setScrollbarOnElement(element);
        });

        var DOM = {
            '$header': jQuery('.social__header')
        };

        /**
         * Recalculate layout, depending on passing element with DOM nodes (jqObj)
         * @param  {Object} [DOM={}]     jqObj with 'for' attr
         * @return null
         */
        var init = function(DOM = {}) {
            if (!jQuery.isEmptyObject(DOM)) {

                if (DOM.for === 'messages') {
                    var elements = jQuery('#conversations_area, #messages_area');
                    var offset = DOM.$messagesSendbox.height() + outstyle.layout.DOM.$header.height();
                    _setWidthForElements(DOM.$messagesSendbox, DOM.$messagesContainer);

                    if (outstyle.dialogs.isInDialogue()) {
                        DOM.$messagesSendbox.show();
                        _setEqualHeightForElements(elements, offset);
                    } else {
                        DOM.$messagesSendbox.hide();
                        _setEqualHeightForElements(elements, 0);
                    }
                }

                _log('[LAYOUT] recalculation for ' + DOM.for+' finished');
            }
        };

        /**
         * Sets an equal height for elements
         * @param  {obj} el              jqObj - List of selectors
         * @param  {int} offset          Substracted height
         * @param  {obj} options         Misc options for helping with layout appearance
         */
        var _setEqualHeightForElements = function(elements = {}, offset = 0, options = {}) {
            var windowHeight = window.innerHeight;
            elements.css({
                'height': 'calc(100vh - ' + offset + 'px)'
            });

            _log('[LAYOUT] equal heights set');
        };

        /**
         * Brings first element width to second element's width
         * @param  {obj} $el          JQuery object - element to apply width to
         * @param  {obj} $widthEl     JQuery object - element to get width from for applying
         * @return {null}
         */
        var _setWidthForElements = function($el, $widthEl) {
            $el.css({
                'width': $widthEl.width() + 'px'
            });
        };

        /**
         * Sets scrollbars on particular layout element
         * @param  {obj} $el    jqObj
         * @return null
         */
        var _setScrollbarOnElement = function($el) {
            if (!$el.hasClass('os-host') && typeof $el.attr('id') !== typeof undefined) {

                if ($el.attr('id') === 'messages_list') {
                    $el.overlayScrollbars({})
                        .overlayScrollbars()
                        .scroll({
                            y: "100%" /* To move scroll to the bottom of messages */
                        });
                }

                _log('[LAYOUT] scrollbars added on element ' + $el.attr('id'));
            }
        };

        /* --- Take out only needed functions to global scope --- */
        this.init = init;
        this.DOM = DOM;

    }).call(outstyle.layout);

    _log('[JQREADY] outstyle.layout object created');
});