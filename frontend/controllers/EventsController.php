<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\data\Pagination;
use yii\filters\AccessControl;

use yii\web\UploadedFile;
use yii\web\Response;
use yii\web\NotFoundHttpException;

use common\CImageHandler;

use frontend\models\Photo;
use frontend\models\Photoalbum;
use frontend\models\UserDescription;
use frontend\models\Likes;
use frontend\models\Comments;
use frontend\models\AuthAssignment;

use backend\models\Category;

use common\models\Events;

use frontend\components\ParentController;

class EventsController extends ParentController
{
    public $layout = 'portal';
    public $partialViewFile = '_eventsblock'; /* Used in actionShow for 'event' representation */

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
              'actions' => [
                'index',
                'view',
                'viewcategory',
                'show',
              ],
              'allow' => true,
            ],
            [
              'actions' => [
                'add'
              ],
              'allow' => true,
              'roles' => ['@'],
            ],
          ],
        ],
      ];
    }

    /**
     * Events index action
     * @return array
     */
    public function actionIndex()
    {
        $data = Yii::$app->request->get();
        $where = $response = [];

        /* Initial page to start load events from */
        $page = (!empty($data['page'])) ? (int)$data['page'] : 0;

        $modelEvents = Events::getEvents($where, $page);
        $eventsCategories = Category::getCategories(['id' => Events::EVENTS_CATEGORIES]);
        $page++;

        /**
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         *
         * Rendering as HTML code and rendering only partial view to avoid all page refresh
         */
        if (isset($data['ic-request'])) {
            $response['page'] = $page;
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Title', rawurlencode(Yii::$app->controller->id));
            $headers->add('X-IC-Trigger', '{"'.Yii::$app->controller->id.'":['.Json::encode($response).']}');

            return $this->renderPartial('index', [
              'modelEvents' => $modelEvents,
              'eventsCategories' => $eventsCategories,
              'page' => $page
            ]);
        }

        /* Open Graph: https://github.com/dragonjet/yii2-opengraph */
        Yii::$app->opengraph->set([
            'title' => Yii::t('seo', Yii::$app->controller->id.'.title'),
            'description' => Yii::t('seo', Yii::$app->controller->id.'.description'),
            'image' => Url::toRoute(['css/i/opengraph/outstyle_default_968x504.jpg'], true),
        ]);

        return $this->render('index', [
          'modelEvents' => $modelEvents,
          'eventsCategories' => $eventsCategories,
          'page' => $page
        ]);
    }

    /**
     * Show events instance (outputs JSON or partial data)
     * @return array|JSON
     */
    public function actionShow()
    {
        $data = Yii::$app->request->get();
        $where = [];

        /* Data validation */
        $page = $data['page'] ?? 0;
        $outstyle_news_height = $data['outstyle_news_height'] ?? 0;
        $category = $data['category'] ?? 0;

        /* Category filter validation - only numeric values are acceptable (needed for cleaning up values from API - users side) */
        $categories = $data['categories'] ?? 0;
        if (is_array($categories)) {
            foreach ($categories as $k => $v) {
                if (!is_numeric($v)) {
                    unset($categories[$k]);
                } else {
                    $categories[$k] = (int) $v;
                }
            }
        } else {
            $categories = (int) $categories;
        }

        /* Assigning WHERE clause and getting the model */
        if ($categories) {
            $where['category'] = $categories;
        }
        if ($category) {
            $where['category'] = $category;
        }

        /* If events does not exist - we show 404 */
        $modelEvents = Events::getEvents($where, $page);

        /* If we don't have our model filled, that means we won't send another request, cause we're reached the end of news */
        if (!$modelEvents) {
            $response['lastPageReached'] = 1;
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"'.Yii::$app->controller->id.'":['.Json::encode($response).']}');

            // If we dont have anything in certain category...
            if ($page == 0) {
                return '<center>'.Yii::t('app', 'This category has no active events!').'</center>';
            }

            return;
        }

        /**
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         *
         * Rendering as HTML code and rendering only partial view to avoid all page refresh
         */
        if (isset($data['ic-request'])) {
            $page++; // Let's add +1 to our page int, so rendered part would know from where to start

            $response['page'] = $page;

            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"'.Yii::$app->controller->id.'":['.Json::encode($response).']}');

            return $this->renderPartial($this->partialViewFile, [
                'modelEvents' => $modelEvents,
                'page' => $page,
                'category' => $category
            ]);
        }

        /* Default response in JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'modelEvents' => $modelEvents,
            'page' => $page,
            'category' => $category
        ];
    }

    /**
      * View news page (single instance)
      * @param  int $id     url as event id (@urlManager)
      * @return array|JSON
     */
    public function actionView($id)
    {
        $where = [];
        $where['id'] = (int)$id;

        $modelEvents = Events::getEvents($where);


        /* If news does not exist - we show 404 */
        if (!$modelEvents) {
            throw new NotFoundHttpException();
        }

        /**
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         *
         * Rendering as HTML code and rendering only partial view to avoid all page refresh
         */
        $data = Yii::$app->request->get();
        if (isset($data['ic-request'])) {
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Title', rawurlencode($modelEvents[0]['title']));

            return $this->renderPartial('view', [
                'modelEvents' => $modelEvents,
            ]);
        }

        return $this->render('view', [
            'modelEvents' => $modelEvents,
        ]);
    }

    /**
     * View events category page.
     *
     * @param string $category Event category
     *
     * @return array|JSON
     */
    public function actionViewcategory($category = null)
    {
        $where = [];
        if ($category) {
            $where['category'] = (Category::findOne(['url' => $category])->id) ?? '';
        }

        /* Initial page to start load events from */
        $page = $data['page'] ?? 0;

        $model = Events::getEvents($where, $page);
        $page++;

        /* If event does not exist - we show 404 */
        if (!$model || empty($where['category'])) {
            throw new NotFoundHttpException();
        }

        /* Open Graph: https://github.com/dragonjet/yii2-opengraph */
        Yii::$app->opengraph->set([
            'title' => Yii::t('seo', Yii::$app->controller->id.'.'.$category.'.title'),
            'description' => Yii::t('seo', Yii::$app->controller->id.'.'.$category.'.description'),
            'image' => Url::toRoute(['css/i/opengraph/outstyle_default_968x504.jpg'], true),
        ]);

        return $this->render('index', [
          'modelEvents' => $model,
          'eventsCategories' => Category::getCategories(['id' => Events::EVENTS_CATEGORIES]),
          'page' => $page,
          'category' => $where['category'] ?? 0,
        ]);
    }










    public static function getEvents($page = 1, $where = [])
    {
        $query = Events::find()->where($where)->andWhere(['status' => 1]);

        $pagination = new Pagination([
            'defaultPageSize' => 8,
            'totalCount' => $query->count(),
            'page' => $page-1,
        ]);

        return $query->orderBy("id desc")
             ->offset($pagination->offset)
             ->limit($pagination->limit)
             ->all();
    }



