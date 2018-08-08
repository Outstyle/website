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

use app\models\User;
use app\models\Friend;

use common\components\helpers\ElementsHelper;

/**
 * OutstyleSocialController: serves as a parent controller for all other social related controllers
 * Every other controller should extend this one
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class OutstyleSocialController extends Controller
{
    /**
     * Routes that allow direct access without csrf token
     * @var array
     */
    private $_allowedEntryPoints = [
      'index',
      'view'
    ];

    /**
     * An array, used to store GLOBAL social data, that is need to be used everywhere inside social layout
     * @var array
     */
    protected $userGlobalData = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        /* TODO: Make local 5 minutes checktime to prevent DB trigerring every time on page refresh */
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
        $this->layout = 'social';

        /**
        * Check if it's an Intercooler request, and if so - using ajaxed layout
        **/

        if (Yii::$app->request->get('ic-request') == true || Yii::$app->request->post('ic-request') == true) {
            $this->layout = 'ajax/social';
        }

        /**
        * Working with _csrf tokens
        * TODO: Move this code to a more appropriate placeholder
        *
        * Here we are comparing our tokens from IC requests with $_POST ones
        * Since we don't want direct access to content, we should perform token check every time we access the controller
        *
        * If we want to allow direct access to certain 'entry points', we should rely on paths and generate fresh token
        * @see: $allowedEntryPoints
        *
        **/
        $csrf_token = Yii::$app->request->headers->get('x-csrf-token');
        $user_token = ElementsHelper::getCSRFToken();

        if ($event->controller->id == Yii::$app->controller->id && in_array($event->id, $this->_allowedEntryPoints)) {
            if (!$csrf_token) {
                $csrf_token = Yii::$app->request->csrfToken;
            }
        }

        if (!$user_token) {
            throw new HttpException(400, Yii::t('err', 'Token empty!'));
        }
        if ($user_token != $csrf_token) {
            throw new HttpException(400, Yii::t('err', 'Token is invalid!'));
        }

        /* Callable methods on each controller action triggered */
        $this->setUserFriends();


        /* Final action to bypass vars to JS */
        $this->setGlobalJsParams();

        return parent::beforeAction($event);
    }

    /**
     * Setting up user friends array for global usage
     * You can also use 'Yii::$app->request->pathInfo == 'friends/online' check i.e. to isolate certain actions
     */
    protected function setUserFriends()
    {
        $friends = Friend::getUserFriends(Friend::STATUS_ACTIVE_FRIENDSHIP)
          ->limit(Friend::$friendsPageSize)
          ->asArray()
          ->all();
        $friends = Friend::createFriendsArrayForUser($friends);
        $friends = Friend::getFriendsDescription($friends);

        $friendsPending = Friend::getUserFriends(Friend::STATUS_ACTIVE_PENDING)
          ->limit(Friend::$friendsPendingPageSize)
          ->asArray()
          ->all();
        $friendsPending = Friend::createFriendsArrayForUser($friendsPending);
        $friendsPending = Friend::getFriendsDescription($friendsPending);

        $friendsOnline = Friend::getUserFriendsOnline()
          ->limit(Friend::$friendsPageSize)
          ->asArray()
          ->all();
        $friendsOnline = Friend::createFriendsArrayForUser($friendsOnline);
        $friendsOnline = Friend::getFriendsDescription($friendsOnline);

        /* Setting global var for child controllers */
        $this->userGlobalData['friends']['active'] = $friends;
        $this->userGlobalData['friends']['pending'] = $friendsPending;
        $this->userGlobalData['friends']['online'] = $friendsOnline;

        /* Passing parameters to any child view */
        $this->view->params['friends']['active'] = count($friends);
        $this->view->params['friends']['pending'] = count($friendsPending);
        $this->view->params['friends']['online'] = count($friendsOnline);
    }

    /**
     * Setting up JS global data for clientside scripts to access
     */
    protected function setGlobalJsParams()
    {
        $this->view->registerJs(
          'var outstyle_globals = '.Json::encode($this->view->params), \yii\web\View::POS_HEAD);
    }
}
