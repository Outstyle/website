/**
 * Outstyle School JS Module
 * Depends on: JQuery
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
if (!outstyle.schools) {
    outstyle.schools = {};
}

jQuery(document).ready(function () {

    /* Needed for hiding initial content, intended for crawlers.
    Since it must be there on a page for a robot, we hide initial items from user.
    Hiding is necessary because user can have filters chosen, and that filters
    will override initial content. To prevent blinking of a content zone,
    we're hiding the items from a user and reading his filter settings instead. */
    jQuery("#outstyle_school .school__item--initial").css({
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
        jQuery("body").on("school", function (event, data) {
            setTimeout(function () {
                jQuery('#outstyle_school .school')
                    .packery()
                    .packery('destroy');

                init(event.type, data);
            }, 120);
        });

        /* --- GLOBAL BINDS END --- */



        /* ! --- INTERCOOLER BINDS --- */

        jQuery(document).on("beforeAjaxSend.ic", function (event, settings) {

            /* --- Also we need to prepend filter containter back to prevent it's disappearing after AJAX call --- */
            jQuery("#filter-box").prependTo("#outstyle_school").hide();
            jQuery("#school-filter-block--geolocation").insertAfter("#outstyle_school");

            if (jQuery('#cool_loader').length === 0) {
                jQuery("#outstyle_school").before('<img src="/frontend/web/images/images/breakdance_loader.gif" class="school__loader" id="cool_loader">');
            }
        });
        /* --- INTERCOOLER BINDS END --- */


        var init = function (eventType = '', data = {}) {
            var $container = jQuery("#outstyle_school");

            if ($container.length) {
                var DOM = {
                    'forElement': eventType,
                    '$container': $container,
                    '$grid': $container.find('.o-grid--wrap.school'),
                    '$page': $container.find('#page'),
                    '$contentHeight': $container.find('#contentHeight')
                };

                if (data.page) {
                    DOM.$page.val(data.page);
                }

                if (data.contentHeight) {
                    DOM.$contentHeight.val(data.contentHeight);
                    DOM.$container.css({
                        "min-height": data.contentHeight + "px",
                    });
                }

                jQuery('body').trigger('checkboxesInit', DOM.forElement);
                jQuery('body').trigger('geolocation', DOM);

                /* --- We need to initialize Packery at start --- */
                jQuery(DOM.$grid)
                    .packery({
                        itemSelector: '.block__item',
                        gutter: 0,
                        resize: true,
                        percentPosition: true
                    })
                    .off('layoutComplete').on('layoutComplete', function () {
                        /* --- Fit text size for each Packery block --- */
                        jQuery('.block__title').preciseTextResize({
                            parent: '.overlay',
                            widthOffset: 1,
                            heightOffset: 1
                        });

                        jQuery('.block__item .overlay').show();
                        jQuery('#outstyle_school, #school-filter-block--geolocation').css({
                            'visibility': 'visible'
                        });

                    })
                    .packery('layout')
                    .find('.overlay')
                    .show();

                setTimeout(function () {
                    echo.init({
                        offset: 1000
                    });

                    /* --- Also we need to prepend filter containter back to prevent it's disappearing after AJAX call --- */
                    jQuery("#school-filter").prependTo("#outstyle_school").css({
                        'visibility': 'visible'
                    });

                    /* --- This stuff is needed for triggering 'scroll-on-view' element,
                    so it could be on a user's viewport! --- */
                    /* Also initial elements must be on a page that is loaded without any JS,
                    for crawling spiders to be able to index the data */
                    jQuery("#outstyle_school .school__item--initial").hide();
                    jQuery("#cool_loader").hide();

                }, 200);

                _log("[SCHOOLS] outstyle.schools.init finished");
            }
        };

        /* --- Take out only needed functions to global scope --- */
        this.init = init;
    }.call(outstyle.schools));

    _log("[✔️][JQREADY] outstyle.schools object created");
});

