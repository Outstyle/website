<?php

namespace common\models\geolocation;

use Yii;
use common\components\helpers\CURLHelper;

/**
 * This is the model class for table "{{%geolocation_cities}}".
 * Migration file: m170108_163037_create_geolocation_cities_table.php
 *
 * @property integer $id
 * @property string  $name
 * @property string  $area
 * @property string  $region
 * @property integer $vk_city_id
 * @property integer $vk_country_id
 */
class GeolocationCities extends \yii\db\ActiveRecord
{
    const URL_VK_API_CITIES = 'https://api.vk.com/api.php?oauth=1&method=database.getCities&v=5.7';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%geolocation_cities}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'vk_city_id', 'vk_country_id'], 'required'],
            [['vk_city_id', 'vk_country_id'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['vk_city_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'vk_city_id' => Yii::t('app', 'Vk City ID'),
        ];
    }


    /**
     * Gets an array of possible cities (fuzzy search) from VK social networks by queue string
     * @param  string  $q                Possible queue string for city (i.e. 'Киев')
     * @param  int     $vk_country_id
     * @return array                     Returns empty array if wasn't been able to find anything
     */
    public static function getVkCity($q, $vk_country_id)
    {
        # validation
        if (!is_numeric($vk_country_id)) {
            return;
        }

        # init
        $response = null;

        # first, let's check if we already have this city (or cities) in our DB & corresponding country
        $city_exists = self::find()->where(['like', 'name', $q])->all();

        if (empty($city_exists)) {
            $curlData = self::curlVkCities($q, $vk_country_id);

            # decoding JSON and saving it to our cache so not to make additional queries to VK API for next [$cache_time]
            if ($curlData['content']) {
                $parsedjson = json_decode($curlData['content'], true);
            }

            if (isset($parsedjson['response']) && !empty($parsedjson['response']['items'][0]['title'])) {
                /**
                 * Write to our DB to prevent thirdparty API interaction in future
                 * If you need to write everything VK returns to our DB, remove 'break' at the end
                 */
                foreach ($parsedjson['response']['items'] as $city) {
                    $geolocation_country = new GeolocationCities;
                    $geolocation_country->vk_city_id = $city['id'];
                    $geolocation_country->vk_country_id = $vk_country_id;
                    $geolocation_country->name = $city['title'];
                    $geolocation_country->area = (isset($city['area'])) ? $city['area'] : '';
                    $geolocation_country->region = (isset($city['region'])) ? $city['region'] : '';
                    $isAdded = $geolocation_country->save();
                    break;
                }

                $response[] = [
                  'id' => $parsedjson['response']['items'][0]['id'],
                  'title' => $parsedjson['response']['items'][0]['title'],
                  'added' => $isAdded
                ];

                return $response;
            }
        } else {
            # forming an array if multiple entities found & the country exists in our database
            foreach ($city_exists as $city) {
                if ($city['vk_country_id'] == $vk_country_id) {
                    $response[] = [
                      'id' => $city['vk_city_id'],
                      'title' => $city['name']
                    ];
                }
            }
            return $response;
        }

        return false;
    }

    /**
     * CURLs VK api for cities, using query param and country ID
     * @param  string  $q               Search query (city name)
     * @param  integer $vk_country_id   Country ID (1 for Russia, 2 for Ukraine and so on)
     * @return array
     */
    public static function curlVkCities($q = 'Киев', $vk_country_id = 2) : array
    {
        $headerOptions = [
            'method' => "GET",
            'header' => "Accept-language: en\r\n" .
            "Cookie: remixlang=0\r\n"
        ];

        $url = self::URL_VK_API_CITIES
            .'&q='.$q
            .'&country_id='.$vk_country_id
            .'&access_token='.Yii::$app->params['vkServiceAccessToken'];

        return CURLHelper::getURL($url, $headerOptions);
    }
}
