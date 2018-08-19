<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Response;
use frontend\models\Likes;

class LikesController extends Controller
{
    public $layout = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                     'actions' => ['like'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * API: add like
     * urlManager route: /api/likes/like
     * @return JSONarray
     */
    public function actionLike()
    {
        $data = Yii::$app->request->get();

        $response = Likes::addLike($data['elem_type'], $data['id']);
        $response['likeCount'] = Likes::findLikesCount($data['elem_type'], $data['id']);
        $response['myLike'] = Likes::findMyLike($data['elem_type'], $data['id']);

        /**
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         */
        $headers = Yii::$app->response->headers;
        $headers->add('X-IC-Trigger', '{"like":['.Json::encode($response).']}');

        /* If we making a request via Intercooler, we're sending back pure parameter for visual feedback */
        if (isset($data['ic-request'])) {
            return $response['likeCount'];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }
}
