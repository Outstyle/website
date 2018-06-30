<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace frontend\components;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;

use common\components\helpers\ElementsHelper;

/**
 * Csrf controller: for working with _csrf tokens
 * Here we are comparing our tokens from IC requests with $_POST ones
 * Since we don't want direct access to content, we should perform token check every time we access the controller
 *
 * If we want to allow direct access to certain 'entry points', we should rely on paths and generate fresh token
 * @see: $allowedEntryPoints
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class CsrfController extends Controller
{
    /**
     * Routes that allow direct access without csrf token
     * @var array
     */
    private $_allowedEntryPoints = [
      'index',
      'view'
    ];

    /**
     * @inheritdoc
     */
    public function beforeAction($event)
    {
        $csrf_token = Yii::$app->request->headers->get('x-csrf-token');
        $user_token = ElementsHelper::getCSRFToken();

        if ($event->controller->id == Yii::$app->controller->id && in_array($event->id, $this->_allowedEntryPoints)) {
            if (!$csrf_token) {
                $csrf_token = Yii::$app->request->csrfToken;
            }
        }

        if (!$user_token) {
            throw new HttpException(400, Yii::t('err', 'Token empty!'));
        }
        if ($user_token != $csrf_token) {
            throw new HttpException(400, Yii::t('err', 'Token is invalid!'));
        }

        return parent::beforeAction($event);
    }
}
