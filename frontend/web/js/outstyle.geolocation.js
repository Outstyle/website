/**
 * Outstyle Geolocation JS Module
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
          Intercooler: false,
           _log: false */
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
if (!outstyle.geolocation) {
    outstyle.geolocation = {};
}

jQuery(document).ready(function () {
    (function () {
        "use strict";

        jQuery("body").on("geolocation", function (event, DOM) {
            if (!DOM.$container.hasClass('geolocation-binded')) {
                init(DOM);
                DOM.$container.addClass('geolocation-binded');
            }
        });

        var init = function (DOM = {}) {
            /* Select2 country stuff */
            var country = jQuery('#geolocation_country'),
                city = jQuery("#geolocation_city"),
                category = jQuery("#geolocation_category");

            /* Initially everything is hidden */
            country.parent('.field-school-country').hide();
            city.parent('.field-school-city').hide();
            category.parent('.field-school-category').hide();

            /* Initial list of countries to work with */
            jQuery.ajax({
                dataType: "json",
                url: "/api/school/get?geodata",
                success: function (data) {

                    /* Show countries list */
                    country.parent('.field-school-country').show();
                    country.select2({
                        data: data.countries
                    });

                    /**
                     * Show corresponding cities of chosen country
                     * [city.empty().trigger('change')] is needed for reinit Select2 data.
                     * @see http://stackoverflow.com/a/35773629
                     */
                    country.on('select2:select', function (evt) {
                        var country_id = parseInt(jQuery(this).val());

                        if (country_id) {
                            city.parent('.field-school-city').show();
                            category.parent('.field-school-category').hide();

                            city.empty().trigger('change');
                            city.select2({
                                data: data.cities[country_id],
                                escapeMarkup: function (markup) {
                                    return markup;
                                },
                                templateResult: formatDropdownCity,
                                templateSelection: formatDropdownCitySelection
                            });

                        } else {
                            city.parent('.field-school-city').hide();
                            category.parent('.field-school-category').hide();
                        }
                    });

                    /**
                     * Show categories after country has been chosen
                     * @see: https://select2.github.io/options.html#events for 'select2:select' event
                     * @see: http://intercoolerjs.org/examples/typeahead.html for 'Intercooler.triggerRequest' function
                     */
                    city.on('select2:select', function (evt) {
                        var country_id = parseInt(country.val()),
                            chosen_city_id = parseInt(jQuery(this).val());

                        jQuery.each(data.cities[country_id], function (key, city) {
                            if (city.id == chosen_city_id) {
                                var objects = city.objects.join();
                                jQuery('#geolocation_cities_query').val(objects);
                            }
                        });

                        Intercooler.triggerRequest(jQuery('#geolocation_cities_query'));

                        if (chosen_city_id) {
                            city.parent('.field-school-city').show();
                            category.parent('.field-school-category').show();
                            category.select2();

                        } else {
                            category.parent('.field-school-category').hide();
                        }

                    });

                }
            });

            /* TODO: Select2 visual stuff */
            function formatDropdownCity(data) {
                if (data.loading) return data.text;
                var markup = "<div class='select2-result-datacity clearfix'>" + data.text + "</div>";
                return markup;
            }

            function formatDropdownCitySelection(data) {
                return data.text;
            }


        };

        jQuery(document).on("beforeAjaxSend.ic", function (event, settings) {

            var countryId = parseInt(jQuery('#geolocation_country').val()),
                schoolsId = jQuery('#geolocation_cities_query').val();

            settings.data = settings.data +
                '&countryId=' + countryId +
                '&schoolsId=' + schoolsId;

        });

        /* --- Take out only needed functions to global scope --- */
        this.init = init;
    }.call(outstyle.geolocation));

    _log("[✔️][JQREADY] outstyle.geolocation object created");
});