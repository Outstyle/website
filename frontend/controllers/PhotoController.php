<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\HttpException;

use yii\filters\AccessControl;
use yii\helpers\Json;

use frontend\models\Photo;
use frontend\models\Photoalbum;

use frontend\components\OutstyleSocialController;

/**
 * Photo controller: for working with user photos
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class PhotoController extends OutstyleSocialController
{
    /**
     * Layout to be used for all the actions
     * @var string|false
     */
    public $layout = 'social';

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
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['*'],
                    ],
                ],
            ],
        ];
    }

    /**
     * User photos page
     * @param  integer $userId Logged user ID (Default: 0)
     * @return array
     */
    public function actionIndex(int $userId = 0)
    {
        $photos = Photo::getPhotos();
        $photoalbums = Photoalbum::getPhotoalbums();

        return $this->render('index', [
           'photos' => $photos,
           'photoalbums' => $photoalbums
        ]);
    }

    /**
     * Single photo view
     *
     * @param  string $photoId
     * @return array
     */
    public function actionView(int $photoId)
    {
        $photo = Photo::getById($photoId);

        if (!$photo) {
            throw new HttpException(400, Yii::t('err', 'Photo not found'));
        }

        return $this->render('view', [
            'photo' => $photo
        ]);
    }

    /**
     * [API] Get photos
     *
     * @param  string $photoId
     * @return array
     */
    public function actionGet()
    {
        $data = Yii::$app->request->get();
        $where = $response = [];

        /* Initial page to start load photos from */
        $page = (!empty($data['page'])) ? (int) $data['page'] : 1;
        $albumId = (!empty($data['album_id'])) ? (int) $data['album_id'] : 0;

        $where['album'] = $albumId;
        $photos = Photo::getPhotos($where, $page);

        /* If we don't have our model filled, that means we won't send another request, cause we're reached the end of photos */
        if (!$photos) {
            $response['lastPageReached'] = 1;
            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"photoalbumPhotosLoadMore":['.Json::encode($response).']}');
            return;
        }

        /*
         * http://intercoolerjs.org/docs.html
         * Intercooler headers to trigger certain events
         *
         * Rendering as HTML code and rendering only partial view to avoid all page refresh
         */
        if (Yii::$app->request->get('ic-request')) {
            $page++; // Let's add +1 to our page int, so rendered part would know from where to start
            $response['page'] = $page;

            $headers = Yii::$app->response->headers;
            $headers->add('X-IC-Trigger', '{"photoalbumPhotosLoadMore":['.Json::encode($response).']}');

            return $this->renderPartial('_photoloadmore', [
                'photos' => $photos,
                'page' => (int)$page,
                'album_id' => (int)$albumId,
            ]);
        }
    }

    /**
     * [API] Photos uploading
     * @return null|JSON
     */
    public function actionUpload()
    {
        $this->layout = false;

        $photo = new Photo();
        $photo->album = (int)Yii::$app->request->post('album_id');

        # Checking image file headers before image processing (resize and stuff)
        $imageFile = UploadedFile::getInstance($photo, 'img');

        if (isset($imageFile->tempName) && !exif_imagetype($imageFile->tempName)) {
            throw new HttpException(400, Yii::t('err', 'Image type is not valid!'));
        }

        # If basic validation is passed, assigning image for rules() 'img' filter
        $photo->img = $imageFile;

        if ($photo->validate()) {
            $photo->save();

            # Setting cover for photoalbum as a last added photo
            # NOTE: Possible DB queue overhaul, since cover will be updated on each photo uploaded
            # NOTE: Move to Photoalbum as a separate method if some more actions added?
            $photoalbum = Photoalbum::findOne([
              'id' => $photo->album,
              'user' => Yii::$app->user->id
            ]);
            if ($photoalbum) {
                $photoalbum->cover = $photo->id;
                $photoalbum->update();
            }
        } else {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $photo->errors;
        }
    }
}
