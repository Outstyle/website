/**
 * Outstyle Errors Handler
 * This file must contain all the functions, related to errors (handling, showing, etc.)
 * Author: <scsmash3r@gmail.com>
 * Copyright (c) 2018 [SC]Smash3r; Beerware
 * @preserve
 */
/* jshint esversion: 6 */

/* Define global namespaces */
if ("undefined" == typeof outstyle) {
    var outstyle = {};
}
if (!outstyle.errors) {
    outstyle.errors = {};
}

jQuery(document).ready(function() {
    (function() {
        "use strict";

        jQuery('body').on('showErrors', function(event, data) {
            ohSnapX();
            jQuery.each(data, function(key, value) {
                ohSnap(decodeURIComponent(value).replace(/\+/g, " "), {
                    'color': 'red'
                });
            });
        });

    }).call(outstyle.errors);

    _log('[JQREADY] outstyle.errors object created');
});