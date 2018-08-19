<?php
namespace frontend\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\UploadedFile;
use common\CImageHandler;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use frontend\models\User;
use frontend\models\Blacklist;
use frontend\models\UserSearch;
use frontend\models\UserPrivacy;
use frontend\models\UserDescription;

use frontend\components\ParentController;

class SettingsController extends ParentController 
{
    public $layout='main-new';

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

    public function actionIndex() 
    {
        $model = User::findOne(Yii::$app->user->id);
        $oldPass = $model->password_hash; 

        $oldEmail = $model->email;
        $position = strripos($oldEmail, '@');
        $oldEmailEnd = stristr($oldEmail, '@');
        $change = '******';
        if($position >=4){
            $oldEmailBegin = substr($oldEmail,0,4);
            $oldEmail = $oldEmailBegin.$change.$oldEmailEnd;
        }else{
            $oldEmailBegin = substr($oldEmail,0,2);
            $oldEmail = $oldEmailBegin.$change.$oldEmailEnd;
        }


        $post = Yii::$app->request->post();
        if(isset($post["User"]["oldPass"]) && $post["User"]["oldPass"] != '') {
               
            $oldPass_form = $post["User"]["oldPass"];
            $newPass = $post["User"]["newPass"];
            $newPass_repeat = $post["User"]["newPass_repeat"];

            if (Yii::$app->getSecurity()->validatePassword($oldPass_form, $oldPass)) {
                if($newPass === $newPass_repeat){
                    $model->password_hash = Yii::$app->getSecurity()->generatePasswordHash($newPass);
                }
                if($model->validate()){
                    $model->save();
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Успешно сохраненно'));
                    return $this->refresh();
                }
            }

        }elseif (isset($post["User"]["newEmail"]) && $post["User"]["newEmail"] != '') {
            
            $modelSearch = UserSearch::findOne(Yii::$app->user->id);
            $model->email = $post["User"]["newEmail"];
            $modelSearch->email = $post["User"]["newEmail"];
            if( $model->validate() && $modelSearch->validate() ){
                $model->save();
                $modelSearch->save();
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Успешно сохраненно'));
                return $this->refresh();
            }
        }

        $modelPrivacy = UserPrivacy::find()->where("`id` = :id", [':id' => Yii::$app->user->id])->one();

        if($modelPrivacy->load(Yii::$app->request->post())){
            $post = Yii::$app->request->post();
            if($modelPrivacy->validate()){
                $modelPrivacy->save();
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Успешно сохраненно'));
                return $this->refresh();
            }
        }
       
        return $this->render('index', [
            'model' => $model,
            'oldEmail' => $oldEmail,
            'modelPrivacy' => $modelPrivacy,
        ]);
    }

    public function actionBlacklist() 
    {
        $data = Yii::$app->request->get();
        $response = Blacklist::addBlacklist($data['user_id'], $data['blacklisted_id']);
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionDelblacklist()
    {
        $data = Yii::$app->request->get();
        $response = Blacklist::find()->where(array('user_id' => $data['user_id'], 'blacklisted_id' => $data['blacklist_id']));
        if ($response){
            return Blacklist::delBlacklist($data['id']);
        }
    }
}