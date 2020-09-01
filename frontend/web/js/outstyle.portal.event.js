/**
 * Outstyle Events JS Module
 * Depends on: JQuery, Intercoolerjs
 * Author: <scsmash3r@gmail.com>
 * Copyright (c) 2020 [SC]Smash3r; Beerware
 * @preserve
 */

/**
 * JSHint options
 * @see https://jshint.com/docs/options/
 */
/*globals jQuery: false,
           _log: false,
           echo: false */
/*jshint esversion: 6 */
/*jshint maxparams: 3 */
/*jshint undef: true */
/*jshint indent: 4 */
/*jshint unused: true */
/*jshint browser: true */

/* Define global namespaces */
if ("undefined" == typeof outstyle) {
    var outstyle = {};
}
if (!outstyle.events) {
    outstyle.events = {};
}

jQuery(document).ready(function () {

    /* Needed for hiding initial content, intended for crawlers.
    Since it must be there on a page for a robot, we hide initial items from user.
    Hiding is necessary because user can have filters chosen, and that filters
    will override initial content. To prevent blinking of a content zone,
    we're hiding the items from a user and reading his filter settings instead. */
    jQuery("#outstyle_events .event__item--initial").hide();

    (function () {
        "use strict";


        /* ! --- GLOBAL BINDS --- */

        /**
         * See X-IC-Trigger headers: http://intercoolerjs.org/reference.html
         */
        jQuery("body").on("events", function (event, data) {
            // Timeout is needed to refresh DOM elements like `contentHeight` from older state
            setTimeout(function () {
                init(event.type, data);
            }, 120);
        });

        /* --- GLOBAL BINDS END --- */



        /**
         * Init function for articles
         * @return null
         */
        var init = function (eventType = '', data = {}) {
            var $container = jQuery("#outstyle_events");

            if ($container.length) {
                var DOM = {
                    'forElement': eventType,
                    '$container': $container,
                    '$page': $container.find('#page'),
                    '$contentHeight': $container.find('#contentHeight')
                };

                if (data.page) {
                    DOM.$page.val(data.page);
                }

                if (data.contentHeight) {
                    DOM.$contentHeight.val(data.contentHeight);
                    DOM.$contentHeight.css({
                        "min-height": data.contentHeight + "px",
                    });
                }

                jQuery('body').trigger('checkboxesInit', DOM.forElement);

                setTimeout(function () {
                    echo.init({
                        offset: 350
                    });

                    /* --- Also we need to prepend filter containter back to prevent it's disappearing after AJAX call --- */
                    jQuery("#events-filter").prependTo("#outstyle_events").css({
                        'visibility': 'visible'
                    });

                    /* --- This stuff is needed for triggering 'scroll-on-view' element,
                    so it could be on a user's viewport! --- */
                    /* Also initial elements must be on a page that is loaded without any JS,
                    for crawling spiders to be able to index the data */
                    jQuery("#outstyle_events .event__item--initial").hide();
                }, 200);

                _log("[EVENTS] outstyle.events.init finished");
            }
        };

        /* --- Take out only needed functions to global scope --- */
        this.init = init;
    }.call(outstyle.events));

    _log("[✔️][JQREADY] outstyle.events object created");
});