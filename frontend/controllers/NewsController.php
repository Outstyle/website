<?php

namespace frontend\controllers;

/* TODO - get rid off unused stuff */

use Yii;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use common\models\News;
use backend\models\Category;
use frontend\components\ParentController;
use frontend\components\OutstylePortalController;
use common\models\SettingScript;
use yii\helpers\ArrayHelper;

class NewsController extends OutstylePortalController
{
    /**
     * Used in actionShow for 'news' representation
     * This is the template for any other controller to use
     *
     * @var string
     */
    public $partialViewFile = '_newsblock';

    /**
     * Since our news entity can represent various other entities,
     * (news can be article, can be videonews, release or reviews),
     * we must set up this variable for any other controller to know
     * what entity to query from.
     *
     * @var int
     */
    public $newsType = News::NEWS_TYPE_DEFAULT;

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
     * News index action.
     *
     * @return array
     */
    public function actionIndex()
    {
        $data = Yii::$app->request->get();
        $where = $response = [];

        /* Checking if our news is currently representing another type. If so, adding to our query params */
        $where['type'] = $this->newsType;

        /* Initial page to start load news from */
        $page = (!empty($data['page'])) ? (int) $data['page'] : 0;
        $contentHeight = (!empty($data['contentHeight'])) ? (int) $data['contentHeight'] : 0;

        $modelNews = News::getNews($where, $page);
        $newsCategories = Category::getCategories(['id' => News::NEWS_CATEGORIES]);
        ++$page;

        /**
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         *
         * Rendering as HTML code and rendering only partial view to avoid all page refresh
         */
        if (isset($data['ic-request'])) {
            $response['contentHeight'] = $contentHeight;
            $response['page'] = $page;
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Title', rawurlencode(Yii::$app->controller->id));
            $headers->add('X-IC-Trigger', '{"' . Yii::$app->controller->id . '":[' . Json::encode($response) . ']}');

            return $this->renderPartial('index', [
                'modelNews' => $modelNews,
                'newsCategories' => $newsCategories,
                'contentHeight' => $contentHeight,
                'page' => $page,
            ]);
        }

        /* Open Graph: https://github.com/dragonjet/yii2-opengraph */
        Yii::$app->opengraph->set([
            'title' => Yii::t('seo', Yii::$app->controller->id . '.title'),
            'description' => Yii::t('seo', Yii::$app->controller->id . '.description'),
            'image' => Url::toRoute(['css/i/opengraph/outstyle_default_968x504.jpg'], true),
        ]);

        return $this->render('index', [
            'modelNews' => $modelNews,
            'newsCategories' => $newsCategories,
            'contentHeight' => $contentHeight,
            'page' => $page,
        ]);
    }

    /**
     * API: Show news instance (outputs JSON or partial data).
     *
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

        /* Checking if our news is currently representing an article */
        $where['type'] = $this->newsType;

        /* If news does not exist - we show 404 */
        $modelNews = News::getNews($where, $page);

        /* If we don't have our model filled, that means we won't send another request, cause we're reached the end of news */
        if (!$modelNews) {
            $response['lastPageReached'] = 1;
            $response['page'] = $page;
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"' . Yii::$app->controller->id . '":[' . Json::encode($response) . ']}');

