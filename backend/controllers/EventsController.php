<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Events;
use backend\models\search\EventsSearch;
use common\models\geolocation\Geolocation;
use backend\models\Category;
use backend\models\StatusPublication;

/**
 * EventsController implements the CRUD actions for Events model.
 */
class EventsController extends Controller
{
    public function behaviors()
    {
        return [
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
            'deleteImage' => [
                'class' => 'demi\image\DeleteImageAction',
                'modelClass' => Events::className(),
                'redirectUrl' => function ($model) {
                    /* @var $model \yii\db\ActiveRecord */
                        // triggered on !Yii::$app->request->isAjax, else will be returned JSON: {status: "success"}
                        return ['events/update', 'id' => $model->primaryKey];
                },
                'afterDelete' => function ($model) {
                    /* @var $model \yii\db\ActiveRecord */
                        // You can customize response by this function, e.g. change response:
                        if (Yii::$app->request->isAjax) {
                            Yii::$app->response->getHeaders()->set('Vary', 'Accept');
                            Yii::$app->response->format = yii\web\Response::FORMAT_JSON;

                            return ['status' => 'success', 'message' => 'Image deleted'];
                        } else {
                            return Yii::$app->response->redirect(['events/view', 'id' => $model->primaryKey]);
                        }
                },
            ],
            'cropImage' => [
                'class' => 'demi\image\CropImageAction',
                'modelClass' => Events::className(),
                'redirectUrl' => function ($model) {
                    /* @var $model Post */
                    // triggered on !Yii::$app->request->isAjax, else will be returned JSON: {status: "success"}
                    return ['events/update', 'id' => $model->id];
                },
            ],
        ];
    }

    /**
     * Lists all Events models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventsSearch();
        $categories = Category::getCategories(['id' => Events::EVENTS_CATEGORIES], '', true);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'categories' => $categories,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Events model.
     *
     * @param int $id Existing Event ID
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $categories = Category::getCategories(['id' => Events::EVENTS_CATEGORIES], '', true);
        $status = StatusPublication::getStatusList();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'categories' => $categories,
            'status' => $status,
        ]);
    }

    /**
     * Creates a new Events model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->post();

        $model = new Events();
        $categories = Category::getCategories(['id' => Events::EVENTS_CATEGORIES], '', true);
        $status = StatusPublication::getStatusList();

        /* Active form validation (AJAX) */
        if (Yii::$app->request->isAjax && $model->load($data)) {
            if (isset($data['ajax']) && ($data['ajax'] == 'form-create-event')) {
                return Json::encode(ActiveForm::validate($model));
            }
        }

        /*
         * Boot up our model with data
         * If validation from our model passes, continue with writing to DB and redirecting to further view
         */

        if ($model->load($data) && $model->validate()) {
            $model = $this->setData($data, $model);
            $model->created = time();

            /* If our model passes the validation, redirecting to view - otherwise showing JSON error output */
            if ($model->validate()) {
                $model->events_date = date('Y-m-d H:i:s', strtotime($model->events_date));
                $model->save(false);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        /* Default view */
        return $this->render('create', [
            'model' => $model,
            'categories' => $categories,
            'status' => $status,
            'errors' => $model->errors,
        ]);
    }

    /**
     * Updates an existing Events model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id Existing Event ID
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $data = Yii::$app->request->post();
        $model = $this->findModel($id);

        $categories = Category::getCategories(['id' => Events::EVENTS_CATEGORIES], '', true);
        $status = StatusPublication::getStatusList();

        /* Active form validation (AJAX) */
        if (Yii::$app->request->isAjax && $model->load($data)) {
            if (isset($data['ajax']) && ($data['ajax'] == 'form-create-event')) {
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
                $model->events_date = date('Y-m-d H:i:s', strtotime($model->events_date));
                $model->save(false);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        /* Working with certain variables to make them more human-readable or etc. */
        $model->events_date = date('d.m.Y H:i', strtotime($model->events_date));

        /* Default view */
        return $this->render('create', [
            'model' => $model,
            'categories' => $categories,
            'status' => $status,
            'errors' => $model->errors,
        ]);
    }

    /**
     * Deletes an existing Events model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Events model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Events the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Events::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Populates model with user data.
     *
     * @see @common/models/Events for validation rules
     *
     * @param array $data  Data to pupolate model with
     * @param array $model Initiated model to populate
     *
     * @return array $model Populated model
     */
    protected function setData($data, $model)
    {
        $controllerId = ucfirst(Yii::$app->controller->id);

        $model->user = Yii::$app->user->id;
        $model->description = $data[$controllerId]['description'];

        /* TODO: ? What is the album? */
        $model->album = 0;
        if (isset($idAlbum) && $idAlbum) {
            $model->album = $idAlbum;
        }

        /* Saving geocode data */
        $model->geolocation_id = Geolocation::validateData($data) ?? $model->geolocation_id;

        /* Setting geolocation name */
        if (isset($data[$controllerId]['placename'])) {
            Geolocation::setGeolocationName($model->geolocation_id, $data[$controllerId]['placename']);
        }
        return $model;
    }
}
