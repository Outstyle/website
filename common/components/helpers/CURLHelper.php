<?php

namespace common\components\helpers;

use Yii;

/**
 * CURLHelper provides a set of static methods for working with everything that is related to 'parsing' processes and getting the info from internal sources.
 *
 * @see: proxylist -> @common/params
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @since 1.0
 */
class CURLHelper
{
    public static function getURL($url = '', $headerOptions = []) : array
    {
        $results = [];

        $proxies = Yii::$app->params['CURLHelper']['proxies'] ?? '';
        $useProxies = Yii::$app->params['CURLHelper']['useProxies'] ?? '';

        $ch = curl_init();

        // Choose a random proxy
        if (isset($proxies) && $useProxies === true) {
            $proxy = $proxies[array_rand($proxies)];
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        // Set any other cURL options that are required
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerOptions);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, Yii::$app->params['CURLHelper']['timeout'] ?? 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        $results['content'] = curl_exec($ch) !== false ? curl_exec($ch) : '';

        // Error tracking
        if (curl_errno($ch)) {
            $results['error'] = curl_error($ch);
            $results['proxy'] = (isset($proxy)) ? $proxy : $_SERVER['SERVER_ADDR'];
        }

        curl_close($ch);

        return $results;
    }
}
