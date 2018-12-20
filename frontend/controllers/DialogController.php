<?php

namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

use frontend\models\Dialog;

use frontend\components\handlers\ErrorHandler;
use frontend\components\OutstyleSocialController;

class DialogController extends OutstyleSocialController
{

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
                      'allow' => true,
                      'roles' => ['@'],
                    ],
                    [
                      'allow' => false,
                      'roles' => ['*'],
                    ],
                ],
            ],
        ];
    }

    /**
     * [API] Dialogues list
     * @return string HTML
     */
    public function actionList()
    {
        $dialogs = Dialog::getByUserId(Yii::$app->user->id);

        $pagination = new Pagination([
            'defaultPageSize' => Dialog::$dialogPageSize,
            'totalCount' => $dialogs->count(),
            'page' => 0,
        ]);

        $dialogs = $dialogs
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        $dialogs = Dialog::setupData($dialogs);

        $response['triggeredBy'] = Yii::$app->request->post('ic-trigger-name');
        $response['page'] = 0;

        if (!$response['triggeredBy']) {
            $response['triggeredBy'] = 'loadmore';
            $response['page'] = 1;
        }

        $headers = Yii::$app->response->headers;
        $headers->add('X-IC-Trigger', '{"dialogsLoaded":['.Json::encode($response).']}');

        return $this->renderPartial('index', [
            'dialogs' => $dialogs
        ]);
    }
}
