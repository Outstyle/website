<?php

namespace frontend\controllers;

use Yii;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\helpers\Json;

use common\models\geolocation\Geolocation;
use common\models\geolocation\GeolocationCountries;
use common\models\geolocation\GeolocationCities;

class GeolocationController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Gets certain geolocation information from targeted provider
     * Providers allowed can be specified @urlManager
     * See possible formatting for $location@google: https://developers.google.com/maps/documentation/geocoding/intro?hl=ru
     *
     * @param  string $provider   Geoinformation provider
     * @param  string $location   Geolocation
     * @return array              Provider's response
     */
    public function actionGet($provider)
    {
        $data = Yii::$app->request->get();
        $response = 'null';

        # GOOGLE API
        if ($provider == 'google') {

            # geocode
            $formatted = (isset($data['formatted']) ? 1 : 0);
            if (isset($data['address'])) {
                $response = Geolocation::getGoogleGeocode($data['address'], $formatted);
            }
        }

        # VKONTAKTE API
        if ($provider == 'vk') {

            # countries
            if (isset($data['countries'])) {
                # default response or formatted response for dropdown usage
                $response = (isset($data['formatted']) ?
                GeolocationCountries::getVkCountriesDropdown(0) :
                GeolocationCountries::getVkCountries(0));
            }

            # city
            if (isset($data['q']) && isset($data['country_id'])) {
                $response = GeolocationCities::getVkCity($data['q'], $data['country_id']);
            }
        }


        /* Default response in JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            $provider => $response,
        ];
    }

    public function actionCheck($provider)
    {
        if ($provider == 'vk') {
            $curlData = GeolocationCities::curlVkCities();

            if (isset($curlData['error'])) {
                $headers = Yii::$app->response->headers;
                $headers->add('X-IC-Trigger', '{"vkApiError":['.Json::encode($curlData).']}');
                Yii::error($curlData['error'], 'thirdparty');
                return $curlData['error'];
            }

            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"vkApiSuccess":[]}');

            return 1;
        }

        if ($provider == 'google') {
            $response = Geolocation::getGoogleGeocode('Киев, бульвар Шевченко');

            if (!$response) {
                $error = 'Empty file_get_contents() response';
                $headers = Yii::$app->response->headers;
                $headers->add('X-IC-Trigger', '{"googleApiError":['.Json::encode($error).']}');
                return $error;
            }

            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"googleApiSuccess":[]}');

            return 1;
        }
    }
}
