<?php

namespace common\models\geolocation;

use Yii;
use yii\helpers\ArrayHelper;
use common\components\helpers\BackupHelper;
use common\components\helpers\CURLHelper;

/**
 * This is the model class for table "{{%geolocation_countries}}".
 *
 * @property integer $id
 * @property string $iso_code
 * @property string $name_ru
 */
class GeolocationCountries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%geolocation_countries}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iso_code', 'name_ru'], 'required'],
            [['iso_code'], 'string', 'max' => 3],
            [['name_ru'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'iso_code' => Yii::t('app', 'Iso Code'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * Gets global countries list from VK socail network (JSON format)
     * @param  integer  $lang   What language to use in response? See languages list and details here: https://habrahabr.ru/post/204840/
     * @return array            VK API response (see docs for more: https://vk.com/dev/database.getCountries)
     */
    public static function getVkCountries($lang = 0)
    {
        $cache = Yii::$app->cache;
        $cache_time = 3600 * 24 * 30; /* 1 month */
        $cache_key = 'database.getCountries.'.$lang;

        $parsedjson = $cache->get($cache_key);
        if ($parsedjson === false) {

            # headers and init stuff
            $headerOptions = [
                'method' => "GET",
                'header' => "Accept-language: en\r\n" .
                "Cookie: remixlang=$lang\r\n"
            ];

            $url = 'https://api.vk.com/api.php?oauth=1&method=database.getCountries&v=5.5&need_all=1&count=1000';
            $json = CURLHelper::getURL($url, $headerOptions);

            # decoding JSON and saving it to our cache so not to make additional queries to VK API for next [$cache_time]
            if ($json) {
                $parsedjson = json_decode($json, true);
            }

            if (isset($parsedjson['response'])) {
                $parsedjson = $parsedjson['response']['items'];
                $cache->set($cache_key, $parsedjson, $cache_time);

                # making a file-backup just in case if VK API services won't be available anymore
                BackupHelper::saveJSON($cache_key, $parsedjson);
            } else {
                # if VK failed to send valid response to us...
                Yii::error('VK provider has no items key in response!', 'thirdparty');

                $parsedjson = BackupHelper::getJSON($cache_key);
                $cache->set($cache_key, $parsedjson, $cache_time);
            }
        }

        return $parsedjson;
    }
    /**
     * Behaves same as 'getVkCountries', adding 'Choose country...' as a first menu to choose from dropdown
     * @param  integer $lang See 'getVkCountries' param
     * @return array         See 'getVkCountries' return
     */
    public static function getVkCountriesDropdown($lang = 0)
    {
        $countries = self::getVkCountries($lang);
        $placeholder = ['id' => 0, 'title' => Yii::t('app', 'Choose country...')];

        if ($countries) {
            array_unshift($countries, $placeholder);
            return $countries;
        }

        return $placeholder;
    }
    /**
     * Gets single country from VK social networks by it's ISO code
     * @param  string             $iso_code           ISO 3166-1 alpha-2 country name (i.e. UA, RU, CA)
     * @param  boolean            $returnISOcode      if true, returns ISO code, if false - returns country id from DB
     * @return string|int|false                       VK country ID, if $returnISOcode set to false
     */
    public static function getVkCountry($iso_code = '', $returnISOcode = false)
    {

        #first, let's check if we already have this country code stored in our DB
        $country_exists = self::find()->where(['iso_code' => $iso_code])->one();

        if ($country_exists === null) {

            # headers and init stuff
            $headerOptions = [
                'method' => "GET",
                'header' => "Accept-language: en\r\n" .
                "Cookie: remixlang=0\r\n"
            ];

            $url = 'https://api.vk.com/api.php?oauth=1&method=database.getCountries&v=5.5&code='.$iso_code;
            $json = CURLHelper::getURL($url, $headerOptions);

            # decoding JSON and saving it to our cache so not to make additional queries to VK API for next [$cache_time]
            if ($json) {
                $parsedjson = json_decode($json, true);
            }

            if (isset($parsedjson['response']) && !empty($parsedjson['response']['items'][0]['title'])) {

                # write to our DB to prevent thirdparty API interaction in future
                $geolocation_country = new GeolocationCountries;
                $geolocation_country->iso_code = $iso_code;
                $geolocation_country->name_ru = $parsedjson['response']['items'][0]['title'];
                $geolocation_country->vk_country_id = $parsedjson['response']['items'][0]['id'];
                $geolocation_country->save();

                return ($returnISOcode ? $iso_code : $parsedjson['response']['items'][0]['id']);
            }
        } else {
            return ($returnISOcode ? $country_exists->iso_code : $country_exists->vk_country_id);
        }

        return false;
    }
    /**
     * Get all active countries from DB
     * @return array
     */
    public static function getAllActiveCountriesArray()
    {
        return self::find()->select('vk_country_id,name_ru')->asArray()->all();
    }
    /**
     * Behaves same as 'getAllActiveCountriesArray', adding 'Choose country...' as a first menu to choose from dropdown.
     *
     * @return array
     */
    public static function getAllActiveCountriesDropdown()
    {
        $countries = ArrayHelper::map(self::getAllActiveCountriesArray(), 'vk_country_id', 'name_ru');
        if (is_array($countries)) {
            return ['' => Yii::t('app', 'Choose country...')] + $countries;
        }
    }
}
