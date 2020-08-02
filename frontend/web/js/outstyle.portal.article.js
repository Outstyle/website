/**
 * Outstyle Articles JS Module
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
if (!outstyle.articles) {
    outstyle.articles = {};
}

jQuery(document).ready(function () {

    /* Needed for hiding initial content, intended for crawlers.
    Since it must be there on a page for a robot, we hide initial items from user.
    Hiding is necessary because user can have filters chosen, and that filters
    will override initial content. To prevent blinking of a content zone,
    we're hiding the items from a user and reading his filter settings instead. */
    jQuery("#outstyle_articles .article__item--initial").hide();

    (function () {
        "use strict";


        /* ! --- GLOBAL BINDS --- */

        /**
         * Triggering on 'article' event from ArticleController
         * See X-IC-Trigger headers: http://intercoolerjs.org/reference.html
         */
        jQuery("body").on("article releases reviews videoz", function (event, data) {
            /* Since article can be represented as other entities, add them up in `.on` event */
            init(event.type, data);
        });

        /* --- GLOBAL BINDS END --- */



        /**
         * Init function for articles
         * @return null
         */
        var init = function (eventType = '', data = {}) {
            var $articlesContainer = jQuery("#outstyle_articles");

            if ($articlesContainer.length) {
                var DOM = {
                    'forElement': eventType,
                    '$articlesContainer': $articlesContainer,
                    '$articlesPage': $articlesContainer.find('#page'),
                    '$articlesContainerHeight': $articlesContainer.find('#contentHeight')
                };

                if (data.page) {
                    DOM.$articlesPage.val(data.page);
                }

                if (data.contentHeight) {
                    DOM.$articlesContainerHeight.val(data.contentHeight);
                    DOM.$articlesContainer.css({
                        "min-height": data.contentHeight + "px",
                    });
                }

                jQuery('body').trigger('checkboxesInit', DOM.forElement);

                setTimeout(function () {
                    echo.init({
                        offset: 350,
                        callback: function (element) {
                            jQuery(element).load(function () {
                                jQuery(element).next("img.article__image--overlay").show();
                            });
                        },
                    });

                    /* --- Also we need to prepend filter containter back to prevent it's disappearing after AJAX call --- */
                    jQuery("#articles-filter").prependTo("#outstyle_articles").css({
                        visibility: "visible",
                    });

                    /* --- This stuff is needed for triggering 'scroll-on-view' element,
                    so it could be on a user's viewport! --- */
                    /* Also initial elements must be on a page that is loaded without any JS,
                    for crawling spiders to be able to index the data */
                    jQuery("#outstyle_articles .article__item--initial").hide();
                }, 200);


                _log("[ARTICLES] outstyle.articles.init finished");
            }
        };


        /* --- Take out only needed functions to global scope --- */
        this.init = init;
    }.call(outstyle.articles));

    _log("[✔️][JQREADY] outstyle.articles object created");
});