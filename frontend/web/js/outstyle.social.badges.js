/**
 * Outstyle Badges JS Functions
 * Author: <scsmash3r@gmail.com>
 * Copyright (c) 2018 [SC]Smash3r; Beerware
 * @preserve
 */
/* jshint esversion: 6 */

/* Define global namespaces */
if ("undefined" == typeof outstyle) {
    var outstyle = {};
}
if (!outstyle.badges) {
    outstyle.badges = {};
}

jQuery(document).ready(function() {
    (function() {
        "use strict";

        /* --- GLOBAL BINDS --- */

        /* --- Global 'ondocumentready' binds for calling out the function from other modules or for IC --- */
        jQuery("body").on("appendBadge", function(event, data) {
            _appendBadgeToElement(data.id, data.color, data.type, data.text);

            _log('[BADGES] appendBadge for ' + data.id + ' triggered');
        });

        /* Refresh badges on every ic request complete */
        jQuery("body").on("complete.ic", function(evt, elt, data) {
            setTimeout(function() {
                init();
            }, 325);
        });

        /* --- GLOBAL BINDS END --- */

        /**
         * Init function for badges
         * @return null
         */
        var init = function() {
            _appendBadgeToElement('#menu__item-friends a', 'red', 'shaded', OUTSTYLE_GLOBALS.owner.friends.count.pending);
            _appendBadgeToElement('#menu__item-messages a', 'red', 'shaded', OUTSTYLE_GLOBALS.owner.messages.count.unread);
            _appendBadgeToElement('#friends__roundbutton-all a', 'red', 'shaded', OUTSTYLE_GLOBALS.owner.friends.count.pending);

            _log('[BADGES] init finished');
        };


        /**
         * Append badge to element
         * @param {string} elementId  CSS selector
         * @param {string} color     CSS class or color, according to BlazeUI
         * @param {string} type      CSS class or color, according to BlazeUI
         * @param {string} text
         * @see {@link https://www.blazeui.com/components/badges|BlazeUI Badges}
         */
        var _appendBadgeToElement = function(elementId, color, type, text) {
            var existingBadge = jQuery(elementId).find('span.c-badge');
            if (existingBadge.length <= 0 && text != 0) {
                var badge = jQuery('<span class="c-badge c-badge--rounded c-badge--' + color + ' c-badge--' + type + '">' + text + '</span>');
                jQuery(elementId).append(badge);
                badge.addClass('popout');
            } else {
                existingBadge.html(text);
                if (text === 0) {
                    existingBadge.remove();
                }
            }
        };

        this.init = init;

    }).call(outstyle.badges);

    outstyle.badges.init();
    _log('[JQREADY] outstyle.badges object created');
});