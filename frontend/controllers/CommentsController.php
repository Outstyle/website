<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\helpers\Json;

use app\models\Comments;
use app\models\Attachments;

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

        $newCommentId = Comments::addComment($data['elem_type'], $data['elem_id'], $data['comments_message'] ?? ''); // Returns comment ID
        $headerResponse = $newCommentId;

        if (is_numeric($newCommentId)) {
            $headerResponse = [];
            $headerResponse['elem_type'] = $data['elem_type'];
            $headerResponse['parent_id'] = (int)$data['elem_id'];
            $headerResponse['elem_id'] = $newCommentId;

            # Adds an attachment to comment, if valid data is present
            if (isset($data['attachments'])) {
                $attachments = Attachments::parseStringForAttachments($data['attachments']);
                foreach ($attachments as $attachment) {
                    $added_attachments[] = Attachments::addAttachment($attachment[0], $attachment[1], $data['elem_type'], $newCommentId);
                }
                $headerResponse['attachments'] = $added_attachments;
            }
        }

        $headers = Yii::$app->response->headers;
        $headers->add('X-IC-Trigger', '{"commentAdd":['.Json::encode($headerResponse).']}');

        if (isset($data['ic-request']) && $newCommentId) {
            $where['id'] = $newCommentId;
            $modelComment = Comments::getComments($where); /* REDO! -req */

            return $this->renderPartial('_commentblock', [
                'modelComments' => $modelComment
            ]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $newCommentId;
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
            $headerResponse['elem_type'] = $data['elem_type'];
            $headerResponse['elem_id'] = $data['elem_id'];
            $headers->add('X-IC-Trigger', '{"commentsShow":['.Json::encode($headerResponse).']}');

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
