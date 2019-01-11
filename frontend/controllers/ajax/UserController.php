<?php

namespace frontend\controllers\ajax;

use app\models\UserPrivacy;
use yii\helpers\Json;
use yii\web\Controller;
use frontend\components\ParentController;

class UserController extends ParentController {

    public function actionPrivacy(){

        var_dump($_POST);
        var_dump($_GET);
    }

    public function actionPrivacysource(){
        echo Json::encode(UserPrivacy::setNames());
    }
}