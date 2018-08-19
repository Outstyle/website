<?php
namespace frontend\controllers;

use Yii;
//use frontend\models\User;
//use yii\web\User;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\helpers\Url;
use common\models\LoginForm;
use common\models\User;
 
use frontend\models\ConfirmEmail;

class AuthController extends Controller {

    public function actionConfirm($key)
    {
        if($model = ConfirmEmail::find()->where("confirm_key = :key",[":key" => $key])->one()){

            if( time() - $model->created < 3600*72 ){
                $modelUser = User::find()->where("email = :email",[":email" => $model->email])->one();
                $user = new LoginForm();
                $user->username = $modelUser->username;
                Yii::$app->user->login($user->getUser(), 0);
                $model->delete();
                Yii::$app->session->setFlash('cess', Yii::t('app', 'Подтверждение Вашей электронной почты прошло успешно'));

                return $this->redirect('/users/');

            }else{
                $model->confirm_key = Yii::$app->security->generateRandomString();
                Yii::$app->mailer->compose()
                        ->setFrom(Yii::$app->params['adminEmail'])
                        ->setTo($model->email)
                        ->setSubject('Outstyle - robot')
                        ->setTextBody("Для подтверждения своего email перейдите по ссылке http://outstyle.dev/auth/confirm/".$model->confirm_key.'/')
                        ->send();
                $model->save();

                Yii::$app->session->setFlash('cess', Yii::t('app', 'Ссылка подтверждения Вашего email уже не действителена. На Вашу почту была отправлена новая ссылка'));
            }
        }

        return $this->redirect('/main/');
    }
}