/*
    TODO: move this to goolgemaps
    Schools single page scripts init
    Used:
    - echoJS for lazy load images:          https://www.npmjs.com/package/echo-js
    - PreciseTextResize for text:           @frontend/web/js/misc/preciseTextResize.js
    - wayjs for two-way data-binding:       https://github.com/gwendall/way.js
    - jQuery Mousewheel for OwlCarousel:    https://github.com/jquery/jquery-mousewheel (Owl Carousel built-in)
    - jQuery OwlCarousel:                   https://owlcarousel2.github.io/OwlCarousel2/
*/
function schoolInit() {

    var mapDiv = jQuery('#map__canvas--single');
    var carouselDiv = jQuery('.owl-carousel');

    /* GOOGLE MAPS INIT */
    function initGoogleMap(mapDiv) {
        var location = {
            lat: Number(mapDiv.attr('data-lat')),
            lng: Number(mapDiv.attr('data-lng'))
        };
        var options = {
            center: location,
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: [{
                "featureType": "all",
                "elementType": "labels",
                "stylers": [{
                    "visibility": "on"
                }]
            }, {
                "featureType": "all",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "saturation": 36
                }, {
                    "color": "#000000"
                }, {
                    "lightness": 40
                }]
            }, {
                "featureType": "all",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "visibility": "on"
                }, {
                    "color": "#000000"
                }, {
                    "lightness": 16
                }]
            }, {
                "featureType": "all",
                "elementType": "labels.icon",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "administrative",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#000000"
                }, {
                    "lightness": 20
                }]
            }, {
                "featureType": "administrative",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#000000"
                }, {
                    "lightness": 17
                }, {
                    "weight": 1.2
                }]
            }, {
                "featureType": "administrative.country",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#e5c163"
                }]
            }, {
                "featureType": "administrative.locality",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#c4c4c4"
                }]
            }, {
                "featureType": "administrative.neighborhood",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#e5c163"
                }]
            }, {
                "featureType": "landscape",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#000000"
                }, {
                    "lightness": 20
                }]
            }, {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#000000"
                }, {
                    "lightness": 21
                }, {
                    "visibility": "on"
                }]
            }, {
                "featureType": "poi.business",
                "elementType": "geometry",
                "stylers": [{
                    "visibility": "on"
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#e5c163"
                }, {
                    "lightness": "0"
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#ffffff"
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#e5c163"
                }]
            }, {
                "featureType": "road.arterial",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#000000"
                }, {
                    "lightness": 18
                }]
            }, {
                "featureType": "road.arterial",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#575757"
                }]
            }, {
                "featureType": "road.arterial",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#ffffff"
                }]
            }, {
                "featureType": "road.arterial",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#2c2c2c"
                }]
            }, {
                "featureType": "road.local",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#000000"
                }, {
                    "lightness": 16
                }]
            }, {
                "featureType": "road.local",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#999999"
                }]
            }, {
                "featureType": "transit",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#000000"
                }, {
                    "lightness": 19
                }]
            }, {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#000000"
                }, {
                    "lightness": 17
                }]
            }]
        };
        var map = new google.maps.Map(mapDiv[0], options);
        var marker = new google.maps.Marker({
            position: location,
            map: map
        });
    }

    /* OWL CAROUSEL INIT */
    function initOwlCarousel(carouselDiv) {
        carouselDiv.owlCarousel({
            loop: true,
            lazyLoad: true,
            items: 4,
            nav: false,
        });
        carouselDiv.on('mousewheel', '.owl-stage', function (e) {
            if (e.deltaY > 0) {
                carouselDiv.trigger('next.owl');
            } else {
                carouselDiv.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }


    jQuery('.block__item .overlay').show();

    initGoogleMap(mapDiv);
    initOwlCarousel(carouselDiv);

    /* Error handling: no user image is available */
    jQuery("img.avatar").error(function () {
        jQuery(this).unbind("error").attr("src", "/css/i/broken/avatar_128x128.png");
    });
}