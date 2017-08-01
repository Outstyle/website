<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\helpers\Json;

use app\models\Comments;

class CommentsController extends Controller
{
    public $layout = false;

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
                     'actions' => ['add', 'delete', 'show'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

/**
 * Adds a comment to DB and sends a JSON response (success or error)
 * urlManager route: api/comments/add
 * X-IC-Trigger - Intercooler headers to trigger certain events [http://intercoolerjs.org/docs.html]
 *
 * @return int      Added comment ID
 * TODO: this v [opt+]
 */
    public function actionAdd()
    {
        $data = Yii::$app->request->get();

        if (!isset($data['comments_message'])) {
            $data['comments_message'] = '';
        }

        $response = Comments::addComment($data['elem_type'], $data['elem_id'], $data['comments_message']); // Returns comment ID

        $headers = Yii::$app->response->headers;
        $headers->add('X-IC-Trigger', '{"commentAdd":['.Json::encode($response).']}');

        if (isset($data['ic-request']) && $response) {
            $where['id'] = $response;
            $modelComment = Comments::getComments($where); /* REDO! -req */

            return $this->renderPartial('_commentblock', [
                'modelComments' => $modelComment
            ]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

/**
 * Deletes comment from DB
 * @return boolean true|false
 */
    public function actionDelete()
    {
        $data = Yii::$app->request->get();
        $user_id = Yii::$app->user->id;

        $response = Comments::deleteComment($data['id'], $user_id);

        /**
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         */
        $headers = Yii::$app->response->headers;
        $headers->add('X-IC-Trigger', '{"commentDelete":['.Json::encode($response).']}');

        if (isset($data['ic-request'])) {
            return;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

/**
 * Shows comments
 * @return mixed    JSON array or HTML data
 */
    public function actionShow()
    {
        /* --- User data --- */
        $data = Yii::$app->request->get();

        /* --- Query for comments --- */
        $where['elem_id'] = $data['elem_id'];
        $where['elem_type'] = $data['elem_type'];

        $response = Comments::getComments($where);

        /**
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         *
         * Rendering as HTML code and rendering only partial view to avoid all page refresh
         * TODO: this v
         */
        if (isset($data['ic-request'])) {
            $page = '';
            $headers = Yii::$app->response->headers;

            $headerResponse['target'] = $data['ic-target-id'];
            $headerResponse['elem'] = $data['elem_type'];
            $headers->add('X-IC-Trigger', '{"comments":['.Json::encode($headerResponse).']}');

            return $this->renderPartial('_commentblock', [
                'modelComments' => $response,
                'page' => $page
            ]);
        }

        /* Default response in JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'modelComments' => $response,
        ];
    }
}
