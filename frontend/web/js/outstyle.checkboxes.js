/**
 * Outstyle Checkboxes Handler
 * This file must contain all the functions, related to checkboxes (handling, showing, etc.)
 * Author: <scsmash3r@gmail.com>
 * Copyright (c) 2020 [SC]Smash3r; Beerware
 * @preserve
 */

/*jshint esversion: 6 */

/* Define global namespaces */
if ("undefined" == typeof outstyle) {
    var outstyle = {};
}
if (!outstyle.checkboxes) {
    outstyle.checkboxes = {};
}

jQuery(document).ready(function () {
    (function () {
        "use strict";

        var $checkboxesContainer = jQuery(".checkbox__wrap");
        var $fakeCheckboxesContainer = jQuery(".checkbox__wrap--disabled");


        /* ! --- GLOBAL BINDS --- */

        /**
         * Main init for checkboxes
         * Must be called for any masked checkboxes on page
         *
         * @param   {String}  forElement      Controller ID name
         */
        jQuery("body").on("checkboxesInit", function (event, forElement) {
            init(forElement);
        });

        /* --- GLOBAL BINDS END --- */



        /* ! --- INTERCOOLER BINDS --- */

        jQuery(document).on("beforeAjaxSend.ic", function () {
            /* Hide all checkboxes before AJAX request begins,
            swapping them with fake (disabled) ones */
            $checkboxesContainer.hide();
            $fakeCheckboxesContainer.show();
        });

        jQuery('body').on("complete.ic", function () {
            /* Returning back active checkboxes after AJAX is complete.
                Delay is needed cause of possible page switch (new elements),
                since IC takes 40ms for content swapping */
            setTimeout(function () {
                $fakeCheckboxesContainer.hide();
                $checkboxesContainer.show();
            }, 125);
        });

        /* --- INTERCOOLER BINDS END --- */



        /**
         * Init function for checkboxes
         * @return null
         */
        var init = function (forElement) {
            var $checkboxesContainer = jQuery(".checkbox__wrap");
            var $fakeCheckboxesContainer = jQuery(".checkbox__wrap--disabled");

            if ($checkboxesContainer.length) {
                var DOM = {
                    forElement: forElement,
                    $checkboxesContainer: $checkboxesContainer,
                    $checkboxesInputs: $checkboxesContainer.find("input[type=checkbox]"),
                    $fakeCheckboxesContainer: $fakeCheckboxesContainer,
                };

                /* Bind local events only once */
                if (!DOM.$checkboxesContainer.hasClass('checkbox-binded')) {
                    _bindLocalEvents(DOM);
                    DOM.$checkboxesContainer.addClass('checkbox-binded');
                }

                setTimeout(function () {
                    _restoreCheckboxes(DOM);
                }, 125);


                _log("[CHECKBOXES] outstyle.checkboxes.init finished");
            }
        };


        /**
         * Local events are meant to be binded onto DOM nodes that are not in global scope
         * (means that elements are not 'body' nor 'document')
         * @param  {Object} [DOM={}] Current DOM nodes
         */
        var _bindLocalEvents = function (DOM = {}) {
            /**
             * Working with masked checkboxes
             * Triggering on 'change' event and toggling element's classes to show 'fake' checkbox element
             * This is needed to prevent multiple AJAX sends
             * Basically this substitutes an elements to a fake 'noninteractable' elements during AJAX call
             */
            DOM.$checkboxesInputs.on("change", function () {
                var otherFilter = parseInt(jQuery(this).val(), 10);
                var activeCheckbox = jQuery(
                    DOM.$checkboxesContainer.find("input[type=checkbox][value=" + otherFilter + "]")
                );

                if (jQuery(this).is(":checked")) {
                    jQuery(this).parent().addClass("active");
                    jQuery(this).parent().next().addClass("active");
                    activeCheckbox
                        .next("i")
                        .removeClass("zmdi-circle-o")
                        .addClass("zmdi-circle");

                    /* Store newly checked value to localStorage */
                    outstyle.localstorage.filters.push(DOM.forElement, otherFilter);

                } else {
                    jQuery(this).parent().removeClass("active");
                    jQuery(this).parent().next().removeClass("active");
                    activeCheckbox
                        .next("i")
                        .removeClass("zmdi-circle")
                        .addClass("zmdi-circle-o");

                    /* Remove checked value from localStorage */
                    var storedFilters = outstyle.localstorage.filters.get(DOM.forElement);
                    var checkedCheckboxes = storedFilters.filter(function (elem) {
                        return elem != otherFilter;
                    });
                    outstyle.localstorage.filters.set(DOM.forElement, checkedCheckboxes);
                }


                _log("[CHECKBOXES] change event fired");
            });

            _log('🔥 [CHECKBOXES] local events binding finished');
        };


        /**
         * Restores values from localStorage, based on element type as key
         * @param  {Object} [DOM={}] Current DOM nodes
         */
        var _restoreCheckboxes = function (DOM = {}) {
            var categories = outstyle.localstorage.filters.get(DOM.forElement);
            if (categories) {

                /* For usual checkboxes */
                if (jQuery('#filter-form').length) {
                    jQuery.each(categories, function (key, value) {
                        jQuery(
                            "#filter-form input[type=checkbox][value=" + value + "]"
                        ).attr("checked", true);
                        jQuery("#filter-form input[type=checkbox][value=" + value + "]")
                            .parent()
                            .addClass("active");
                        jQuery("#filter-form div[data-fake-id=" + value + "]")
                            .parent()
                            .addClass("active");
                    });
                }

                /* For `news` checkboxes, since they have different layout (boxed) */
                if (jQuery('#news-filter-form').length) {
                    jQuery.each(categories, function (key, value) {
                        var currentCheckbox = jQuery(
                            "#news-filter-form input[type=checkbox][value=" + value + "]"
                        );
                        currentCheckbox.attr("checked", true);
                        currentCheckbox
                            .parent()
                            .addClass("active");
                        currentCheckbox
                            .next('i')
                            .removeClass('zmdi-circle-o')
                            .addClass('zmdi-circle');
                    });
                }
            }
            _log("[CHECKBOXES] values restored for " + DOM.forElement);
        };

        /* --- Take out only needed functions to global scope --- */
        this.init = init;

    }.call(outstyle.checkboxes));

    _log("[✔️][JQREADY] outstyle.checkboxes object created");
});