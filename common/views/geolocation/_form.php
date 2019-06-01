<?php

use yii\helpers\Html;

/*
 * Geolocation form
 * DB tables relation: 'geolocation', 'geolocation_cities', 'geolocation_countries'
 */

echo
Html::beginTag('div', ['class' => 'geolocation__box']),

  /* Already selected geolocation */
  Html::tag('div',
    Html::tag('div',

     '<div id="geolocation_fields_loader" class="loader loader--medium u-l"></div>'.

      Html::tag('label',
        Yii::t('app', 'Selected address').
          '&nbsp;'.
          Html::a('('.Yii::t('app', 'Change address').')',
            '#',
            [
              'id' => 'geolocation_address_change',
              'class' => 'c-text--small',
            ]
          ),
        [
          'class' => 'control-label',
          'for' => 'address_selected',
        ]
      ).

      /* This hidden field is needed for jQuery interaction, since we can't get a value from 'disabled' input o_O */
      Html::input('hidden', ucfirst(Yii::$app->controller->id).'[address_selected]', $geolocation->formatted_address ?? 0,
        [
          'id' => 'geolocation_address_selected_hidden',
          'class' => 'form-control select-events-address_selected',
        ]
      ).

      Html::input('text', 'address', $geolocation->formatted_address ?? 0,
        [
          'id' => 'geolocation_address_selected',
          'disabled' => 'disabled',
          'class' => 'form-control select-events-address',
        ]
      ),

      [
        'class' => 'col-md-12 form-group field-events-address_selected',
      ]
    ),

  [
    'id' => 'geolocation__selected',
    'class' => 'row',
  ]),

  Html::tag('div',

    /* Country select */
    Html::tag('div',
      Html::tag('label', Yii::t('app', 'Country'),
        [
          'class' => 'control-label',
          'for' => 'country',
        ]
      ).
      Html::dropDownList(ucfirst(Yii::$app->controller->id).'[country]', null, [],
        [
          'id' => 'geolocation_country',
          'class' => 'form-control select-country',
        ]
      ),
      ['class' => 'col-md-3 field-country']
    ).

    /* City select (shows only after country is selected) */
    Html::tag('div',
      Html::tag('label', Yii::t('app', 'City'),
        [
          'class' => 'control-label',
          'for' => 'city',
        ]
      ).
      Html::dropDownList(ucfirst(Yii::$app->controller->id).'[city]', null, [],
        [
          'id' => 'geolocation_city',
          'class' => 'form-control select-city',
        ]
      ),
      ['class' => 'col-md-3 field-city']
    ).

    /* Address select (shows only after city is selected) */
    Html::tag('div',
      Html::tag('label', Yii::t('app', 'Address'),
        [
          'class' => 'control-label',
          'for' => 'address',
        ]
      ).
      Html::dropDownList(ucfirst(Yii::$app->controller->id).'[address]', null, [],
        [
          'id' => 'geolocation_address',
          'class' => 'form-control select-address',
        ]
      ),
      ['class' => 'col-md-3 field-address']
    ).

    /* Geolocation name */
    Html::tag('div',
      Html::tag('label', Yii::t('app', 'Place name'),
        [
          'class' => 'control-label',
          'for' => 'placename',
        ]
      ).

      Html::textInput(ucfirst(Yii::$app->controller->id).'[placename]', $geolocation->name ?? '',
        [
          'id' => 'geolocation_placename',
          'class' => 'form-control select-placename',
        ]
      ),
      ['class' => 'col-md-3 field-placename']
    ),

  [
    'id' => 'geolocation__new',
    'class' => 'row',
  ]),

Html::endTag('div');

/*
    ----------------------------------------------------------------------------
    JS stuff, that is related ONLY to this view
    ----------------------------------------------------------------------------
*/
?>

<script>
jQuery(document).ready(function () {

  /* Select2 country stuff */
  var country = jQuery('#geolocation_country'),
      city = jQuery("#geolocation_city"),
      address = jQuery("#geolocation_address"),
      placename = jQuery("#geolocation_placename");

  /* Initially everything is hidden */
  country.parent('.field-country').hide();
  city.parent('.field-city').hide();
  address.parent('.field-address').hide();
  placename.parent('.field-placename').hide();

  /* Initial list of countries to work with */
  jQuery.ajax({
    dataType: "json",
    url: "/api/geo/vk/get?countries&formatted",
    delay: 500,
    success: function(data) {
      var dropdownitems = '';
      jQuery.each(data.vk, function(key, country) {
        dropdownitems += '<option value=' + country.id + '>' + country.title + '</option>';
      });
      country.parent('.field-country').show();
      country.append(dropdownitems).select2();
      initHide();
    }
  });

  country.on('select2:select', function (evt) {
    var country_id = jQuery(this).val();
    if (country_id != "0") {
      city.parent('.field-city').show();
      address.parent('.field-address').hide();
      city.select2({
          ajax: {
            url: "/api/geo/vk/get",
            dataType: 'json',
            delay: 500,
            data: function (params) {
              return {
                q: params.term,
                country_id: country_id
              };
            },
            processResults: function (data, params) {
              if (data.vk) {
                jQuery.each(data.vk, function(key, city) {
                  if (city.added) {
                    ohSnap(city.title + ' added to DB', {'color':'blue'});
                  }
                });
              }
              return {
                results: data.vk
              };
            },
            cache: true
          },
          escapeMarkup: function (markup) { return markup; },
          minimumInputLength: 3,
          templateResult: formatVkCity,
          templateSelection: formatVkCitySelection
        });
    } else {
      city.parent('.field-city').hide();
      address.parent('.field-address').hide();
    }
  });

  city.on('select2:select', function (evt) {
    var city_name = evt.params.data.title,
        country_name = country.select2('data')[0].text;

    address.parent('.field-address').show();
    address.select2({
        ajax: {
          url: "/api/geo/google/get?formatted",
          dataType: 'json',
          delay: 500,
          data: function (params) {
            return {
              address: params.term+','+city_name+','+country_name
            };
          },
          processResults: function (data, params) {
            placename.parent('.field-placename').show();
            return {
              results: data.google
            };
          },
          cache: true
        },
        escapeMarkup: function (markup) { return markup; },
        minimumInputLength: 7,
        templateResult: formatGoogleAddress,
        templateSelection: formatGoogleAddressSelection
      });
  });



  /* x TODO: Select2 visual stuff */
  function formatVkCity (vk) {
    if (vk.loading) return vk.text;
    var markup = "<div class='select2-result-vkcity clearfix'>"+ vk.title + "</div>";
    return markup;
  }

  function formatVkCitySelection (vk) {
    return vk.title;
  }

  function formatGoogleAddress (google) {
    var full_address = google.text;
    if (google.loading) return google.text;
    if (google.sublocality) full_address = google.text + ' '+google.sublocality;
    var markup = "<div class='select2-result-googleaddress clearfix'>"+ full_address + "</div>";
    return markup;
  }

  function formatGoogleAddressSelection (google) {
    return google.text;
  }

  /* Hide or show certain elements depending on $geolocation_id */
  function initHide() {
    if (jQuery('#geolocation_address_selected').val() == '0') {
        jQuery('#geolocation__selected').hide();
    } else {
        jQuery('#geolocation__new').hide();
        jQuery('#geolocation_address_change').show().on('click', function() {
          jQuery(this).hide();
          jQuery('#geolocation__selected').hide();
          jQuery('#geolocation__new').show();
          placename.parent('.field-placename').show();
        });
    }
    jQuery('#geolocation_fields_loader').hide();
  }

});
</script>
