<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use yii\web\UploadedFile;
use common\CImageHandler;
use yii\web\HttpException;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use app\models\Photo;
use app\models\Photoalbum;

use frontend\components\ParentController;

/* Move or merge Parent cont */

class PhotoController extends \frontend\components\CsrfController
{
    public $layout = 'social';

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
     * User photos page
     * @param  integer $userId Logged user ID (Default: 0)
     * @return array
     */
    public function actionIndex($userId = 0)
    {
        $photos = Photo::getPhotos();
        $photoalbums = Photoalbum::getPhotoalbums();

        return $this->render('index', [
           'photos' => $photos,
           'photoalbums' => $photoalbums
        ]);
    }

    /**
     * [API] Photos uploading
     * @return JSON
     */
    public function actionUpload()
    {
        $this->layout = false;

        $photo = new Photo();
        $photo->album = Yii::$app->request->post('album_id');

        # Checking image file headers before image processing (resize and stuff)
        $imageFile = UploadedFile::getInstance($photo, 'img');

        if (isset($imageFile->tempName) && !exif_imagetype($imageFile->tempName)) {
            throw new HttpException(400, Yii::t('err', 'Image type is not valid!'));
        }

        # If basic validation is passed, assigning image for rules() 'img' filter
        $photo->img = $imageFile;

        if ($photo->validate()) {
            $photo->save();
        } else {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $photo->errors;
        }
    }

    public function actionAlbumlist()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $idOwner = $data['idOwner'];
            $page = $data['page'];
            $modelAlbums = self::getAlbum($idOwner, $page);
            $count = count($modelAlbums);
            $albums = array();
            $countAlbums = count($modelAlbums);
            $countFalse = 0;

