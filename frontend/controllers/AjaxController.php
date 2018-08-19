<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use frontend\models\User;
use frontend\models\UserSearch;
use frontend\models\UserPrivacy;
use frontend\models\UserDescription;

use frontend\components\ParentController;

class AjaxController extends ParentController {


    public function actionIndex()
    {
        $model = null;

        return $this->render('index', [
            'model' => $model,
        ]);
    }


    public function actionUserautocomplete()
    {
        $search = urldecode($_GET['term']);
        $model = UserDescription::find()->select(['name'])->where(" `name` LIKE  :search ", [':search' => $search.'%'])
            ->limit(15)
            ->all();
        $r = [];
        foreach($model AS $v){
            $r[] = $v->name;
        }
        $model2 = User::find()->select(['username'])->where(" `username` LIKE  :search ", [':search' => $search.'%'])
            ->limit(15)
            ->all();
        foreach($model2 AS $v){
            $r[] = $v->username;
        }
        $r = array_unique($r);
        echo Json::encode($r);
    }

    public function actionPrivacysource(){
        echo Json::encode(UserPrivacy::setNames());
    }

    public function actionPrivacy(){
        $model = UserPrivacy::find()->where("`id` = :id", [':id' => Yii::$app->user->id])->one();
        if(!$model){
            return new BadRequestHttpException(Yii::t('app', 'Not found user'), 404);
        }
        if(!isset($model->{$_POST['name']})){
            return new BadRequestHttpException(Yii::t('app', 'Not found data'), 404);
        }
        $data = UserPrivacy::setNames();
        $data = array_flip($data);
        if(!isset($data[$_POST['value']])){
            return new BadRequestHttpException(Yii::t('app', 'Not found data value'), 404);
        }
        $model->{$_POST['name']} = (int)$data[$_POST['value']];
        $model->save();
        echo 'OK';
    }
}