<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\NotFoundHttpException;

use backend\models\Category;
use common\models\Events;
use frontend\components\OutstylePortalController;

class EventsController extends OutstylePortalController
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
        $contentHeight = (!empty($data['contentHeight'])) ? (int) $data['contentHeight'] : 0;

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
            $headers->add('X-IC-Trigger', '{"' . Yii::$app->controller->id . '":[' . Json::encode($response) . ']}');

            return $this->renderPartial('index', [
                'modelEvents' => $modelEvents,
                'eventsCategories' => $eventsCategories,
                'contentHeight' => $contentHeight,
                'page' => $page
            ]);
        }

        /* Open Graph: https://github.com/dragonjet/yii2-opengraph */
        Yii::$app->opengraph->set([
            'title' => Yii::t('seo', Yii::$app->controller->id . '.title'),
            'description' => Yii::t('seo', Yii::$app->controller->id . '.description'),
            'image' => Url::toRoute(['css/i/opengraph/outstyle_default_968x504.jpg'], true),
        ]);

        return $this->render('index', [
            'modelEvents' => $modelEvents,
            'eventsCategories' => $eventsCategories,
            'contentHeight' => $contentHeight,
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
        $contentHeight = $data['contentHeight'] ?? 0;
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
            $headers->add('X-IC-Trigger', '{"' . Yii::$app->controller->id . '":[' . Json::encode($response) . ']}');

            // If we dont have anything in certain category...
            if ($page == 0) {
                return '<center>' . Yii::t('app', 'This category has no active events!') . '</center>';
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

            $response['contentHeight'] = ($page == 1) ? 500 : $contentHeight;
            $response['page'] = $page;

            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"' . Yii::$app->controller->id . '":[' . Json::encode($response) . ']}');

            return $this->renderPartial($this->partialViewFile, [
                'modelEvents' => $modelEvents,
                'page' => $page,
                'contentHeight' => $contentHeight,
                'category' => $category
            ]);
        }

        /* Default response in JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'modelEvents' => $modelEvents,
            'page' => $page,
            'contentHeight' => $contentHeight,
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
            $headers->add('X-IC-Trigger', '{"' . Yii::$app->controller->id . Yii::$app->controller->action->id . '":[]}');

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
        $contentHeight = $data['contentHeight'] ?? 0;

        $model = Events::getEvents($where, $page);
        $page++;

        /* If event does not exist - we show 404 */
        if (!$model || empty($where['category'])) {
            throw new NotFoundHttpException();
        }

        /* Open Graph: https://github.com/dragonjet/yii2-opengraph */
        Yii::$app->opengraph->set([
            'title' => Yii::t('seo', Yii::$app->controller->id . '.' . $category . '.title'),
            'description' => Yii::t('seo', Yii::$app->controller->id . '.' . $category . '.description'),
            'image' => Url::toRoute(['css/i/opengraph/outstyle_default_968x504.jpg'], true),
        ]);

        return $this->render('index', [
            'modelEvents' => $model,
            'eventsCategories' => Category::getCategories(['id' => Events::EVENTS_CATEGORIES]),
            'contentHeight' => $contentHeight,
            'page' => $page,
            'category' => $where['category'] ?? 0,
        ]);
    }
}