            for ($i=0; $i<$countAlbums; $i++) {
                if (!UserPrivacy::getPrivacy($modelAlbums[$i]->privacy_album, $idOwner) && $idOwner != Yii::$app->user->id) {
                    $countFalse ++;
                    continue;
                }
                $albums[$i-$countFalse]['idAlbum'] = $modelAlbums[$i]->id;
                $albums[$i-$countFalse]['idUser'] = $modelAlbums[$i]->user;
                $albums[$i-$countFalse]['name'] = $modelAlbums[$i]->name;
                $albums[$i-$countFalse]['countPhoto'] = Photo::find()->where("album = :album", [':album' => $modelAlbums[$i]->id])->count();

                $cover = '';
                if (!empty($modelAlbums[$i]->cover)) {
                    $c = Photo::find()->where(['id' => $modelAlbums[$i]->cover])->one();
                    if ($c) {
                        $cover = $c->img;
                    }
                }
                $albums[$i-$countFalse]['photoCover'] = $cover;
            }
            $countAlbums = $countAlbums - $countFalse;
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'albums' => $albums,
            ];
    }

    public static function getAlbum($idOwner, $page = 1)
    {
        return Photoalbum::find()->where("user = :user AND portal_album = :portal_album", [':user' => $idOwner, ':portal_album' => 0])->orderBy("id desc")->all();
    }

    public function actionPhotolist()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $id = $data['idAlbum'];
            $page = $data['page'];
            $loadMore = true;

            $check = self::getPhoto($id, $page+1);
            if (empty($check)) {
                $loadMore = false;
            }

            $countPhotos = Photo::find()->where("album = :album", [':album' => $id])->count();
            $albumPhotos = self::getPhoto($id, $page);
            $countAlbumPhotos = count($albumPhotos);
            $modelPhotos = array();

            for ($i=0; $i<$countAlbumPhotos; $i++) {
                $modelPhotos[$i]['idPhoto'] = $albumPhotos[$i]->id;
                $modelPhotos[$i]['idOwner'] = $albumPhotos[$i]->user;
                $modelPhotos[$i]['idAlbum'] = $albumPhotos[$i]->album;
                $modelPhotos[$i]['name'] = $albumPhotos[$i]->name;
                $modelPhotos[$i]['nameImg'] = $albumPhotos[$i]->img;
                $modelPhotos[$i]['commentsCount'] = $albumPhotos[$i]->comments()->count();
                $modelPhotos[$i]['likeCount'] = Likes::countLikes('photo', $albumPhotos[$i]->id);
                $modelPhotos[$i]['myLike'] = Likes::myLike('photo', $albumPhotos[$i]->id);
            }
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'modelPhotos' => $modelPhotos,
            'countPhotos' => $countPhotos,
            'loadMore' => $loadMore,
            ];
    }


    public static function getPhoto($idAlbum, $page = 1)
    {
        $query = Photo::find()->where("album = :album",
                                [':album' => $idAlbum]);

        $pagination = new Pagination([
                    'defaultPageSize' => 15,
                    'totalCount' => $query->count(),
                    'page' => $page-1,
                ]);

        return $query->orderBy("id desc")
                     ->offset($pagination->offset)
                     ->limit($pagination->limit)
                     ->all();
    }

    public function actionPhotoinfo()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $id = $data['idPhoto'];
            $model = Photo::findOne(['id' => $id]);
            $modelAlbum = Photoalbum::findOne(['id' => $model->album]);
            $photo = array(
                'idPhoto' => $model->id,
                'idOwner' => $model->user,
                'idAlbum' => $model->album,
                'name' => $model->name,
                'nameImg' => $model->img,
                'created' => PhotosController::getAddTimePhoto(strtotime($model->created)),
                'userName' => $model->getUserNickname($model->user),
                'privacyComments' => UserPrivacy::getPrivacy($modelAlbum->privacy_comments, $model->user),
                'myLike' => Likes::myLike('photo', $id),
            );

            $countLikes = Likes::countLikes('photo', $id);
            $comentPhoto = $model->comments()->all();
            $commentsCount = $model->comments()->count();
            $comments = array();

            for ($i=0; $i<$commentsCount; $i++) {
                $comments[$i] = array(
                    'id' => $comentPhoto[$i]->id,
                    'elem_type' => $comentPhoto[$i]->elem_type,
                    'elem_id' => $comentPhoto[$i]->elem_id,
                    'user_id' => $comentPhoto[$i]->user_id,
                    'user_name' => UserDescription::getNickname($comentPhoto[$i]->user_id),
                    'created' => PhotosController::getTimeRecord(strtotime($comentPhoto[$i]->created)),
                    'comment' => $comentPhoto[$i]->comment,
                    'likeCount' => Likes::countLikes('comments', $comentPhoto[$i]->id),
                    'myLike' => Likes::myLike('comments', $comentPhoto[$i]->id),
                );
            }

            $comments['commentsCount'] = $commentsCount;
            $isAdmin = AuthAssignment::isAdmin(Yii::$app->user->id);

            $photoInfo = array(
                    'photo' => $photo,
                    'comments' => $comments,
                    'countLikes' => $countLikes,
                    'isAdmin' => $isAdmin,
                );
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'photoInfo' => $photoInfo,
            ];
    }

    public static function getTimeRecord($time)
    {
        if (date('d m Y', time()) === date('d m Y', $time)) {
            return "Сегодня в ".date('H:i', $time);
        } elseif ((date('d', time())-date('d', $time)) == 1 && date('m Y', time()) == date('m Y', $time)) {
            return "Вчера в ".date('H:i', $time);
        } else {
            $monthes = array(
            1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
            5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
            9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря');

            return date('j', $time).' '. $monthes[(int)date('m', $time)].' в '.date('H:i', $time);
        }
    }

    public static function getAddTimePhoto($time)
    {
        $monthes = array(
            1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
            5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
            9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
        );
        return date('j', $time).' '. $monthes[(int)date('m', $time)].' в '.date('H:i', $time);
    }

    public function actionView($id)
    {
        $name = UserDescription::findOne(['id' => $id])->name;
        $model = Photoalbum::find()->where("user = :user AND portal_album = :portal_album", [':user' => $id, ':portal_album' => 0])->one();
        return $this->render('view', [
            'id' => $id,
            'name' => $name,
            'model' => $model,
        ]);
    }

    public function actionAddalbum()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $photoalbum = new Photoalbum();
            $photoalbum->user = Yii::$app->user->id;
            $photoalbum->name = $data['nameNewAlbum'];
            $photoalbum->url = Photoalbum::translateUrl($photoalbum->name);
            $photoalbum->created = date("Y-m-d H:i:s");
            $photoalbum->text = strip_tags($data['descriptionNewAlbum']);
            $photoalbum->privacy_album = $data['privatNewAlbum'];
            $photoalbum->privacy_comments = $data['privatNewAlbumPhoto'];

            if ($photoalbum->validate()) {
                $photoalbum->save();

                $album = Photoalbum::find()->where("user = :user", [':user' => Yii::$app->user->id])->orderBy('id desc')->limit(1)->one();
                $idAlbum = $album->id;

                $dir = Yii::getAlias('@frontend/web/images/photo/');
                if (!file_exists($dir)) {
                    @mkdir($dir, 0777);
                }

                $dir = Yii::getAlias('@frontend/web/images/photo/'.Yii::$app->user->id.'/');
                if (!file_exists($dir)) {
                    @mkdir($dir, 0777);
                }

                $dir = Yii::getAlias('@frontend/web/images/photo/'.Yii::$app->user->id.'/'.$idAlbum.'/');
                if (!file_exists($dir)) {
                    @mkdir($dir, 0777);
                }
                $this->redirect(['photos/index']);
            }
            $ok = false;
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'ok' => $ok,
            ];
    }

    public function actionDelalbum()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $idAlbum = $data['idAlbum'];

            $albumDel = Photoalbum::find()
                            ->where("user = :user AND id = :id", [':user' => Yii::$app->user->id, ':id' => $idAlbum])
                            ->one();

            if (!empty($albumDel->created)) {
                $albumDel->delete();
            }
            $albumDel->delete();
            $dir = Yii::getAlias('@frontend/web/images/photo/'.Yii::$app->user->id.'/'.$idAlbum);
            self::removeDirectory($dir);
            $ok = true;
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'ok' => $ok,
            ];
    }

    public static function removeDirectory($dir)
    {
        if ($objs = glob($dir."/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

    public function actionLoadphoto()
    {
        $ok = false;
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            foreach ($_FILES as $files) {
                $fileUp = $files['tmp_name'];
                $fileNameUp = $files['name'];
            }
            $idAlbum = $data['idAlbum'];
            $nameImg = $data['nameImg'];

            $dir = Yii::getAlias('@frontend/web/images/photo/'.Yii::$app->user->id.'/'.$idAlbum.'/');
            $photo = new Photo();

            $file = $fileUp;
            $nameOfFile = md5($fileNameUp.time()).'.jpg';
            $photo->image = $file;
            $fullName = $dir . $nameOfFile;
            $fullNameMini = $dir . 'small_'.$nameOfFile;
            $fullNameNormal = $dir . 'normal_'.$nameOfFile;
            move_uploaded_file($file, $fullName) ;
            $ih = new CImageHandler();
            $ih->load($fullName)
                    ->thumb(300, 300, true)
                    ->crop(216, 123, false, false)
                    ->save($fullNameMini)
                    ->reload()
                    ->resize(400, 400)
                    ->save($fullNameNormal)
                    ->reload()
                    ->resize(800, 800)
                    ->save($fullName);
            $photo->image = $photo->img = $nameOfFile;
            $photo->user = Yii::$app->user->id;
            $photo->album = $data['idAlbum']; ///передать айди альбома
                $photo->created = date("Y-m-d H:i:s");
            $photo->name = $nameImg;
            if ($photo->validate()) {
                $photo->save();
                $lastAlbumPhoto = Photo::find()->where("album = :album", [':album' => $idAlbum])->orderBy("id desc")->limit(1)->one();
                $modelPhotos = array(
                        'idPhoto' => $lastAlbumPhoto->id,
                        'idOwner' => $lastAlbumPhoto->user,
                        'idAlbum' => $lastAlbumPhoto->album,
                        'name' => $lastAlbumPhoto->name,
                        'nameImg' => $lastAlbumPhoto->img,
                        );
            }
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'modelPhotos' => $modelPhotos,
            ];
    }

    public function actionDelphoto()
    {
        $ok = false;
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $idAlbum = $data['idAlbum'];
            $idPhoto = $data['idPhoto'];

            $delPhoto = Photo::find()
                            ->where("id = :id",
                                [':id' => $idPhoto])
                            ->one();
            if ($delPhoto) {
                $dir = Yii::getAlias('@frontend/web/images/photo/'.Yii::$app->user->id.'/'.$idAlbum.'/');
                $fullName = $dir . $delPhoto->img;
                $fullNameMini = $dir . 'small_'. $delPhoto->img;
                $fullNameNormal = $dir . 'normal_'. $delPhoto->img;
                if (file_exists($fullName)) {
                    unlink($fullName);
                }
                if (file_exists($fullNameNormal)) {
                    unlink($fullNameNormal);
                }
                if (file_exists($fullNameMini)) {
                    unlink($fullNameMini);
                }

                $delPhoto->delete();
            }
            $ok = true;
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'ok' => $ok,
            ];
    }

    public function actionEdit()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $id = $data['idAlbum'];

            $albumEdit = Photoalbum::find()
                                ->where("id = :id", [':id' => $id])
                                ->one();

//            $lastAlbumPhoto = Photo::find()->where("album = :album", [':album' => $id])->orderBy("id desc")->limit(1)->one();

            $photoCover = null;
            if ($albumEdit->cover) {
                $cover = Photo::find()->where("id = :id", [':id' => $albumEdit->cover])->one();
                if ($cover) {
                    $photoCover = $cover->img;
                }
            }

            $modelAlbum = array(
                'idAlbum' => $albumEdit->id,
                'idOwner' => $albumEdit->user,
                'name' => $albumEdit->name,
                'text' => $albumEdit->text,
                'photoCover' => $photoCover,
                'privacy_album' => $albumEdit->privacy_album,
                'privacy_comments' => $albumEdit->privacy_comments,
                );

            $albumPhotos = Photo::find()
                                ->where("album = :album", [':album' => $id])
                                ->orderBy("id desc")
                                ->all();
            $countPhotos = count($albumPhotos);
            $modelPhotos = array();
            for ($i=0; $i<$countPhotos; $i++) {
                $modelPhotos[$i]['idPhoto'] = $albumPhotos[$i]->id;
                $modelPhotos[$i]['idOwner'] = $albumPhotos[$i]->user;
                $modelPhotos[$i]['idAlbum'] = $albumPhotos[$i]->album;
                $modelPhotos[$i]['name'] = $albumPhotos[$i]->name;
                $modelPhotos[$i]['nameImg'] = $albumPhotos[$i]->img;
            }
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'modelAlbum' => $modelAlbum,
            'modelPhotos' => $modelPhotos,
        ];
    }

    public function actionLike()
    {
        $data = Yii::$app->request->get();
        $response = Likes::addLike($data['elem_type'], $data['id']);
        $response['likeCount'] = Likes::countLikes($data['elem_type'], $data['id']);
        $response['myLike'] = Likes::myLike($data['elem_type'], $data['id']);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionComment()
    {
        $data = Yii::$app->request->get();

        $atype = (!empty($data['atype'])) ? $data['atype'] : null;
        $aid = (!empty($data['aid'])) ? $data['aid'] : null;

        if (Photo::find()->where(['id' => $data['id']])->count() > 0) {
            $response = Comments::addComment('photo', $data['id'], $data['text'], $atype, $aid);
        }

        if ($response['ok']) {
            $model = Comments::find()->where(array('elem_type' => 'photo', 'elem_id' => $data['id']))->orderBy('id desc')->limit(1)->one();
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
        if (Comments::find()->where(array('id' => $data['id']))->count() > 0) {
            $response = Comments::delComment($data['id']);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionAlbumcomment()
    {
        $data = Yii::$app->request->get();

        $idAlbum = $data['idAlbum'];
        $nameAlbum = Photoalbum::findOne(['id' => $idAlbum])->name;
        $idOwner = Photoalbum::findOne(['id' => $idAlbum])->user;
        $modelsPhotos = Photo::find()->where(['album' => $idAlbum])->orderBy("id asc")->all();
        $countPhotos = count($modelsPhotos);
        $idPhoto = array();

        for ($i = 0; $i<$countPhotos; $i++) {
            $idPhoto[] = $modelsPhotos[$i]->id;
        }
        $albumComments = array();
        $modelComments = Comments::find()->where(['elem_id' => $idPhoto, 'elem_type' => 'photo'])->orderBy('id desc')->all();
        if (!empty($modelComments)) {
            $countComments = count($modelComments);

            for ($i = 0; $i<$countComments; $i++) {
                $albumComments[$i]['comment'] = $modelComments[$i]->comment;
                $albumComments[$i]['created'] = self::getTimeRecord(strtotime($modelComments[$i]->created));
                $albumComments[$i]['user_name'] = UserDescription::getNickname($modelComments[$i]->user_id);
                $albumComments[$i]['user_id'] = $modelComments[$i]->user_id;
                $albumComments[$i]['likeCount'] = Likes::countLikes("comments", $modelComments[$i]->id);
                $albumComments[$i]['myLike'] = Likes::myLike("comments", $modelComments[$i]->id);
                $albumComments[$i]['idPhoto'] = $modelComments[$i]->elem_id;
                $albumComments[$i]['idComment'] = $modelComments[$i]->id;
                $albumComments[$i]['nameImg'] = Photo::findOne(['id' => $modelComments[$i]->elem_id])->img;
            }
        }
        $albumComments['albumName'] = $nameAlbum;
        $albumComments['countComments'] = $countComments;
        $albumComments['idAlbum'] = $idAlbum;
        $albumComments['idOwner'] = $idOwner;

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $albumComments;
    }

////редактирование альбома
    public function actionEditalbum()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();

            $idAlbum = $data['idAlbum'];

            $albumEdit = Photoalbum::find()
                            ->where("user = :user AND id = :id", [':user' => Yii::$app->user->id, ':id' => $idAlbum])
                            ->one();

            if ($data['newNameAlbum'] != "") {
                $albumEdit->name = $data['newNameAlbum'];
                $albumEdit->url = Photoalbum::translateUrl($albumEdit->name);
            }
            if ($data['newDescriptionAlbum'] != "") {
                $albumEdit->text = $data['newDescriptionAlbum'];
            }

            if ($data['newNameAlbum'] != "") {
                $albumEdit->privacy_album = $data['privatAlbum'];
                $albumEdit->privacy_comments = $data['privatAlbumPhoto'];
            }

            if ($albumEdit->validate()) {
                $albumEdit->save();
            }
            $ok = true;
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'ok' => $ok,
            ];
    }

////редактирование фото
    public function actionEditphoto()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->get();
            $idPhoto = $data['idPhoto'];
            $newDescription = $data['newDescription'];
            $edPhoto = Photo::find()
                            ->where("id = :id AND user = :user",
                                [':id' => $idPhoto, ':user' => Yii::$app->user->id])
                            ->one();

            $edPhoto->name = $newDescription;

            if ($edPhoto->validate()) {
                $edPhoto->save();
            }
            $ok = 1;
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'ok' => $ok,
            ];
    }

    public function actionCountlikes()
    {
        if (Photo::find()->where(['id' => $data['id']])->count() > 0) {
            $response = Likes::countLikes('photo', $data['id']);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionCountcomments()
    {
        $data = Yii::$app->request->get();
        if (Photo::find()->where(array('id' => $data['id']))->count() > 0) {
            $response = Comments::countComments('photo', $data['id']);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }


    public function actionRepost()
    {
        $data = Yii::$app->request->get();

        $model = new Board();
        $model->user = Yii::$app->user->id;
        $model->owner = Yii::$app->user->id;
        $model->created = date("Y-m-d H:i:s", time());
        $model->text = '';

        if ($model->validate()) {
            $model->save();
            $modelNewsFeed = new Newsfeed();
            $modelNewsFeed->elem_type = "board";
            $modelNewsFeed->elem_id = $model->id;
            $modelNewsFeed->user_id = $model->user;
            if ($modelNewsFeed->validate()) {
                $modelNewsFeed->save();
            }
        }

        $modelAtt = Board::find()->where(array('user' => Yii::$app->user->id, 'owner' => Yii::$app->user->id))->orderBy('id desc')->limit(1)->one();
        $attachment = Attachments::addAttachment('board', $modelAtt->id, 'photo', $data['id']);

        $ok = false;
        if (!empty($attachment)) {
            $ok = true;
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'ok' => $ok,
            ];
    }

    public function actionPhotoforcover()
    {
        $data = Yii::$app->request->get();
        $idAlbum = $data['idAlbum'];
        $model = Photo::find()->where("album = :album", [':album' => $idAlbum])->orderBy('id desc')->all();

        if (!empty($model)) {
            $countPhotos = count($model);
            $modelPhotos = array();

            for ($i=0; $i<$countPhotos; $i++) {
                $modelPhotos[$i] = array(
                    'idPhoto' => $model[$i]->id,
                    'idAlbum' => $model[$i]->album,
                    'idOwner' => $model[$i]->user,
                    'nameImg' => $model[$i]->img,
                    );
            }
        } else {
            $modelPhotos = false;
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'modelPhotos' => $modelPhotos,
            ];
    }

    public function actionSetcover()
    {
        $data = Yii::$app->request->get();

        $idAlbum = $data['idAlbum'];
        $idPhoto = $data['idPhoto'];
        $model = Photoalbum::find()->where(['id' => $idAlbum])->one();

        if (!empty($model)) {
            $model->cover = $idPhoto;
            if ($model->validate()) {
                $model->save();
            }
            $modelCover = Photo::find()->where(['id' => $idPhoto])->one();
            $cover = array(
                'idPhoto' => $modelCover->id,
                'idAlbum' => $modelCover->album,
                'idOwner' => $modelCover->user,
                'nameImg' => $modelCover->img,
                );
            $response = $cover;
        } else {
            $response = false;
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'response' => $response,
            ];
    }
}
