<?php
namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\UploadedFile;
use common\CImageHandler;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use frontend\models\Rating;

use frontend\components\ParentController;

class RatingController extends ParentController {

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

    public function actionAdd_event()
    {
        $data = Yii::$app->request->get();
        $event = new Rating;
        $event->event_name = $data['event_name'];
        $event->event_limit = $data['event_limit'];
        $event->limit_type = $data['limit_type'];
        $event->event_price = $data['event_price'];
        $event->active = $data['active'];
        $event->save();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $event;
    }

    public function actionEdit_event()
    {
        
        $data = Yii::$app->request->get();
        $event = Rating::find()->where(array('id' => $data['id']))->one();
        $event->event_name = $data['event_name'];
        $event->event_limit = $data['event_limit'];
        $event->limit_type = $data['limit_type'];
        $event->event_price = $data['event_price'];
        $event->active = $data['active'];
        $event->save();
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $event;
    }

    public function actionDel_event()
    {
        $data = Yii::$app->request->get();
        return Rating::find()->where(array('id' => $data['id']))->one()->delete();
    }

   public function actionEventlogrecord()
   {
        $data = Yii::$app->request->get();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return Rating::doEvent($data['event_name']);
   }
}