/**
 * Outstyle Localstorage
 * This file must contain all the functions, related to storing temporary userdata (like filter values i.e.)
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
           xStore: false,
           CURRENT_CONTROLLER_ID: false * /
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
if (!outstyle.localstorage) {
    outstyle.localstorage = {};
}

jQuery(document).ready(function () {
    (function () {
        "use strict";

        /* https://github.com/florian/xStore/blob/master/docs/api-documentation.md */
        this.filters = new xStore("filters:", localStorage);

        /* Setting up intial values for storage, fallback to empty if there is none */
        this.filters.set({
            news: this.filters.get('news', []),
            articles: this.filters.get('article', []),
            videoz: this.filters.get('videoz', []),
            releases: this.filters.get('releases', []),
            reviews: this.filters.get('reviews', []),
            events: this.filters.get('events', []),
            schools: this.filters.get('schools', [])
        });

        /* Restores values from localStorage by provided url */
        this.addDataToQueryString = function (url = '/', controllerId = 'news') {
            var categoryFilters = '';
            var filtersString = '';

            if (url == '/api/' + controllerId + '/show' ||
                url == '/' + controllerId) {
                categoryFilters = outstyle.localstorage.filters.get(controllerId);
            }

            if (categoryFilters) {
                jQuery.each(categoryFilters, function (key, value) {
                    filtersString = filtersString + '&categories[' + value + ']=' + value;
                });
            }

            _log("[LOCALSTORAGE] values restored for " + url);
            return filtersString;
        };



        /* ! --- INTERCOOLER BINDS --- */

        jQuery(document).on("beforeAjaxSend.ic", function (event, settings) {
            if (typeof CURRENT_CONTROLLER_ID !== 'undefined') {
                var localStorageData = outstyle.localstorage.addDataToQueryString(settings.url, CURRENT_CONTROLLER_ID);
                if (localStorageData) {
                    settings.data = settings.data + localStorageData;
                }
            }
        });

        /* --- INTERCOOLER BINDS END --- */



    }.call(outstyle.localstorage));

    _log("[✔️][JQREADY] outstyle.localstorage object created");
});