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
if (!outstyle.events.single) {
    outstyle.events.single = {};
}

jQuery(document).ready(function () {
    (function () {
        "use strict";

        jQuery("body").on("eventsview", function (event, data) {
            setTimeout(function () {
                init(event.type, data);
            }, 120);
        });

        var init = function (eventType = '', data = {}) {
            var $container = jQuery("#outstyle_events-single");

            if ($container.length) {
                var DOM = {
                    'forElement': eventType,
                    '$container': $container,
                    '$mapContainer': $container.find('#map__canvas'),
                    '$mapToggler': $container.find('.map .toggleable')
                };

                /* Bind local events only once */
                if (!DOM.$container.hasClass('binded')) {
                    _bindLocalEvents(DOM);
                    DOM.$container.addClass('binded');
                }

                // TODO: Move this to separate broken images handler from all modules
                echo.init({
                    offset: 500,
                    callback: function () {
                        jQuery("img").error(function () {
                            jQuery(this).hide();
                        });
                    }
                });

                /* EVENT TITLE RESIZE */
                jQuery('.datebox__title').preciseTextResize({
                    parent: '.datebox__wrap',
                    grid: [{
                        0: {
                            60: {
                                1: 72,
                                4: 58,
                                10: 46,
                                15: 40,
                                20: 38,
                                25: 34,
                                30: 26
                            },
                            100: {
                                1: 58,
                                4: 42,
                                10: 38,
                                15: 34,
                                20: 32,
                                25: 28
                            }
                        },
                    }],
                });


                _log("[EVENTS.SINGLE] outstyle.events.single.init finished");
            }
        };


        /**
         * Local events are meant to be binded onto DOM nodes that are not in global scope
         * (means that elements are not 'body' nor 'document')
         * @param  {Object} [DOM={}] Current DOM nodes
         */
        var _bindLocalEvents = function (DOM = {}) {
            DOM.$mapToggler.click(function () {
                DOM.$mapContainer.toggleClass('visible');
                jQuery('body').trigger('googleMapsInit', DOM);

                jQuery(this).find('i').toggleClass('zmdi-chevron-down').toggleClass('zmdi-chevron-up');
                jQuery(this).find('b').toggle();
            });

            jQuery("#events-single-recommended .grayscale, #events-single-similar .grayscale").hover(function () {
                jQuery(this).toggleClass('grayscale');
            });

            _log('🔥 [EVENTS.SINGLE] local events binding finished');
        };


        this.init = init;
    }.call(outstyle.events.single));

    _log("[✔️][JQREADY] outstyle.events.single object created");

    // Self-invoking init in case if page was loaded without AJAX
    jQuery('body').trigger('eventsview');
});