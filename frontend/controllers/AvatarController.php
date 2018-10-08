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

use frontend\models\UserAvatar;
use frontend\models\UserDescription;
use frontend\components\OutstyleSocialController;

/**
 * Avatar controller: for working with user avatars
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class AvatarController extends OutstyleSocialController
{
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
                        'roles' => ['?'], /* Allow guests for API actions */
                        'actions' => [
                          'upload'
                        ],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'], /* Allow registered */
                        'actions' => [
                          'upload'
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * [API] Avatar uploading
     * For `$this->userId` see parent controller
     * @return int|JSON
     */
    public function actionUpload()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $avatar = new UserAvatar();
        $userDescripion = new UserDescription();

        $user = $userDescripion->findOne($this->userId);

        if (!$user) {
            throw new HttpException(401, Yii::t('err', 'User not authorized!'));
        }

        # Checking image file headers before image processing (resize and stuff)
        $imageFile = UploadedFile::getInstance($avatar, 'img');

        if (isset($imageFile->tempName) && !exif_imagetype($imageFile->tempName)) {
            throw new HttpException(400, Yii::t('err', 'Image type is not valid!'));
        }

        # If basic validation is passed, assigning image for rules() 'img' filter
        $avatar->img = $imageFile;
        $avatar->user = $user->id;
        $avatar->type = $avatar::PHOTO_TYPE_AVATAR;

        if ($avatar->validate()) {
            $avatar->save();
            $user->avatar = $avatar->getPrimaryKey();
            $user->update();
            return (int)$user->avatar;
        } else {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $avatar->errors;
        }
    }
}
