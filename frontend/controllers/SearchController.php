<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\data\Pagination;
use yii\web\UploadedFile;
use common\CImageHandler;
use yii\web\HttpException;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use frontend\models\Friend;
use frontend\models\FriendRequests;
use frontend\models\UserDescription;

use frontend\components\ParentController;


class SearchController extends ParentController  {

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
        return $this->render('index');
    }

    public function actionList()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
      
            $where = array();

            $search = $data['search'];
            $sort = $data['sort'];

            if ($data['country'] != 0){
                $where['country'] = $data['country'];
            }

            if ($data['city'] != '0'){
                $where['city'] = $data['city'];
            }

            $age_start = ($data['age_start'] != "") ?  date('Y-m-d', time()-(int)$data['age_start']*31536000) : "";
            $age_end = ($data['age_end'] != "") ? date('Y-m-d', time()-(int)$data['age_end']*31536000) : "";
            
            if($age_start == "" && $age_end != ""){
                $age_start = date('Y-m-d', time());
            }

            $sex = "NULL";
            if($data['female'] != "false" && $data['male'] != "false"){
                $sex = "both";
            }else{
                if($data['female'] != "false"){
                    $sex = "female";
                }
                if ($data['male'] != "false"){
                    $sex = "male";
                }
            }
                    
            if ($data['culture'] != "0"){
                $where['culture'] = $data['culture'];
            }

            if ($data['online'] != "false"){
                $data['online'] = 1;
            }

            $foto = ($data['foto'] != "false") ? "1" : "";
            $page = $data['page'];

            $findUsres = self::searchUsers($sort, $where, $sex, $age_start, $age_end, $search, $foto, $page);
            
            $users = array();
            
            if($data['online'] == 1){
                $countOnline = 0;
                for($i=0; $i<count($findUsres); $i++){
                    if( (int)self::getIndicator($findUsres[$i]->user->lastvisit) == 1 ){
                        $users[$countOnline]['id'] = $findUsres[$i]->id;
                        $users[$countOnline]['name'] = $findUsres[$i]->nickname;
                        $users[$countOnline]['city'] = ($findUsres[$i]->city === null) ? "" : $findUsres[$i]->getCity()->one()->name;
                        $users[$countOnline]['onlineInd'] = (int)self::getIndicator($findUsres[$i]->user->lastvisit);
                        $users[$countOnline]['isFriend'] = Friend::checkOnFriend($findUsres[$i]->id);
                        $countOnline++;
                    }
                }
            }else{
                for($i=0; $i<count($findUsres); $i++){
                    $users[$i]['id'] = $findUsres[$i]->id;
                    $users[$i]['name'] = $findUsres[$i]->nickname;
                    $users[$i]['city'] = ($findUsres[$i]->city === null) ? "" : $findUsres[$i]->getCity()->one()->name;
                    $users[$i]['onlineInd'] = (int)self::getIndicator($findUsres[$i]->user->lastvisit);
                    $users[$i]['isFriend'] = Friend::checkOnFriend($findUsres[$i]->id);
                }
            }
            
        }
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'users' => $users,
            ];
    }

    public static function searchUsers($sort, $where, $sex, $age_start, $age_end, $search, $foto, $page = 1)
    {
        $query = UserDescription::find()
            ->with(['user'])
            ->where('
                    IF(:age_start != "" OR :age_end != "",({{%user_description}}.`birthday` < :age_start AND {{%user_description}}.`birthday` > :age_end), 1=1)
                    AND IF(:sex = "both", {{%user_description}}.`sex`="male" OR {{%user_description}}.`sex`="female", IF (:sex = "NULL", 1=1, {{%user_description}}.`sex`= :sex))
                    AND IF(:foto != "", {{%user_description}}.`avatar` != "", 1=1)'
//                    AND IF(:search != "", MATCH (name, last_name, nickname) AGAINST (:search IN BOOLEAN MODE), 1=1)'
                    ,[
                        ':age_start' => $age_start,
                        ':age_end' => $age_end,
//                        ':search' => $search,
                        ':foto' => $foto,
                        ':sex' => $sex,
                    ])
            ->andWhere(['like', "CONCAT(`name`, `last_name`, `nickname`)", $search])
            ->andWhere($where)
            ->orderBy([$sort => SORT_DESC]);

            $pagination = new Pagination([
                        'defaultPageSize' => 20,
                        'totalCount' => $query->count(),
                        'page' => $page-1,
                    ]);

            return $query->offset($pagination->offset)
                         ->limit($pagination->limit)
                         ->all();
    }
    
    public static function getIndicator($lastvisit)
    {
        if( time()-$lastvisit < 900){
            return '1';
        }else{
            return '0';
        }
    }
}