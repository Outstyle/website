<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\AccessControl;
use yii\helpers\Json;

use app\models\Video;
use app\models\Attachments;
use common\components\helpers\html\AttachmentsHelper;

class AttachmentsController extends Controller
{
    public $layout = false;

    /**
     * @inheritdoc
     */
    // public function beforeAction($event)
    // {
    //     /* Since we don't want direct access to content, we should perform token check every time we access the controller */
    //     $csrf_token = Yii::$app->request->headers->get('x-csrf-token');
    //     $user_token = Yii::$app->request->get('_csrf');
    //
    //     if (!$user_token) {
    //         throw new HttpException(400, Yii::t('err', 'Token empty!'));
    //     }
    //     if ($user_token != $csrf_token) {
    //         throw new HttpException(400, Yii::t('err', 'Token is invalid!'));
    //     }
    //
    //     /* Checking for allowed elements being represented as attachments */
    //     $elem_type = Yii::$app->request->get('elem_type');
    //
    //     if (is_numeric($elem_type)) {
    //         $elem_type = AttachmentsHelper::$allowedElements[$elem_type];
    //     }
    //
    //     if (!in_array($elem_type, AttachmentsHelper::$allowedElements) && $elem_type) {
    //         throw new HttpException(400, Yii::t('err', 'Element type is not supported!'));
    //     }
    //
    //     return parent::beforeAction($event);
    // }

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
                        'get',
                        'add',
                        'delete',
                        'list'
                      ],
                      'allow' => true,
                      'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }

    /**
     * Gets user attachments list based on element type (i.e. $type = 'video' gets all user videos)
     * @param string    $type
     * @return mixed
     */
    public function actionGet($type)
    {
        if ($type == 'video') {
            $model = Video::getByUserId();
        }

        return $this->render($type, [
            'model' => $model,
            'elem_type' => Yii::$app->request->get('elem_type'), /* Check is already performed in 'beforeAction' event */
            'elem_type_parent' => Yii::$app->request->get('elem_type_parent')
        ]);
    }

    /**
     * Add user attachment
     * @return mixed
     */
    public function actionAdd()
    {
        $data = Yii::$app->request->get();

        /*
         * If we already do have an element ID to which an attachment can be added, we can write with DB to store that
         */
        if ($data['elem_id'] && $data['elem_type']) {
            $response = Attachments::addAttachment($data['type'], $data['id'], $data['elem_type'], $data['elem_id']);
        } else {
            $response['attachment'] = Attachments::prepareAttachment($data['type'], $data['id'], $data['elem_type']);
            $response[Yii::$app->request->csrfParam] = $data[Yii::$app->request->csrfParam];

            /*
             * If we don't have an active element to tie attachment to (i.e. comment, that is not yet sent), we're working with localstorage and headers
             * We also need to pass _csrf token to make request valid
             */
            if (isset($data['ic-request'])) {
                $headers = Yii::$app->response->headers;
                $headers->add('X-IC-Trigger', '{"attachmentPrepareAdd":['.Json::encode($response).']}');
            }
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    /**
     * Delete user attachment
     * @return mixed
     */
    public function actionDelete()
    {
        $data = Yii::$app->request->get();

        /* If it's an Intercooler request, we won't send back any data, since we're working on clientside */
        if (isset($data['ic-request'])) {
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"attachmentDelete":[]}');
            return;
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
    }

    /**
     * Lists an attachments from localstorage, based on element ID and element type
     * @return mixed
     */
    public function actionList()
    {
        $data = Yii::$app->request->get();
        $activeAttachments = [];

        /* List attachments from localstorage */
        if (isset($data) && !isset($data['elem_id']) && !isset($data['elem_type'])) {
            foreach ($data as $elem_type => $attachments) {
                if (is_int($elem_type)) {
                    foreach ($attachments as $key => $attachment) {
                        $attachment = Attachments::parseStringForAttachment($attachment);
                        $activeAttachments[$key] = Attachments::getAttachmentByTypeAndId($attachment[0], $attachment[1]);
                    }
                }
            }
        } else {
            $attachments = Attachments::find()->where([
              'elem_type' => $data['elem_type'],
              'elem_id' => $data['elem_id']
            ])->asArray()->all();


            /* Attachment type check to get atual model of the attached element */
            foreach ($attachments as $key => $attachment) {
                $activeAttachments[$key] = Attachments::getAttachmentByTypeAndId($attachment['attachment_type'], $attachment['attachment_id']);
            }
        }

        return $this->render('view', [
            'model' => $activeAttachments,
            'elem_type' => $elem_type ?? false
        ]);
    }
}
