<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\models\School;
use common\models\geolocation\Geolocation;
use backend\models\Category;
use common\components\helpers\StringHelper;

/* TODO */
use app\models\Photo;
use frontend\components\ParentController;

class SchoolController extends ParentController
{
    public $layout = 'portal';
    public $partialViewFile = '_schoolblock'; /* Used in actionShow for 'school' representation */

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
                  'show',
                  'get',
                ],
                'allow' => true,
              ],
              [
                'actions' => [
                  'add',
                ],
                'allow' => true,
                'roles' => ['@'],
              ],
            ],
          ],
        ];
    }

    /**
     * Schools index action.
     *
     * @return array
     */
    public function actionIndex()
    {
        $data = Yii::$app->request->get();
        $where = $response = [];

        /* Initial page to start load schools from */
        $page = (!empty($data['page'])) ? (int) $data['page'] : 0;
        $page_height = (!empty($data['page_height'])) ? (int) $data['page_height'] : 0;

        $model = School::getSchools($where, $page);
        $categories = Category::getCategories(['id' => School::SCHOOL_CATEGORIES]);
        ++$page;

        /*
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         *
         * Rendering as HTML code and rendering only partial view to avoid all page refresh
         */
        if (isset($data['ic-request'])) {
            $response['page_height'] = $page_height;
            $response['page'] = $page;

            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Title', rawurlencode(Yii::$app->controller->id));
            $headers->add('X-IC-Trigger', '{"'.Yii::$app->controller->id.'":['.Json::encode($response).']}');

            return $this->renderPartial('index', [
              'model' => $model,
              'categories' => $categories,
              'page' => $page,
              'page_height' => $page_height,
            ]);
        }

        /* Open Graph: https://github.com/dragonjet/yii2-opengraph */
        Yii::$app->opengraph->set([
            'title' => Yii::t('seo', Yii::$app->controller->id.'.title'),
            'description' => Yii::t('seo', Yii::$app->controller->id.'.description'),
            'image' => Url::toRoute(['css/i/opengraph/outstyle_default_968x504.jpg'], true),
        ]);

        return $this->render('index', [
          'model' => $model,
          'categories' => $categories,
          'page' => $page,
          'page_height' => $page_height,
        ]);
    }

    /**
     * Gets school data, based on $_REQUEST param.
     *
     * @return JSON
     */
    public function actionGet()
    {
        $data = Yii::$app->request->get();

        /* Default arguments for 'where' clause */
        $where['status'] = School::STATUS_PUBLISHED;

        /* GEODATA */
        if (isset($data['geodata'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return Geolocation::getGeodataDropdown(Yii::$app->controller->id, $where);
        }

        /* If nothing is triggered - throw 404 */
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * Show school instance (outputs JSON or partial data).
     *
     * @return array|JSON
     */
    public function actionShow()
    {
        $data = Yii::$app->request->get();
        $where = [];
        $page_height = '';
        $schools_to_show = ''; /* Needed for precise schools filtration, showing what cities having what schools */

        /* Data validation */
        $page = (!empty($data['page'])) ? (int) $data['page'] : 0;
        $page_height = (!empty($data['page_height'])) ? (int) $data['page_height'] : 0;

        /* Category filter validation - only numeric values are acceptable (needed for cleaning up values from API - users side) */
        $category = (!empty($data['category'])) ? $data['category'] : 0;
        if (is_array($category)) {
            foreach ($category as $k => $v) {
                if (!is_numeric($v)) {
                    unset($category[$k]);
                } else {
                    $category[$k] = (int) $v;
                }
            }
        } else {
            $category = (int) $category;
        }

        /* Assigning WHERE clause and getting the model */
        if ($category) {
            $where['category'] = $category;
        }

        /*
         * Working with objects (schools in cities), if there are any
         * FIXME: Rewrite this code to some more elegant solution regarding sibgle schoolId and array of schools
         * FIXME: Move this check to rules() in model
         */
        if (isset($data['schoolsId']) && !is_array($data['schoolsId'])) {
            if ((preg_match('/^\d+(\,\d+)*$/', $data['schoolsId']))) {
                $where['id'] = explode(',', $data['schoolsId']);
            }
        }

        /* If news does not exist - we show 404 */
        $model = School::getSchools($where, $page);
        $categories = Category::getCategories(['id' => School::SCHOOL_CATEGORIES]);

        /* If we don't have our model filled, that means we won't send another request, cause we're reached the end of news */
        if (!$model) {
            $response['lastPageReached'] = 1;
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"'.Yii::$app->controller->id.'":['.Json::encode($response).']}');

            return;
        }

        /*
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         *
         * Rendering as HTML code and rendering only partial view to avoid all page refresh
         */
        if (isset($data['ic-request'])) {
            ++$page; // Let's add +1 to our page int, so rendered part would know from where to start

            $response['page_height'] = ($page == 1) ? 500 : $page_height;
            $response['page'] = $page;

            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"'.Yii::$app->controller->id.'":['.Json::encode($response).']}');

            return $this->renderPartial($this->partialViewFile, [
                'model' => $model,
                'page' => $page,
                'page_height' => $page_height,
                'categories' => $categories,
                'category' => $category,
            ]);
        }

        /* Default response in JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'schools' => $model,
            'page' => $page,
        ];
    }

    /**
     * Single school view.
     *
     * @param string $id school id to choose by from DB
     *
     * @return array|JSON
     */
    public function actionView($id = null)
    {
        $where = [];
        $where['id'] = $id;

        $categories = Category::getCategories(['id' => School::SCHOOL_CATEGORIES]);

        $cache = Yii::$app->cache;
        $key = Yii::$app->controller->id.$id;
        $model = $cache->get($key);

        if ($model === false) {
            $model = School::getSchools($where);
            if (!$model) {
                throw new NotFoundHttpException();
            }
            $cache->set($key, $model, 3600 * 24); /* 1 day [?] */
        }

        /* Open Graph: https://github.com/dragonjet/yii2-opengraph */
        Yii::$app->opengraph->set([
            'title' => Yii::t('seo', '{'.Yii::$app->controller->id.'} - {category} school in {city}', [
              Yii::$app->controller->id => $model[0]['title'],
              'category' => $model[0]['category'],
              'city' => $model[0]['geolocation']['city'],
            ]),
            'description' => StringHelper::cutString($model[0]['description'] ?? '', 140),
            'image' => Url::toRoute(['css/i/opengraph/outstyle_default_968x504.jpg'], true),
        ]);

        return $this->render('view', [
           'model' => $model,
           'categories' => $categories,
        ]);
    }

    /* Should it be here? I mean, only admins (backend) should be able to delete any entity from DB */
    public function actionDel($id)
    {
        return School::find()->where(array('id' => $id))->one()->delete();
    }

    /* ---[?]--- */
    public static function getOnePhotoSchool($id)
    {
        $model = Photo::find()->where('album = :album', [':album' => $id])->one();

        return $model ? $model->img : null;
    }

    public static function getPhotoSchool($id)
    {
        return Photo::find()->where('album = :album', [':album' => $id])->all();
    }
}
