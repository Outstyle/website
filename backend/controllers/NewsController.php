<?php

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\search\NewsSearch;
use common\components\helpers\StringHelper;
use common\models\News;
use backend\models\Category;
use backend\models\StatusPublication;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
{
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
            /* TODO: ? */
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'imageupload' => [
                'class' => 'backend\widgets\imperavi\actions\UploadAction',
                'url' => Url::to('/images/news/'), // Directory URL address, where files are stored.
                'path' => '@frontend/web/images/news/', // Or absolute path to directory where files are stored.
            ],
            'deleteImage' => [
                'class' => 'demi\image\DeleteImageAction',
                'modelClass' => News::className(),
                'redirectUrl' => function ($model) {
                    return ['news/update', 'id' => $model->primaryKey];
                },
                'afterDelete' => function ($model) {
                    /* @var $model \yii\db\ActiveRecord */
                        if (Yii::$app->request->isAjax) {
                            Yii::$app->response->getHeaders()->set('Vary', 'Accept');
                            Yii::$app->response->format = yii\web\Response::FORMAT_JSON;

                            return ['status' => 'success', 'message' => 'Image deleted'];
                        } else {
                            return Yii::$app->response->redirect(['news/view', 'id' => $model->primaryKey]);
                        }
                },
            ],
            'cropImage' => [
                'class' => 'demi\image\CropImageAction',
                'modelClass' => News::className(),
                'redirectUrl' => function ($model) {
                    /* @var $model Post */
                    // triggered on !Yii::$app->request->isAjax, else will be returned JSON: {status: "success"}
                    return ['news/update', 'id' => $model->id];
                },
            ],

        ];
    }

    /**
     * Lists all News from DB, also representing them as articles if needed params are queried.
     *
     * @return mixed [view render]
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Yii::$app->controller->id);

        return $this->render('//news/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('//news/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new News model. News::STATUS_PUBLISHED
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        /* Data init: getting $_POST data */
        $data = Yii::$app->request->post();

        $model = new News();
        $categories = Category::getCategories(['id' => News::NEWS_CATEGORIES], '', true);
        $status = StatusPublication::getStatusList();


        /* Active form validation (AJAX) */
        if (Yii::$app->request->isAjax && $model->load($data)) {
            if (isset($data['ajax']) && ($data['ajax'] == 'form-create-news')) {
                return Json::encode(ActiveForm::validate($model));
            }
        }

        /*
         * Boot up our model with data
         * If validation from our model passes, continue with writing to DB and redirecting to further view
         */
        if ($model->load($data) && $model->validate()) {
            $model = $this->setData($data, $model);
            $model->created = date('Y-m-d H:i:s');

            /* If our model passes the validation, redirecting to view - otherwise showing JSON error output */
            if ($model->validate()) {
                $model->save();

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        /* Default view */
        return $this->render('//news/create', [
            'model' => $model,
            'categories' => $categories,
            'status' => $status,
            'errors' => $model->errors
        ]);
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /* Data init: getting $_POST data */
        $data = Yii::$app->request->post();

        $model = $this->findModel($id);
        $categories = Category::getCategories(['id' => News::NEWS_CATEGORIES], '', true);
        $status = StatusPublication::getStatusList();

        /* Active form validation (AJAX) */
        if (Yii::$app->request->isAjax && $model->load($data)) {
            if (isset($data['ajax']) && ($data['ajax'] == 'form-create-news')) {
                return Json::encode(ActiveForm::validate($model));
            }
        }

        /*
         * Boot up our model with data
         * If validation from our model passes, continue with writing to DB and redirecting to further view
         */
        if ($model->load($data) && $model->validate()) {
            $model = $this->setData($data, $model);

            /* If our model passes the validation, redirecting to view - otherwise showing JSON error output */
            if ($model->validate()) {
                $model->save();

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        /* Default view */
        return $this->render('//news/update', [
            'model' => $model,
            'categories' => $categories,
            'status' => $status,
            'errors' => $model->errors,
        ]);
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return News the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Populates model with user data.
     *
     * @see @common/models/News for validation rules
     *
     * @param array $data  Data to pupolate model with
     * @param array $model Initiated model to populate
     *
     * @return array $model Populated model
     */
    protected function setData($data, $model)
    {
        if(empty($model->url)){
            $model->url = StringHelper::slugify($model->name);
        }
        else{
            $model->url = StringHelper::slugify($model->url);
        }

        if($model->isNewRecord){
            $model->user = Yii::$app->user->id;
        }
        else{
            $model->user = $model->user;
        }
        $model->redactor_id = Yii::$app->user->id;
        $model->article = (Yii::$app->controller->id == 'news') ? 0 : 1;

        return $model;
    }
}
