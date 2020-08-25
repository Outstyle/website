/**
 * Outstyle News JS Module
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
if (!outstyle.news) {
    outstyle.news = {};
}
/*
    echoJS [https: //www.npmjs.com/package/echo-js]
    packery [http: //packery.metafizzy.co/]
    PreciseTextResize [preciseTextResize.js]

    TODO: Redo this using ic - scroll - offset: http: //intercoolerjs.org/attributes/ic-scroll-offset.html
    I also need to mention, that we have some really big stuck with Packery + 'scrolled-in-view'
    event for loading more news. So the possible solution could be in destroying Packery instance every time after ajax event and setting it up  again instead 'reloadItems'
    Otherwise we will get continious AJAX requests.

    jQuery "news" trigger fires after ajaxComplete request when certain Intercooler header was accepted.
    See 'X-IC-Trigger' in 'NewsController'
*/
jQuery(document).ready(function () {

    /* Needed for hiding initial content, intended for crawlers.
    Since it must be there on a page for a robot, we hide initial items from user.
    Hiding is necessary because user can have filters chosen, and that filters
    will override initial content. To prevent blinking of a content zone,
    we're hiding the items from a user and reading his filter settings instead. */
    jQuery('#outstyle_news .news__item--initial').css({
        'height': 50,
        'width': 50,
        'position': 'static'
    }).hide();

    (function () {
        "use strict";


        /* ! --- GLOBAL BINDS --- */

        /**
         * See X-IC-Trigger headers: http://intercoolerjs.org/reference.html
         */
        jQuery("body").on("news", function (event, data) {
            setTimeout(function () {
                // [!] Destroying layout is needed to fill the small or large square gaps with every new page request
                jQuery('#outstyle_news .news')
                    .packery()
                    .packery('destroy');

                init(event.type, data);
            }, 120);
        });

        /* --- GLOBAL BINDS END --- */



        /* ! --- INTERCOOLER BINDS --- */

        jQuery(document).on("beforeAjaxSend.ic", function (event, settings) {

            /* --- Also we need to prepend filter containter back to prevent it's disappearing after AJAX call --- */
            jQuery("#filter-box").prependTo("#outstyle_news").hide();

            /* --- Some neat loader for the news page, showing before each filtering event --- */
            if (jQuery('#cool_loader').length === 0) {
                jQuery("#outstyle_news").before('<img src="/frontend/web/images/images/breakdance_loader.gif" class="news__loader" id="cool_loader">');
            }
        });
        /* --- INTERCOOLER BINDS END --- */



        /**
         * Init function for news
         * @return null
         */
        var init = function (eventType = '', data = {}) {
            var $newsContainer = jQuery("#outstyle_news");

            if ($newsContainer.length) {
                var DOM = {
                    'forElement': eventType,
                    '$newsContainer': $newsContainer,
                    '$newsGrid': $newsContainer.find('.o-grid--wrap.news'),
                    '$newsPage': $newsContainer.find('#page'),
                    '$newsContainerHeight': $newsContainer.find('#contentHeight'),
                    '$newsOverlay': $newsContainer.find('.news__overlay')
                };

                if (data.page) {
                    DOM.$newsPage.val(data.page);
                }

                if (data.contentHeight) {
                    DOM.$newsContainerHeight.val(data.contentHeight);
                    DOM.$newsContainer.css({
                        "min-height": data.contentHeight + "px",
                    });
                }

                jQuery('body').trigger('checkboxesInit', DOM.forElement);

                /* --- We need to initialize Packery at start --- */
                jQuery(DOM.$newsGrid)
                    .packery({
                        itemSelector: '.news__item',
                        gutter: 0,
                        percentPosition: true
                    })
                    .packery('layout');

                /* Bind local events only once */
                if (!DOM.$newsContainer.hasClass('binded')) {
                    _bindLocalEvents(DOM);
                    DOM.$newsContainer.addClass('binded');
                }

                _log("[NEWS] outstyle.news.init finished");
            }
        };


        /**
         * Local events are meant to be binded onto DOM nodes that are not in global scope
         * (means that elements are not 'body' nor 'document')
         * @param  {Object} [DOM={}] Current DOM nodes
         */
        var _bindLocalEvents = function (DOM = {}) {

            /* --- If Packery has finally loaded - initiating some other events --- */
            jQuery(DOM.$newsGrid).on('layoutComplete', function (event, laidOutItems) {

                /* --- Fit text size for each Packery block --- */
                jQuery('.news__title').preciseTextResize({
                    parent: '.news__overlay',
                    widthOffset: 1,
                    heightOffset: 1
                });

                jQuery('#outstyle_news .news__item--initial').hide();
                jQuery('.news__overlay').show();
                jQuery(DOM.$newsContainer).css({
                    'visibility': 'visible'
                });

                jQuery.each(laidOutItems, function (key, value) {
                    jQuery(value.element).find('.news__filter-button').on("click", function () {
                        jQuery(this).after(jQuery('#filter-box').slideDown('fast'));
                    });
                });
            });

            jQuery('#news-filter-form input[type=checkbox]').on("change", function () {
                if (this.checked) {
                    jQuery(this).next('i').removeClass('zmdi-circle-o').addClass('zmdi-circle');
                } else {
                    jQuery(this).next('i').removeClass('zmdi-circle').addClass('zmdi-circle-o');
                }
            });

            echo.init({
                offset: 1000
            });

            _log('🔥 [NEWS] local events binding finished');
        };


        /* --- Take out only needed functions to global scope --- */
        this.init = init;
    }.call(outstyle.news));

    _log("[✔️][JQREADY] outstyle.news object created");
});