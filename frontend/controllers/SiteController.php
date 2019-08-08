<?php
namespace frontend\controllers;

use common\models\User;
use Yii;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

use common\models\AuthMisstep;
use common\models\LoginForm;

use app\models\ConfirmEmail;
use app\models\UserDescription;

use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\ResetPasswordForm;
use frontend\models\PasswordResetRequestForm;

use frontend\components\ParentController;

class SiteController extends ParentController
{
    public $layout = 'portal';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['signup', 'login', 'error'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'error'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    /**
     * API: Performs user login, getting data from view
     * @see     @views/_forms/headerLoginForm.php
     *
     * @return  array|JSON
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        $data = Yii::$app->request->post();

        $model->email = $data['email'] ?? '';
        $model->password = $data['password'] ?? '';

           /* Check for attempts */
            $misstep = AuthMisstep::find()->where("ip = :ip", [':ip' => Yii::$app->request->userIP])->one();
        if ($misstep) {
            if ($misstep->created != 0 && time() - $misstep->created < 86400) {
                $time = ($misstep->created + 86400 - time());
                $endTime = date("H:i", mktime(0, 0, $time));
                $model->addError('misstep', Yii::t('app', 'You will be able to sign up again after').' '.$endTime);
            } else {
                $misstep->attempt += 1;

                    /* If we step over limit - block user from further logins and throw error */
                    if ($misstep->attempt == 10) {
                        $misstep->created = time();
                        $model->addError('misstep', Yii::t('app', 'You have entered your password wrong 10 times. This action is blocked for you on 24 hours!'));
                    }
                $misstep->save();

                    /* If the user has tried again after block, we check for errors and delete + check for login validation again */
                    if ($misstep->attempt > 10 && !$model->errors) {
                        $misstep->delete();
                        $model->login();
                    }
            }
        } else {
            $misstep = new AuthMisstep();
            $misstep->ip = Yii::$app->request->userIP;
            $misstep->created = 0;
            $misstep->attempt = 1;
            $misstep->save();
        }

        /* We will try to login only if attempts are not overlimited */
        if ($misstep->attempt < 10) {
            /* If data is valid, and we don't have any errors - proceed to login */
            if ($model->validate() && !$model->errors) {
                if ($model->login()) {
                    /* If we logged in, we need to delete previous attempts, if there are any + redirect user to his profile page */
                    if ($misstep) {
                        $misstep->delete();
                    }
                    return $this->redirect(['/']); // Must be /myprofile on live (with social)
                }
            } else {
                $response = ['error' => $model->errors];
            }
        } else {
            $response = ['error' => $model->errors];
        }

        /**
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         */

        if (isset($response)) {
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"form-login":['.json_encode($response).']}');
        }

        /* If we making a request via Intercooler, we're not sending anything to the element, that have made a request (button) to prevent content rewrite */
        if (isset($data['ic-request'])) {
            return;
        }
    }

    /**
     * Logout action
     * @return redirect
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * API: Signs up a user to the website
     * @return JSON
     */
    public function actionSignup()
    {

        // Init
        $model = new SignupForm();
        $data = Yii::$app->request->post();
        $formId = 'form-register';
        $response = [];

        // Returning everything as JSON, since it's a part of API
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Ajax validation
        if (Yii::$app->request->isAjax && $model->load($data)) {
            if (isset($data['ajax']) && ($data['ajax'] == $formId)) {
                return ActiveForm::validate($model);
            }
        }

        // If our signup form has passed the validation...
        if ($model->load($data) && $model->validate()) {

            // If signup form returned user ID, sending confirmation email
            // ->setTextBody("Для подтверждения своего email перейдите по ссылке https://".$_SERVER['SERVER_NAME']."/auth/confirm/".$confirmEmail->confirm_key.'/')
            if ($user = $model->signup()) {
                $confirmEmail = new ConfirmEmail();
                $confirmEmail->confirm_key = Yii::$app->security->generateRandomString();
                $confirmEmail->email = $model->email;
                $confirmEmail->created = time();
                Yii::$app->mailer->compose()
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setTo($model->email)
                    ->setSubject('Outstyle - robot')
                    ->setTextBody("Спасибо за регистрацию! В данный момент сайт находится в бета-режиме, поэтому не исключено, что в некоторых местах он будет недоступен.")
                    ->send();

                if ($confirmEmail->validate()) {
                    $confirmEmail->save();

                    $response = ['error' => false, 'message' => Yii::t('app', 'На ваш ящик отправлено письмо')];
                }

                // Logging user in
                Yii::$app->getUser()->login($user);
                return $this->redirect(['/']);
            } else {
                $response = ['error' => true, 'message' => Yii::t('app', 'Signup process has failed!')];
            }
        } else {
            $response = ['error' => true, 'message' => Yii::t('app', 'No data to use for signup')];
        }

        return $response;
    }

    /**
     * Shows a 404 error
     * @return array
     */
    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        $this->layout = '404';
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }


    /* TODO: WHATS WITH THIS STUFF? */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Благодарим Вас за обращение к нам. Мы ответим вам как можно скорее.'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Был ошибка при отправке по электронной почте.'));
            }
            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionPasswordrestore()
    {
        $data = Yii::$app->request->post();
        $model = new PasswordResetRequestForm();

        if (Yii::$app->request->isAjax && $model->load($data)) {
            if (isset($data['ajax']) && ($data['ajax'] == 'form-passwordrestore')) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        if ($model->load($data) && $model->validate()) {
            if ($model->sendEmail()) {
                return [
                        'error' => false,
                        'message' => Yii::t('app', 'Проверьте вашу электронную почту для получения дальнейших инструкций'),
                    ];
            } else {
                $message = '';
                foreach ($model->getErrors() as $key => $value) {
                    $message .= ' ' . $key.': '.$value[0];
                }
                return [
                        'error' => true,
                        'message' => Yii::t('app', 'Обнаружены ошибки: ') . $message
                    ];
            }
        }
        return $this->redirect(['/']);
    }

    public function actionRequestpasswordreset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Проверьте вашу электронную почту для получения дальнейших инструкций'));
                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'К сожалению, мы не в состоянии сбросить пароль.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetpassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Новый пароль был сохранен.'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }


}
