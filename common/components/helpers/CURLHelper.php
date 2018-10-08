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
    public static function getURL($url, $headerOptions = [])
    {

        // Choose a random proxy
        $proxies = Yii::$app->params['CURLHelper']['proxies'] ?? '';
        $useProxies = Yii::$app->params['CURLHelper']['useProxies'] ?? '';

        $ch = curl_init();  // Initialize a cURL handle

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

        $results = curl_exec($ch);
        curl_close($ch);

        return $results;
    }

    public static function postToURL($url, $postData = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        $results = curl_exec($ch);
        curl_close($ch);

        return $results;
    }
}