////добавление события
    public function actionAdd()
    {
        $model = new Events();

        $data = Yii::$app->request->post();

        if (Yii::$app->request->isAjax && $model->load($data)) {
            if (isset($data['ajax']) && ($data['ajax'] == 'form-resetDate')) {
                // Валидация формы
                return Json::encode(ActiveForm::validate($model));
            } else {
                // Форма отправлена по кнопке
            }
        }

        if ($model->load($data) && $model->validate()) {
            $model->user = Yii::$app->user->id;
            $model->date = strtotime($model->date);
            $model->description = $_POST["Events"]['description'];
            $model->created = time();

/////////создание альбома события
            if (UploadedFile::getInstances($model, 'imageFiles')) {
                $photoalbum = new Photoalbum();
                $photoalbum->user = Yii::$app->user->id;
                $photoalbum->name = $model->title;
                $photoalbum->url = Photoalbum::translateUrl($model->title);
                $photoalbum->created = date("Y-m-d H:i:s");
                $photoalbum->text = strip_tags($model->description);
                $photoalbum->portal_album = 1;
                if ($photoalbum->validate()) {
                    $photoalbum->save();

                    $album = Photoalbum::find()->where("user = :user", [':user' => Yii::$app->user->id])->orderBy('id desc')->limit(1)->one();
                    $idAlbum = $album->id;

                    $dir = Yii::getAlias('@frontend/web/images/');
                    if (!file_exists($dir)) {
                        @mkdir($dir, 0777);
                    }
                    $dir = Yii::getAlias('@frontend/web/images/events/');
                    if (!file_exists($dir)) {
                        @mkdir($dir, 0777);
                    }
                    $dir = Yii::getAlias('@frontend/web/images/events/'.$idAlbum.'/');
                    if (!file_exists($dir)) {
                        @mkdir($dir, 0777);
                    }
                }
/////////сохранение фото
                $files = UploadedFile::getInstances($model, 'imageFiles');

                foreach ($files as $file) {
                    $nameOfFile = md5($file->baseName.time()).'.jpg';
                    $fullName = $dir . $nameOfFile;
                    $fullNameMini = $dir . 'small_'.$nameOfFile;
                    $fullNameNormal = $dir . 'normal_'.$nameOfFile;
                    $uploaded = $file->saveAs($fullName);
                    $ih = new CImageHandler();
                    $ih->load($fullName)
                        ->thumb(400, 400, true)
                        ->crop(339, 99, false, false)
                        ->save($fullNameMini)
                        ->reload()
                        ->thumb(200, 200, true)
                        ->crop(126, 124, false, false)
                        ->save($fullNameNormal)
                        ->reload()
                        ->thumb(1010, 1010, true)
                        ->crop(1010, 355, false, false)
                        ->save($fullName);

                    $photo = new Photo();
                    $photo->user = Yii::$app->user->id;
                    $photo->album = $idAlbum;
                    $photo->name = $file->baseName;
                    $photo->img = $nameOfFile;
                    $photo->created = date("Y-m-d H:i:s");
                    if ($photo->validate()) {
                        $photo->save();
                    }
                }
            }
            if (isset($idAlbum) && $idAlbum) {
                $model->album = $idAlbum;
            }

            if ($model->validate()) {
                $model->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('add', [
            "model" => $model,
        ]);
    }

    public function actionEdit($id)
    {
        $model = Events::find()->where("id = :id", [':id' => $id])->one();

        if ($model->load(Yii::$app->request->post())) {
            $model->user = Yii::$app->user->id;
            $model->date = date("Y-m-d H:i:s");
            $model->description = $_POST["Events"]['description'];
        }
        return $this->render('edit', [
            "model" => $model,
        ]);
    }

    public function actionDel($id)
    {
        return Events::find()->where(array('id' => $id))->one()->delete();
    }



    public static function getOnePhotoEvent($id)
    {
        $model = Photo::find()->where("album = :album", [":album" => $id])->one();
        return $model->img;
    }

    public static function getPhotoEvent($id)
    {
        return Photo::find()->where("album = :album", [":album" => $id])->all();
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

    public function actionLike()
    {
        $data = Yii::$app->request->get();
        $response = Likes::addLike($data['elem_type'], $data['id']);
        $response['likeCount'] = Likes::countLikes($data['elem_type'], $data['id']);
        $response['myLike'] = Likes::myLike($data['elem_type'], $data['id']);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionEventcomment()
    {
        $data = Yii::$app->request->get();

        //var_dump($data);die();

        $idEvent = $data['idEvent'];
        $model = Events::findOne(['id' => $idEvent]);
        $comentEvent = $model->comments()->all();
        $commentsCount = $model->comments()->count();
        $comments = array();

        for ($i=0; $i<$commentsCount; $i++) {
            $comments[$i] = array(
                'id' => $comentEvent[$i]->id,
                'elem_type' => $comentEvent[$i]->elem_type,
                'elem_id' => $comentEvent[$i]->elem_id,
                'user_id' => $comentEvent[$i]->user_id,
                'user_name' => UserDescription::getNickname($comentEvent[$i]->user_id),
                'created' => EventsController::getTimeRecord(strtotime($comentEvent[$i]->created)),
                'comment' => $comentEvent[$i]->comment,
                'likeCount' => Likes::countLikes("comments", $comentEvent[$i]->id),
                'myLike' => Likes::myLike('comments', $comentEvent[$i]->id),
            );
        }

        $modelComment = array();
        $modelComment['commentsCount'] = $commentsCount;
        $modelComment['comments'] = $comments;
        $modelComment['isAdmin'] = AuthAssignment::isAdmin(Yii::$app->user->id);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $modelComment;
    }

    public function actionComment()
    {
        $data = Yii::$app->request->post();

        $idEvent = $data['idEvent'];
        $text = $data['text'];
        $atype = (!empty($data['atype'])) ? $data['atype'] : null;
        $aid = (!empty($data['aid'])) ? $data['aid'] : null;

        if (Events::find()->where(array('id' => $idEvent))->count() > 0) {
            $response = Comments::addComment('events', $idEvent, $text, $atype, $aid);
        }

        if ($response['ok']) {
            $model = Comments::find()->where(array('elem_type' => 'events', 'elem_id' => $idEvent))->orderBy('id desc')->limit(1)->one();
            $response['comment'] = $model->comment;
            $response['created'] = self::getTimeRecord(strtotime($model->created));
            $response['elem_id'] = $model->elem_id;
            $response['idComment'] = $model->id;
            $response['user_id'] = $model->user_id;
            $response['user_name'] = UserDescription::getNickname($model->user_id);
            $response['likeCount'] = Likes::countLikes("comments", $model->id);
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
}
