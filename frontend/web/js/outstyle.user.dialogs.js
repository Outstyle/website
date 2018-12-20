/**
 * Outstyle Dialogs JS Functions
 * Author: <scsmash3r@gmail.com>
 * Copyright (c) 2018 [SC]Smash3r; Beerware
 * @preserve
 */
/* jshint esversion: 6 */

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

        /* Global 'ondocumentready' bind for calling out the function from other modules */
        jQuery("body").on("dialogsInit", function(event, data) {
            init();
        });

        /* X-IC-Trigger @ DialogsController */
        jQuery("body").on("dialogsLoaded", function(event, data) {
            jQuery('#conversations__loadmore').remove();
            setTimeout(function() {
                jQuery('body').trigger('dialogsInit');
                jQuery('body').trigger('messagesInit');
            }, 150);
        });

        /**
         * Init function for dialogs
         * Must be called everytime $messagesContainer is rerendered (i.e. by IC ajax)
         * @return null
         */
        var init = function() {
            var $dialogsContainer = jQuery('#dialogs_area');

            /* Init messages only if #ID is on the page */
            if ($dialogsContainer.length) {
                var DOM = {
                    'for': 'dialogs',
                    '$conversationsArea': $dialogsContainer.find('#conversations_area'),
                    '$conversationsLoadmore': $dialogsContainer.find('#conversations__loadmore'),
                    '$dialogBoxes': $dialogsContainer.find('.dialog__box'),
                    'dialogBoxPrefix': '#dialogbox-'
                };

                _log('[DIALOGS] init finished');
            }
        };

        var _highlightCurrent = function(dialogId) {
            if (dialogId === undefined) {
                dialogId = this.getCurrentIdFromURI();
            }
            var el = jQuery(this.DOM.dialogBoxPrefix + dialogId);
            if (el !== undefined) {
                jQuery(this.DOM.dialogBoxes).removeClass('active');
                el.addClass('active');
            }
        };

        var _setScrollbars = function() {
            if (!jQuery(this.DOM.conversationsArea).hasClass('os-host')) {
                jQuery(this.DOM.conversationsArea).overlayScrollbars({}).overlayScrollbars();
            }
        };

        var isInDialogue = function() {
            return (!isNaN(_getLastElementFromURI())) ? true : false;
        };

        function _getLastElementFromURI() {
            return window.location.pathname.split("/").pop();
        }

        /* --- Take out only needed functions to global scope --- */
        this.init = init;
        this.isInDialogue = isInDialogue;

    }).call(outstyle.dialogs);

    _log('[JQREADY] outstyle.dialogs object created');
});