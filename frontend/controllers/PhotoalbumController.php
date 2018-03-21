<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace frontend\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\models\Photo;
use app\models\Photoalbum;

/**
 * Photoalbum controller: for working with user photoalbums
 * Must be extended from CsrfController for token compare
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class PhotoalbumController extends \frontend\components\CsrfController
{
    /**
     * Layout to be used for all the actions
     * @var string|false
     */
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
                    'allow' => true,
                    'actions' => [
                      'create',
                      'view',
                      'delete',
                      'edit'
                     ],
                    'roles' => ['@'],
                  ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists photos from an album
     * @return HTML
     */
    public function actionView()
    {
        $albumId = (int)Yii::$app->request->post('album_id');
        $albumName = Yii::$app->request->post('album_name');

        if (!is_int($albumId)) {
            throw new HttpException(400, Yii::t('err', 'Album ID must be a number'));
        }

        /* WHERE clause - @see: Photo::getPhotos parameters */
        $where = $albumId ? ['album' => $albumId] : [];

        $model = Photoalbum::find()->where(['id' => $albumId])->one();
        $photos = Photo::getPhotos($where);

        /* If it's an Intercooler request, also sending headers for photoalbum view event to fire */
        if (Yii::$app->request->post('ic-request')) {
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"photoalbumView":'.Json::encode($where).'}');
        }

        return $this->renderPartial('view', [
            'model' => $model,
            'photos' => $photos,
            'album_id' => $albumId, /* Photoalbum ID */
            'album_name' => $albumName, /* Photoalbum title */
        ]);
    }

    /**
     * Edit photoalbum photos and photoalbum
     * TODO: Do not return mixed data!
     * @return HTML|JSON
     */
    public function actionEdit()
    {
        $photoalbum = Yii::$app->request->post('Photoalbum');
        $albumId = $photoalbum['id'];

        $photoalbum = Photoalbum::find()->where([
          'id' => $albumId,
          'user' => Yii::$app->user->id
        ])->one();

        if (!$photoalbum) {
            throw new HttpException(400, Yii::t('err', 'Photoalbum not found'));
        }

        if ($photoalbum->load($_POST) && $photoalbum->validate()) {
            $photoalbum->save();
            return $this->renderPartial('index', [
                'photoalbums' => [$photoalbum]
            ]);
        } else {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $photoalbum->errors;
        }
    }

    /**
     * Creates a new Photoalbum model.
     * Renders: new photoalbum
     * TODO: Do not return mixed data!
     * @return HTML|JSON
     */
    public function actionCreate()
    {
        $photoalbum = new Photoalbum();

        if ($photoalbum->load($_POST) && $photoalbum->validate()) {
            $photoalbum->save();
            return $this->renderPartial('index', [
                'photoalbums' => [$photoalbum]
            ]);
        } else {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $photoalbum->errors;
        }
    }

    /**
     * Deletes photoalbum from DB.
     * @return json
     */
    public function actionDelete()
    {
        $albumId = (int)Yii::$app->request->post('album');
        $userId = Yii::$app->user->id;

        if (!is_int($albumId)) {
            throw new HttpException(400, Yii::t('err', 'Album ID must be a number'));
        }

        if (Photoalbum::deleteOneByUserId($userId, $albumId)) {
            Photo::deleteAllPhotosByPhotoalbumId($albumId);

            /* If it's an Intercooler request, also sending headers for photoalbum open event to fire */
            if (Yii::$app->request->post('ic-request')) {
                $headers = Yii::$app->response->headers;
                $headers->add('X-IC-Trigger', '{"photoalbumDelete":['.$albumId.']}');
            }
        }
    }
}
