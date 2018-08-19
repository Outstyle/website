<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\data\Pagination;
use yii\web\UploadedFile;
use yii\web\HttpException;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use frontend\models\News;
use frontend\models\Likes;
use frontend\models\Photo;
use frontend\models\Board;
use frontend\models\Video;
use frontend\models\Friend;
use frontend\models\Comments;
use frontend\models\Newsfeed;
use frontend\models\Photoalbum;
use frontend\models\Attachments;
use frontend\models\NewsCategory;
use frontend\models\AuthAssignment;
use frontend\models\UserDescription;

use frontend\components\ParentController;

class NewsfeedController extends ParentController {

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
        $where = array();
        $page = 1;
        $modelsGeneral = self::getGeneralNews($page, $where = []);
        
        $countGen = count($modelsGeneral);
        $generalNews = array();

        for($i=0; $i<$countGen; $i++){
            $generalNews[$i]['id'] = $modelsGeneral[$i]->id;
            $generalNews[$i]['idUser'] = $modelsGeneral[$i]->user;
            $generalNews[$i]['userName'] = UserDescription::findOne(['id' => $modelsGeneral[$i]->user])->nickname;
            $generalNews[$i]['created'] = NewsfeedController::getTimeRecord(strtotime($modelsGeneral[$i]->created));
            $generalNews[$i]['name'] = $modelsGeneral[$i]->name;
            $generalNews[$i]['small'] = $modelsGeneral[$i]->small;
            $generalNews[$i]['text'] = $modelsGeneral[$i]->text;
            $generalNews[$i]['url'] = ($modelsGeneral[$i]->article == 0) ? 'news/'.$modelsGeneral[$i]->url : 'article/'.$modelsGeneral[$i]->url;
            $generalNews[$i]['img'] = $modelsGeneral[$i]->img;
                $likes = ($modelsGeneral[$i]->article == 0) ? Likes::countLikes('news', $modelsGeneral[$i]->id) : Likes::countLikes('article', $modelsGeneral[$i]->id);
            $generalNews[$i]['myLike'] = Likes::myLike('news', $modelsGeneral[$i]->id);
                $comments = ($modelsGeneral[$i]->article == 0) ? $modelsGeneral[$i]->getNewsComments()->count() : $modelsGeneral[$i]->getArticleComments()->count();
            $generalNews[$i]['countLikes'] = ($likes) ? $likes : 0;
            $generalNews[$i]['countComments'] = (!empty($comments)) ? $comments : 0;
        }
/////////////новости друзей (пока по стенам)///////
        $idFriends = self::getFriendsId();
        $modelsNewsFriends = self::getFriendsNews($page, $where, $idFriends);

        foreach ($modelsNewsFriends as &$post) {
            $post = self::makePost($post);
        }

