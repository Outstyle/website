<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use common\models\geolocation\GeolocationCountries;
use common\models\geolocation\GeolocationCities;

/**
 * Geolocation filter form (frontend)
 * DB tables relation: 'geolocation', 'geolocation_cities', 'geolocation_countries'
 *
 * TODO: If more options are about to be added, move this to separate widget [?]
 *
 * @var $showLabels   bool
 */

if (!isset($showLabels)) {
    $showLabels = false;
}

$controllerId = Yii::$app->controller->id;

echo
  # country select
  Html::tag('div',
    Html::tag('div',
      ($showLabels ? Html::tag('label', Yii::t('app', 'Country'),
        [
          'class' => 'control-label',
          'for' => 'country'
        ]
      ) : '').
      Html::dropDownList('country', null,
        GeolocationCountries::getAllActiveCountriesDropdown(),
        [
          'id' => 'geolocation_country',
          'class' => "form-trigger select-{$controllerId}-country",
          'ic-trigger-on' => 'change'
        ]
      ),
    ['class' => "form-group field-{$controllerId}-country"]).

    # city select
    Html::tag('div',
        ($showLabels ? Html::tag('label', Yii::t('app', 'City'),
        [
          'class' => 'control-label',
          'for' => 'city'
        ]
      ) : '').
      Html::dropDownList('city', null,
        GeolocationCities::getAllActiveCitiesDropdown(),
        [
          'id' => 'geolocation_city',
          'class' => "form-trigger select-{$controllerId}-city",
          'ic-trigger-on' => 'change'
        ]
      ),
    ['class' => "form-group field-{$controllerId}-city"]),

  [
    'id' => 'geolocation__filter'
  ]);
