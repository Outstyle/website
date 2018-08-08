<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace frontend\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\models\Photoalbum;
use frontend\components\OutstyleSocialController;

/**
 * FormsController controller: for working with generated HTML forms (mainly returned via AJAX)
 * Must be extended from CsrfController for token compare
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class FormsController extends OutstyleSocialController
{
    public $layout = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                  [
                    'allow' => true,
                    'actions' => [
                      'renderform'
                     ],
                    'roles' => ['@'],
                  ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Generates an ActiveForm to display through renderAjax method
     * We need this method to walk through, so the yii2 validation rules could be applied on a clientside via JS too
     * @param  string $type           type of form (default: create)
     * @param  string $controllerId   controller ID
     * @return HTML
     */
    public function actionRenderform($type = 'create', $controllerId = '')
    {
        /* If it's an Intercooler request, also sending headers for photoalbum form render event to fire */
        if (Yii::$app->request->post('ic-request')) {
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"'.$controllerId.'FormRendered":[]}');

            if ($controllerId == 'photoalbum') {
                $model = new Photoalbum();
            }

            return $this->renderAjax('../'.$controllerId.'/_form', [
              'model' => $model ?? '',
              'form_type' => $type
            ]);
        }
    }
}
