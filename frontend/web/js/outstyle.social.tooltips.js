/**
 * Outstyle Tooltips JS Functions
 * Depends on: JQuery, Tooltipster
 * This file must contain all the functions, related to tooltip elements
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
if (!outstyle.tooltips) {
    outstyle.tooltips = {};
}

jQuery(document).ready(function() {
    (function() {
        "use strict";

        /* ! --- GLOBAL BINDS --- */

        /* --- Global 'ondocumentready' binds for calling out the function from other modules or for IC --- */
        jQuery('body').on('tooltipsInit', function(event, DOM) {
            init(DOM);
        });

        /* --- GLOBAL BINDS END --- */

        /**
         * Setting up tooltips on DOM elements
         * @see: https://iamceege.github.io/tooltipster/
         * @see: https://iamceege.github.io/tooltipster/#triggers
         * @param  {Object} [DOM={}]     jqObj with 'forElement' attr
         * @return null
         */
        var init = function(DOM = {}) {
            if (!jQuery.isEmptyObject(DOM)) {

                /* ! --- Tooltips init for messages section --- */
                if (DOM.forElement === 'messages') {
                    /* If certain elements already have tooltip applied */
                    if (jQuery(DOM.$messagesDialogOptionsButton).hasClass("tooltipstered")) {
                        return;
                    }

                    /* Activating tooltip for dialog options button in messages header section */
                    jQuery(DOM.$messagesDialogOptionsButton).tooltipster({
                        zIndex: 1337,
                        trigger: 'click',
                        side: 'bottom',
                        distance: -3,
                        contentAsHTML: true,
                        contentCloning: true,
                        interactive: true,
                        functionInit: function(instance) {
                            var content = jQuery('.dialog_options_tooltip_content');
                            instance.content(content);
                        },
                        functionReady: function() {
                            jQuery('.tooltip-close').on('click', function() {
                                jQuery(DOM.$messagesDialogOptionsButton).tooltipster('close');
                            });
                        },
                        functionAfter: function() {
                            jQuery('.dialog_options_tooltip_content').appendTo('.tooltip_templates');
                        }
                    });
                }

                _log('[TOOLTIPS] init for ' + DOM.forElement + ' finished');
            }
        };

        /* --- Take out only needed functions to global scope --- */
        this.init = init;

    }).call(outstyle.tooltips);

    _log('[JQREADY] outstyle.tooltips object created');
});




function photoalbumsTooltipsInit() {
    jQuery('#photo__editbutton').css('visibility', 'visible');
    if (jQuery('#photo__editbutton').hasClass("tooltipstered")) {
        return;
    }

    /* Activating tooltip on "+" button */
    /* @see: http://iamceege.github.io/tooltipster/ */
    jQuery('#photo__editbutton').tooltipster({
        zIndex: 1337,
        trigger: 'click',
        side: 'bottom',
        distance: -3,
        contentAsHTML: true,
        /* contentCloning: true -> Only for one instance (will not work if there are more than 2 tooltips on page) */
        contentCloning: true,
        interactive: true,
        functionInit: function(instance, helper) {
            var content = jQuery('#photos_edit_tooltip_content');
            instance.content(content);
        },
        functionAfter: function(instance, helper) {
            jQuery('#photos_edit_tooltip_content').appendTo('.tooltip_templates');
        }
    });
}

function photoalbumsTooltipsClose() {
    jQuery('#photo__editbutton').tooltipster('close');
}


function friendTooltipInit(elemId) {
    if (jQuery(elemId).hasClass("tooltipstered")) {
        return;
    }

    /* @see: http://iamceege.github.io/tooltipster/ */
    jQuery(elemId).tooltipster({
        zIndex: 1337,
        trigger: 'click',
        side: 'bottom',
        distance: -12,
        contentAsHTML: true,
        contentCloning: true,
        interactive: true,
        animationDuration: 0,
        functionInit: function(instance, helper) {
            var content = jQuery('.friend_options_tooltip_content');
            instance.content(content);
        }
    });
}

function messagesTooltipInit(DOM) {
    if (jQuery(elemId).hasClass("tooltipstered")) {
        return;
    }

    /* @see: http://iamceege.github.io/tooltipster/ */
    jQuery(elemId).tooltipster({
        zIndex: 1337,
        trigger: 'click',
        side: 'bottom',
        distance: -12,
        contentAsHTML: true,
        contentCloning: true,
        interactive: true,
        animationDuration: 0,
        functionInit: function(instance, helper) {
            var content = jQuery('.friend_options_tooltip_content');
            instance.content(content);
        }
    });
}