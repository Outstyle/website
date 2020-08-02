<?php

/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

namespace frontend\components;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

use frontend\models\User;
use frontend\models\Friend;
use frontend\models\Board;
use frontend\models\MessageStatus;

use common\components\helpers\ElementsHelper;

/**
 * OutstylePortalController: serves as a parent controller for all other portal related controllers
 * Every other controller should extend this one
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class OutstylePortalController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        /* For AJAXed requests we skip saving */
        if (!Yii::$app->request->isAjax) {
            return;
        }

        /* TODO: Make local 5 minutes checktime to prevent DB trigerring every time on page refresh,
        move to User model */
        if (Yii::$app->user->id) {
            $userOnline = User::findOne(Yii::$app->user->id);
            $userOnline->lastvisit = time();
            $userOnline->save();
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($event)
    {
        if (!parent::beforeAction($event)) {
            return false;
        }

        $this->layout = 'portal';
        $this->view->params['controller']['id'] = Yii::$app->controller->id;

        /* Final action to bypass vars to JS */
        //$this->setGlobalJsParams();

        return parent::beforeAction($event);
    }

    /**
     * Setting up JS global data for clientside scripts to access
     */
    protected function setGlobalJsParams()
    {
        $this->view->registerJs(
            'var OUTSTYLE_GLOBALS = ' . Json::encode($this->view->params),
            \yii\web\View::POS_HEAD
        );
    }
}