        return $this->render('index', [
            'generalNews' => $generalNews,
            'modelsNewsFriends' => $modelsNewsFriends,
        ]);
    }

    public function actionShowfriendsnews()
    {
        $data = Yii::$app->request->get();

        $page = $data['page'];
        $where = array();
        $idFriends = self::getFriendsId();

        $loadMore = true;
        $check = self::getFriendsNews($page+1, $where = [], $idFriends);
        if(empty($check)){
            $loadMore = false;
        }

        $modelsNewsFriends = self::getFriendsNews($page, $where, $idFriends);

        foreach ($modelsNewsFriends as &$post) {
            $post = self::makePost($post);
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            "modelsNewsFriends" => $modelsNewsFriends,
            "loadMore" => $loadMore,
            ];
    }

    public static function makePost($post, $isRepost = false) 
    {
        $comentPost = $post->comments()->all();
        $commentsCount = $post->comments()->count();
        $comments = array();

        for($i=0; $i<$commentsCount; $i++){
            $comments[$i] = array(
                'id' => $comentPost[$i]->id,
                'elem_type' => $comentPost[$i]->elem_type,
                'elem_id' => $comentPost[$i]->elem_id,
                'user_id' => $comentPost[$i]->user_id,
                'user_name' => UserDescription::getNickname($comentPost[$i]->user_id),
                'created' => NewsfeedController::getTimeRecord(strtotime($comentPost[$i]->created)),
                'comment' => $comentPost[$i]->comment,
                'likeCount' => Likes::countLikes("comments", $comentPost[$i]->id),
                'myLike' => Likes::myLike('comments', $comentPost[$i]->id),

            );
        }

        $newPost =  array(
            'id' => $post->id,
            'idOwner' => $post->owner,
            'nameOwner' => $post->getOwnerDescription()->one()->nickname,
            'timeRecord' => NewsfeedController::getTimeRecord(strtotime($post->created)),
            'text' => $post->text,
            'repost' => $post->repost,
            'type' => $post->repost_type,
            'comments'=> $comments,
            'commentsCount'=>$commentsCount,
            'likeCount' => Likes::countLikes("board", $post->id),
            'myLike' => Likes::myLike('board', $post->id),
            'attachment' => NewsfeedController::getAttachment($post->id),
        );
        if (!empty($post->repost)) {
            $newPost['repost'] = self::makePost(Board::findOne(['id' => $post->repost]), true);
        };
        if ($isRepost) {
            $newPost['comments'] = [];
            $newPost['commentsCount'] = 0;
        }

       return $newPost;
    }

    public static function getAttachment($id)
    {
        $modelAttach = Attachments::findOne(['elem_type'=>'board', 'elem_id'=>$id]);

        if(!empty($modelAttach)){

            if($modelAttach->attachment_type == 'video'){
                $modelVideo = Video::find()->where(['id'=>$modelAttach->attachment_id])->asArray()->one();
                if (!empty($modelVideo)) {
                    $modelVideo['created'] = NewsfeedController::getTimeRecord(strtotime($modelVideo['created']));
                    return array('type' => 'video', 'modelVideo' => $modelVideo);
                }else{
                    return null;
                }
               
            }
            if($modelAttach->attachment_type == 'photo'){
                $modelPhoto = Photo::find()->where(['id'=>$modelAttach->attachment_id])->asArray()->one();
                if (!empty($modelPhoto)) {
                    $modelPhoto['created'] = NewsfeedController::getTimeRecord(strtotime($modelPhoto['created']));
                    return array('type' => 'photo', Photo::findOne(['id'=>$modelAttach->attachment_id]));
                }else{
                    return null;
                }
               
            }
        }else{
            return NULL;
        }
    }

    public function actionShowgeneralnews()
    {

        $data = Yii::$app->request->get();

        $page = $data['page'];
        $where = array();

        $loadMore = true;
        $check = self::getNews($page+1, $where);
        if(empty($check)){
            $loadMore = false;
        }

        $modelsGeneral = self::getGeneralNews($page+1, $where);
        
        $countGen = count($modelsGeneral);
        $generalNews = array();

        for($i=0; $i<$countGen; $i++){
            $generalNews[$i]['id'] = $modelsGeneral[$i]->id;
            $generalNews[$i]['idUser'] = $modelsGeneral[$i]->user;
            $generalNews[$i]['userName'] = UserDescription::findOne(['id' => $modelsGeneral[$i]->user])->name;
            $generalNews[$i]['created'] = NewsfeedController::getTimeRecord(strtotime($modelsGeneral[$i]->created));
            $generalNews[$i]['name'] = $modelsGeneral[$i]->name;
            $generalNews[$i]['small'] = $modelsGeneral[$i]->small;
            $generalNews[$i]['text'] = $modelsGeneral[$i]->text;
            $generalNews[$i]['url'] = ($modelsGeneral[$i]->article == 0) ? 'news/'.$modelsGeneral[$i]->url : 'article/'.$modelsGeneral[$i]->url;
            $generalNews[$i]['img'] = $modelsGeneral[$i]->img;
                $likes = ($modelsGeneral[$i]->article == 0) ? Likes::countLikes('news', $modelsGeneral[$i]->id) : Likes::countLikes('article', $modelsGeneral[$i]->id);
                $comments = ($modelsGeneral[$i]->article == 0) ? $modelsGeneral[$i]->getNewsComments()->count() : $modelsGeneral[$i]->getArticleComments()->count();
            $generalNews[$i]['countLikes'] = ($likes) ? $likes : 0;
            $generalNews[$i]['myLike'] = Likes::myLike('news', $modelsGeneral[$i]->id);
            $generalNews[$i]['countComments'] = (!empty($comments)) ? $comments : 0;
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            "generalNews" => $generalNews,
            "loadMore" => $loadMore,
            ];
    }

    public static function getGeneralNews($page = 1, $where = []){
        $query = News::find()->where($where);

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
            'page' => $page-1,
        ]);

        return $query->orderBy("id desc")
             ->offset($pagination->offset)
             ->limit($pagination->limit)
             ->all();
    }


    public static function getFriendsNews($page = 1, $where = [], $idFriends){
        $modelsFeed = Newsfeed::find()->where(['user_id' => $idFriends])->orderBy("id desc")->all();
        $countsFeed = count($modelsFeed);
        $boards = array();
        for($i=0; $i<$countsFeed; $i ++){
            $boards[] = $modelsFeed[$i]->elem_id;
        }
        
        $query = Board::find()->where(['id' => $boards])->andWhere('user = owner');

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
            'page' => $page-1,
        ]);

        return $query->orderBy("id desc")
             ->offset($pagination->offset)
             ->limit($pagination->limit)
             ->all();
    }

    public static function getFriendsId()
    {
        $model = Friend::find()->where(
                                    "(user1 = :id AND status = :status) OR (user2 = :id AND status = :status) ", 
                                    [':id' => Yii::$app->user->id, ':status' => 1])->all();
        $count = count($model);
        $friends = array();
        for($i=0; $i<$count; $i ++){
            $friends[] = ($model[$i]->user1 != Yii::$app->user->id) ? $model[$i]->user1 : $model[$i]->user2;
        }
        return $friends;
    }

    public static function getTimeRecord($time)
    {
        if( date('d m Y', time()) === date('d m Y', $time)){
            return "Сегодня в ".date('H:i', $time);
        }elseif( (date('d', time())-date('d', $time)) == 1 && date('m Y', time()) == date('m Y', $time)){
            return "Вчера в ".date('H:i', $time);
        }else{
            $monthes = array(
            1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
            5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
            9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря');
            
            return date('j', $time).' '. $monthes[(int)date('m', $time)].' в '.date('H:i', $time);
        }
    }

    public function actionLike() {
        $data = Yii::$app->request->get();
        $response = Likes::addLike($data['elem_type'], $data['id']);
        $response['likeCount'] = Likes::countLikes($data['elem_type'], $data['id']);
        $response['myLike'] = Likes::myLike($data['elem_type'], $data['id']);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionComment() {
        $data = Yii::$app->request->get();

        $idPost = $data['idPost'];
        $text = $data['text'];
        $atype = (!empty($data['atype'])) ? $data['atype'] : null;
        $aid = (!empty($data['aid'])) ? $data['aid'] : null;

        $response = Comments::addComment('board', $idPost, $text, $atype, $aid);

        if($response['ok']){
            $model = Comments::find()->where(array('elem_type' => 'board', 'elem_id' => $idPost))->orderBy('id desc')->limit(1)->one();
            $response['comment'] = $model->comment;
            $response['created'] = self::getTimeRecord(strtotime($model->created));
            $response['elem_id'] = $model->elem_id;
            $response['idComment'] = $model->id;
            $response['user_id'] = $model->user_id;
            $response['user_name'] = UserDescription::getNickname($model->user_id);
            $response['likeCount'] = Likes::countLikes("comments", $model->id);
            $response['myLike'] = Likes::myLike("comments", $model->id);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionDelcomment()
    {
        $data = Yii::$app->request->get();
        if (Comments::find()->where(array('id' => $data['idComment']))->count() > 0) {
            $response = Comments::delComment($data['idComment']);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionDel() { 

        $ok = false;

        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->get();
            $elem_id = $data['elem_id'];
            $elem_type = $data['elem_type'];
            $user_id = $data['user_id'];

            $model = Newsfeed::find()->where(['elem_id' => $elem_id, 'elem_type' => $elem_type, 'user_id' => $user_id])->one();
            if($model->delete()){
                $ok = true;
            }
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'ok' => $ok,
            ];
    }
}