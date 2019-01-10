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

        var _path = '/messages'; /* Since dialogs are a part of messages and are IN messages section */

        /* Global 'ondocumentready' bind for calling out the function from other modules */
        jQuery("body").on("dialogsInit", function(event, data) {
            init();
        });

        jQuery("body").on("highlightCurrentDialogBox", function(event, data) {
            _highlightCurrentDialogBox();
        });

        /* X-IC-Trigger @ DialogsController */
        jQuery("body").on("dialogsLoaded", function(event, data) {
            jQuery('#conversations__loadmore').remove();
            setTimeout(function() {
                jQuery('body').trigger('dialogsInit');
                jQuery('body').trigger('messagesInit');
            }, 150);
        });

        jQuery(document).on("beforeAjaxSend.ic", function(event, settings) {
            if (settings.url == '/api/messages/get') {
                var dialogId = _getLastElementFromURI();
                settings.data = settings.data + '&dialogId=' + dialogId;
            }
        });

        /* Triggered before a snapshot is taken for history - unwire custom JS here, restore initial page state */
        jQuery(document).on("beforeHistorySnapshot.ic", function(evt, target) {
            if (window.location.pathname.indexOf(_path) === 0) {
                jQuery('.dialog__box').removeClass('active');
            }
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
                    '$conversationsArea': $dialogsContainer.find('#conversations_area')
                };

                _highlightCurrentDialogBox();

                _log('[DIALOGS] init finished');
            }
        };

        var _highlightCurrentDialogBox = function() {
            var dialogId = _getLastElementFromURI();
            var $el = jQuery('#dialogbox-' + dialogId);
            if ($el !== undefined) {
                $el.addClass('active');
            }

            _log('[DIALOGS] highlight dialog box finished');
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