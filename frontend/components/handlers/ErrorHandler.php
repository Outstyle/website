<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace frontend\components\handlers;

use Yii;
use yii\helpers\Json;

/**
 * All kind of custom app errors should be handled here
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class ErrorHandler
{
    /**
     * Sets up X-IC-Trigger header to trigger certain event on error
     * @see: http://intercoolerjs.org/reference.html -> Response Headers -> X-IC-Trigger
     *
     * @param  array  $errors   Model errors (@see: https://www.yiiframework.com/doc/api/2.0/yii-base-model#$errors-detail)
     */
    public static function triggerHeaderError($errors = [])
    {
        if ($errors) {
            foreach ($errors as $k => $error) {
                $response[$k] = urlencode($error[0]);
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"'.
              Yii::$app->controller->id.
              ucfirst(Yii::$app->controller->action->id).
              'Error":['.Json::encode($response).']}');
        }
    }
}