            // If we dont have anything in certain category, and it was a straight filter request
            if ($page == 0) {
                return '<center class="u-window-box--super events__body">' . Yii::t('app', 'This category has no active events!') . '</center>';
            }
            // If it was an empty model with filters applied (no posts in certain category)
            if ($page == 1) {
                return '<center class="u-window-box--super events__body">' . Yii::t('app', 'There is no data within this filtered category!') . '</center>';
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
                'modelNews' => $modelNews,
                'page' => $page,
                'contentHeight' => $contentHeight,
                'category' => $category,
            ]);
        }

        /* Default response in JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'modelNews' => $modelNews,
            'page' => $page,
            'contentHeight' => $contentHeight,
            'category' => $category,
        ];
    }

    /**
     * View news single page.
     *
     * @param string $url url slug as param (@urlManager)
     *
     * @return array|JSON
     */
    public function actionView($url = null)
    {
        $visibleScript = $bodyScript = '';
        $data = Yii::$app->request->get();
        $where = [];

        if ($url) {
            $where['url'] = $url;
            $where['type'] = $this->newsType;
        }

        $cache = Yii::$app->cache;
        $key = $url;
        $modelNews = $cache->get($key);
        if ($modelNews === false) {
            $modelNews = News::getNews($where);
            $cache->set($key, $modelNews, 3600 * 24); /* 1 day [?] */
        }

        /* If news does not exist - we show 404 */
        if (!$modelNews) {
            throw new NotFoundHttpException();
        }

        /**
         * Get settings for scripts (display or no)
         * if isset visibleScript, get setting body_script
         */

        $visibleScript = SettingScript::findOne(['param' => 'visible_script']);
        if (isset($visibleScript)) {
            $visibleScript = ArrayHelper::getValue($visibleScript, function ($visibleScript) {
                return $visibleScript->value;
            });

            $bodyScript = SettingScript::findOne(['param' => 'body_script']);
            if (!empty($bodyScript['value'])) {
                $bodyScript = ArrayHelper::getValue($bodyScript, function ($bodyScript) {
                    return $bodyScript->value;
                });
            }
        }

        /**
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events.
         *
         * Rendering as HTML code and rendering only partial view to avoid all page refresh
         */



        if (isset($data['ic-request'])) {
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Title', rawurlencode($modelNews[0]['title']));

            return $this->renderPartial('../news/view', [
                'modelNews' => $modelNews,
                'modelNewsRecommended' => $modelNews['recommended'] ?? 0,
                'modelNewsSimilar' => $modelNews['similar'] ?? 0,
                'visibleScript' => $visibleScript,
                'bodyScript' => $bodyScript,
            ]);
        }

        /* Open Graph: https://github.com/dragonjet/yii2-opengraph */
        Yii::$app->opengraph->set([
            'title' => $modelNews[0]['title'],
            'description' => $modelNews[0]['description'],
            'image' => Url::toRoute([$modelNews[0]['img']], true),
        ]);

        return $this->render('../news/view', [
            'modelNews' => $modelNews,
            'modelNewsRecommended' => $modelNews['recommended'] ?? 0,
            'modelNewsSimilar' => $modelNews['similar'] ?? 0,
            'visibleScript' => $visibleScript,
            'bodyScript' => $bodyScript,
        ]);
    }

    /**
     * View news category page.
     *
     * @param string $category News category
     *
     * @return array|JSON
     */
    public function actionViewcategory($category = null)
    {
        $where = [];
        if ($category) {
            $where['category'] = (Category::findOne(['url' => $category])->id) ?? '';
            $where['type'] = $this->newsType;
        }

        /* Initial page to start load news from */
        $page = $data['page'] ?? 0;
        $contentHeight = $data['contentHeight'] ?? 0;

        $modelNews = News::getNews($where, $page);
        $page++;

        /* If news does not exist - we show 404 */
        if (!$modelNews || empty($where['category'])) {
            throw new NotFoundHttpException();
        }

        /* Open Graph: https://github.com/dragonjet/yii2-opengraph */
        Yii::$app->opengraph->set([
            'title' => Yii::t('seo', Yii::$app->controller->id . '.' . $category . '.title'),
            'description' => Yii::t('seo', Yii::$app->controller->id . '.' . $category . '.description'),
            'image' => Url::toRoute(['css/i/opengraph/outstyle_default_968x504.jpg'], true),
        ]);

        return $this->render('index', [
            'modelNews' => $modelNews,
            'newsCategories' => Category::getCategories(['id' => News::NEWS_CATEGORIES]),
            'contentHeight' => $contentHeight,
            'page' => $page,
            'category' => $where['category'] ?? 0,
        ]);
    }
}
