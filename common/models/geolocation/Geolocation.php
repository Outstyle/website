<?php

namespace common\models\geolocation;

use Yii;
use common\models\School;

/**
 * This is the model class for table "{{%geolocation}}".
 *
 * @property int $id
 * @property string $lat
 * @property string $lng
 * @property string $country
 * @property string $city
 * @property string $address
 * @property string $formatted_address
 * @property string $name
 */
class Geolocation extends \yii\db\ActiveRecord
{
    const URL_GOOGLE_GEODATA_JSON_API = 'https://maps.googleapis.com/maps/api/geocode/json';

    public static function tableName()
    {
        return '{{%geolocation}}';
    }

    public function rules()
    {
        return [
            [['lat', 'lng', 'country', 'city', 'address', 'formatted_address', 'google_place_id'], 'required'],
            [['lat', 'lng'], 'string', 'max' => 32],
            [['country', 'city'], 'string', 'max' => 128],
            [['address', 'formatted_address', 'name'], 'string', 'max' => 256],
            [['google_place_id'], 'string', 'max' => 1024],
            [['google_place_id'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'lat' => Yii::t('app', 'Latitude'),
            'lng' => Yii::t('app', 'Longitude'),
            'country' => Yii::t('app', 'Country'),
            'city' => Yii::t('app', 'City'),
            'address' => Yii::t('app', 'Address'),
            'formatted_address' => Yii::t('app', 'Formatted Address'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * Gets Google Geocode by location name
     * Docs: https://developers.google.com/maps/documentation/geocoding/intro?hl=ru
     * Function taken: http://stackoverflow.com/questions/14586105/how-to-determine-if-address-doesnt-exists-in-google-maps-api-v3
     * Select2 formatted data issue: http://stackoverflow.com/questions/26074414/unable-to-select-item-in-select2-drop-down.
     *
     * @param string $location  Formatted geolocation name (see docs for more info)
     * @param string $formatted If set to true, returns data formatted for Select2 (see Select2 formatted data issue)
     *
     * @return array Google API response (see docs for more info)
     */
    public static function getGoogleGeocode($location = '', $formatted = false)
    {
        if ($location) {
            $response = '';

            $googleData = self::getContentsFromGoogleMapsApi($location);
            if ($googleData === false) {
                return;
            }

            $parsedjson = json_decode($googleData, true);
            if (key_exists(0, $parsedjson['results'])) {

                /* check if we already have such geolocation in our DB */
                $checkGeolocation = self::checkExistingGeolocation(
                  $parsedjson['results'][0]['geometry']['location']['lat'],
                  $parsedjson['results'][0]['geometry']['location']['lng']
                );

                /* if we need our data to be accepted by Select2, we need to properly format it */
                if ($formatted) {
                    $response = array();
                    foreach ($parsedjson['results'] as $address) {
                        $response[] = [
                          'id' => $address['place_id'],
                          'text' => $address['formatted_address'],
                          'sublocality' => self::checkExistingEntity($address['address_components'], 'sublocality'),
                        ];
                    }
                } else {
                    $response = $parsedjson['results'];
                }

                return $response;
            }
        }
    }

    /**
     * Saves Google Geocode data into DB.
     *
     * @param string $place_id Exact 'place_id' of the Google Geocode place
     *
     * @return int Geolocation ID
     */
    public static function setGoogleGeocode($place_id = '')
    {
        if ($place_id) {
            $googleData = self::getContentsFromGoogleMapsApi('', $place_id);
            if ($googleData === false) {
                return;
            }

            $parsedjson = json_decode($googleData, true);
            if (key_exists(0, $parsedjson['results'])) {

                /* Check if we already have such geolocation in our DB, using google's unique identifier */
                $checkGeolocation = self::checkExistingGeolocationById($parsedjson['results'][0]['place_id']);

                /* If not, storing geolocation stuff into our DB (this will be needed for further checks from users side) */
                if (!$checkGeolocation) {
                    return self::addGeolocation($parsedjson['results'][0]);
                }

                return $checkGeolocation;
            }
        }
    }

    /**
     * Gets Geodat afrom Google Maps API either by $location (can be hazy) or by $place_id (must be exact Google place ID)
     * @param  string $location     Address to queue Google Maps API for
     * @param  string $place_id     Exact place ID to get info about that point on map
     * @return string|false
     */
    public static function getContentsFromGoogleMapsApi($location = '', $place_id = '')
    {
        $headerOptions = array(
            'http' => array(
                'method' => 'GET',
                'header' => "Accept-language: ru\r\n",
                'timeout' => 5,
            ),
        );

        if ($location) {
            $params = '&address='.urlencode($location);
        }

        if ($place_id) {
            $params = '&place_id='.$place_id;
        }

        $url = self::URL_GOOGLE_GEODATA_JSON_API
            .'?key='.Yii::$app->params['googleMapsApiKey']
            .$params;

        $streamContext = stream_context_create($headerOptions);
        $data = file_get_contents($url, false, $streamContext);
        return $data;
    }

    /**
     * Performs a check in DB in geolocation table for existing place, using latitude and longitude.
     *
     * @param string $lat Latitude
     * @param string $lng Longitude
     *
     * @return int 0 if not found, $geolocation->id if found
     */
    public static function checkExistingGeolocation($lat = '', $lng = '')
    {
        if (!$lat || !$lng) {
            return;
        }

        $args = array(
            'lat' => (string) $lat,
            'lng' => (string) $lng,
        );

        $geolocation = self::find()->where($args)->select('id')->one();
        if ($geolocation) {
            return $geolocation->id;
        }

        return 0;
    }

    /**
     * Performs a check in DB in geolocation table for existing place, using google's 'place_id'.
     * Refer to: https://developers.google.com/maps/documentation/javascript/examples/places-placeid-geocoder.
     *
     * @param string $placeId
     *
     * @return int 0 if not found, $geolocation->id if found
     */
    public static function checkExistingGeolocationById($placeId = '')
    {
        if (!$placeId) {
            return;
        }

        $args = array(
            'google_place_id' => (string) $placeId,
        );

        $geolocation = self::find()->where($args)->select('id')->one();
        if ($geolocation) {
            return $geolocation->id;
        }

        return 0;
    }

    /**
     * Checks VK for entity name from Google Geocode data.
     * If the entity exists in VK list, writing it to our DB.
     * If entity already exists in our DB, fetching it from our 'geolocation_[entity]' table
     * See example of $address_components:  https://maps.googleapis.com/maps/api/geocode/json?address=ADDRESS_FORMAT.
     *
     * @param array $address_components Array with data from Google Geocode received data ()
     *
     * @return string|true|false Country name if found, true if found and added to DB, false if nonexisting country
     */
    public static function checkExistingEntity($address_components, $entity)
    {
        foreach ($address_components as $type) {
            foreach ($type as $k => $political_type) {
                /* work only with 'types' array (since it's always represented as array) */
                if (is_array($political_type)) {
                    /* getting a country to work with (2 letters ISO code) */
                    if ($political_type[0] == 'country') {
                        $country_short_name = $type['short_name'];
                    }

                    /* getting city name to work with (full CYR name of city) */
                    if ($political_type[0] == 'locality') {
                        $city_long_name = $type['long_name'];
                    }

                    /* getting city sublocality (районы, к примеру) to work with (full CYR name of sublocal zone) */
                    if (in_array('sublocality', $political_type)) {
                        $sublocality = $type['long_name'];
                    }

                    /* getting route and street address if existing */
                    if ($political_type[0] == 'route') {
                        $route = $type['long_name'];
                    }
                    if ($political_type[0] == 'street_number') {
                        $street_number = $type['long_name'];
                    }
                }
            }
        }

        foreach ($address_components as $type) {
            foreach ($type as $k => $political_type) {
                /*
                 * Using VK as a country/city provider, checking for existing entity from there
                 * TODO: [NOTICE] CHECK ON MULTIPLE 'types' LIKE [sublocality] OR [political]
                 */
                if (is_array($political_type) && in_array($entity, $political_type)) {
                    switch ($entity) {
                      case 'country':
                        return GeolocationCountries::getVkCountry($country_short_name, true);

                      /*
                       * TODO: [NOTICE] COULD BE TROUBLES CAUSE OF RETURNING LANGUAGE!
                       * i.e. we're sending ukrainian $city_long_name to VK, and it will not be able to match it (possibility low)
                       */
                      case 'locality':
                        if ($country_short_name && $city_long_name) {
                            $vk_country_id = GeolocationCountries::getVkCountry($country_short_name);
                            $city = GeolocationCities::getVkCity($city_long_name, $vk_country_id);

                            return $city[0]['title'];
                        }

                      case 'sublocality':
                        if ($sublocality) {
                            return $sublocality;
                        }

                      case 'route':
                        $address = $route;
                        if ($route && !empty($street_number)) {
                            $address = $route.', '.$street_number;
                        }

                        return $address;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Stores geolocation to DB
     * For now, it's only one geolocation provider (Google)
     * If you intend to add more, consider to use additional parameter '$provider'.
     *
     * @param array $data Unique data received from provider (Googleapi atm)
     */
    public static function addGeolocation($data)
    {
        if (isset($data['geometry']['location']['lat']) && isset($data['geometry']['location']['lng'])) {
            $lat = (string) $data['geometry']['location']['lat'];
            $lng = (string) $data['geometry']['location']['lng'];
            $country = self::checkExistingEntity($data['address_components'], 'country');
            $city = self::checkExistingEntity($data['address_components'], 'locality');
            $address = self::checkExistingEntity($data['address_components'], 'route');
            $formatted_address = $data['formatted_address'];
            $place_id = $data['place_id'];
            $name = ''; /* Initially empty, cause we can't know what entity it will be tied to */

            /* writing geolocation to DB */
            $geolocation = new self();
            $geolocation->lat = $lat;
            $geolocation->lng = $lng;
            $geolocation->country = $country;
            $geolocation->city = $city;
            $geolocation->address = $address;
            $geolocation->formatted_address = $formatted_address;
            $geolocation->google_place_id = $place_id;
            $geolocation->name = $name;
            $geolocation->save();

            return $geolocation->id;
        }
    }

    /**
     * Behaves same as 'getGeodata', adding 'Choose country...' as a first menu to choose from dropdown.
     *
     * @param string $model Existing model name, that is using geodata. I.e.: 'school'
     * @param array  $where WHERE clause for SQL query for $model
     *
     * @return array See 'getSchoolGeodata' return
     */
    public static function getGeodataDropdown($model = '', $where = [])
    {
        $countries = self::getGeodata($model, $where);
        if (is_array($countries)) {
            array_unshift($countries['countries'], ['id' => 0, 'text' => Yii::t('app', 'Choose country...')]);

            foreach ($countries['cities'] as $k => $city) {
                array_unshift($countries['cities'][$k], ['id' => 0, 'text' => Yii::t('app', 'Choose city...')]);
            }
        }

        return $countries;
    }

    /**
     * Gets geodata for specified model
     * Caches data for 1 day as a JSON array.
     *
     * @param string $model Existing model name, that is using geodata. I.e.: 'school'
     * @param array  $where WHERE clause for SQL query for $model
     *
     * @return array
     */
    public static function getGeodata($model = '', $where = [])
    {
        $cache = Yii::$app->cache;
        $cache_time = 3600 * 24; /* 1 day */
        $cache_key = $model.'.geodata';

        $response = $cache->get($cache_key);
        if ($response === false) {
            /* Init vars here */
            $geodata = $geolocation = [];

            /* Getting all the countries, that is related to our specified model */
            $geolocation = self::find()->with([
              $model => function (\yii\db\ActiveQuery $query) use ($where) {
                  $query->andWhere($where)->select('id, geolocation_id');
              }
            ])->select('id,country,city')->all();

            for ($i = 0; $i < count($geolocation); ++$i) {
                $countryISO = $geolocation[$i]->country;

                /* GEODATA: countries array */
                $geodata['countries'][$countryISO] = $geolocation[$i]->country;

                /* GEODATA: cities array */
                $geolocation_id = $geolocation[$i]->id;
                $geodata['cities'][$countryISO][$geolocation[$i]->city][] = $geolocation_id;

                /* GEODATA: $model array */
                foreach ($geolocation[$i]->$model as $m) {
                    $geodata[$model][$countryISO][$geolocation[$i]->city][] = $m->id;
                }
            }

            /*
             * Now we need to receive actual country name from 'geolocation_countries' DB and to list all actual cities where schools are existing for certain country
             * Format for Select2 dropdowns must be represented as 'id' and 'text' keys
             */
            foreach ($geodata['countries'] as $iso_code) {
                $country = GeolocationCountries::find()->where(['iso_code' => $iso_code])->one();

                /* if the country ISO code exists in our DB */
                if ($country) {

                    /* sending list of available countries */
                    $response['countries'][] = [
                      'id' => $country->vk_country_id,
                      'text' => $country->name_ru,
                    ];

                    /* sending a list of available cities for each countries */
                    foreach ($geodata['cities'][$iso_code] as $city => $geolocation_id) {
                        $city = GeolocationCities::find()->where(['name' => $city])->select('id,name')->one();

                        /* only show cities if there are any schools around */
                        if (isset($geodata[$model][$iso_code][$city->name])) {
                            $response['cities'][$country->vk_country_id][] = [
                              'id' => $city->id,
                              'text' => $city->name,
                              'objects' => $geodata[$model][$iso_code][$city->name],
                            ];
                        }
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Validates incoming user data and proceeds further if everything is valid.
     *
     * @param $data     User's submitted data - Yii::$app->request->post()
     *
     * @return int Geolocation ID
     */
    public static function validateData($data)
    {
        $controllerId = ucfirst(Yii::$app->controller->id);
        if (isset($data[$controllerId]['address']) && isset($data[$controllerId]['city']) && isset($data[$controllerId]['country'])) {

            /* Working with Google Provider to get geolocation ID for our DB */
            $geolocation_id = self::setGoogleGeocode($data[$controllerId]['address']);

            return $geolocation_id;
        }
    }

    /**
     * Sets geolocation name for human-readability and understanding.
     *
     * @param int    $id   Geolocation ID
     * @param string $name Geolocation name
     *
     * return array   Geolocation obj after save
     */
    public static function setGeolocationName($id, $name)
    {
        if (is_int($id)) {
            $params = [
              ':id' => $id,
              ':name' => $name,
            ];

            return Yii::$app->db->createCommand('UPDATE {{%geolocation}} SET name=:name WHERE id=:id')->bindValues($params)->execute();
        }

        return false;
    }

    /* Relations */
    public function getEvents()
    {
        return $this->hasOne(Events::className(), ['geolocation_id' => 'id']);
    }

    public function getSchool()
    {
        return $this->hasMany(School::className(), ['geolocation_id' => 'id']);
    }
}
