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

use frontend\models\User;
use frontend\models\Friend;
use frontend\models\Board;

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
     * Current user ID, including other viewable board of other user (not a board owner)
     * @var int
     */
    protected $boardOwnerUserId = 0;

    /**
     * Current board state for user: whos board is this?
     * @var string    Default: owner
     */
    protected $boardOwnerRelation = Board::BOARD_STATE_OWNER;

    /**
     * Current user ID - always an owner itself
     * @var int
     */
    protected $userId = 0;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        /* TODO: Make local 5 minutes checktime to prevent DB trigerring every time on page refresh, move to User model */
        if (Yii::$app->user->id) {
            $userOnline = User::findOne(Yii::$app->user->id);
            $userOnline->lastvisit = time();
            $userOnline->save();
        }

        $this->boardOwnerUserId = Yii::$app->getRequest()->getQueryParam('userId') ?? Yii::$app->user->id ?? 0;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($event)
    {
        $this->layout = 'social';
        $this->userId = Yii::$app->user->id ?? 0;

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
        $allowed_token = in_array(Yii::$app->request->post('token'), Yii::$app->params['AllowedTokens']);

        /**
         * If it's an allowed source or we have static token, sent with $_POST, ignoring _csrf check
         */

        if ($allowed_token) {
            $this->enableCsrfValidation = false;
            $this->layout = false;
            $this->userId = (int)Yii::$app->request->post('user') ?? Yii::$app->user->id ?? 0;
        } else {
            if ($event->controller->id == Yii::$app->controller->id && in_array($event->id, $this->_allowedEntryPoints)) {
                if (!$csrf_token) {
                    $csrf_token = Yii::$app->request->csrfToken;
                }
            }

            if (!$user_token) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                throw new HttpException(401, Yii::t('err', 'Token empty!'));
            }
            if ($user_token != $csrf_token) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                throw new HttpException(401, Yii::t('err', 'Token is invalid!'));
            }
        }

        /* Callable methods on each controller action triggered */
        $this->setUserFriends($userId = Yii::$app->user->id, $relation = Board::BOARD_STATE_OWNER);

        /* Viewing another user's board? */
        if ($this->boardOwnerUserId !== Yii::$app->user->id) {
            $this->boardOwnerRelation = Board::BOARD_STATE_OTHER;
            $this->setUserFriends($userId = $this->boardOwnerUserId, $relation = $this->boardOwnerRelation);
        }


        /* Final action to bypass vars to JS */
        $this->setGlobalJsParams();

        return parent::beforeAction($event);
    }

    /**
     * Setting up user friends array for global usage
     * You can also use 'Yii::$app->request->pathInfo == 'friends/online' check i.e. to isolate certain actions
     * @param integer $userId
     * @param integer $relation   How this userdata is related to the user itself? `owner` - self data, `other` - other user data
     *
     * The `$relation` variable is needed, when you working with boards and viewing own board or another user board
     */
    protected function setUserFriends($userId = 0, $relation = Board::BOARD_STATE_OWNER)
    {
        $friends = Friend::getUserFriends([Friend::FRIENDSHIP_STATUS_ACTIVE, Friend::FRIENDSHIP_STATUS_ONESIDED], $userId)
          ->limit(Friend::$friendsPageSize)
          ->asArray()
          ->all();
        $friends = $activeFriends = Friend::createFriendsArrayForUser($friends);
        $friends = Friend::getFriendsDescription($friends);

        $friendsPending = Friend::getUserFriends(Friend::FRIENDSHIP_STATUS_PENDING, $userId)
          ->limit(Friend::$friendsPendingPageSize)
          ->asArray()
          ->all();
        $friendsPending = $pendingFriends = Friend::createFriendsArrayForUser($friendsPending, $userId);
        $friendsPending = Friend::getFriendsDescription($friendsPending, $userId);

        $friendsOnline = Friend::getUserFriendsOnline($userId)
          ->limit(Friend::$friendsPageSize)
          ->asArray()
          ->all();
        $friendsOnline = $activeOnlineFriends = Friend::createFriendsArrayForUser($friendsOnline, $userId);
        $friendsOnline = Friend::getFriendsDescription($friendsOnline, $userId);

        /* Setting global var for child controllers */
        $this->userGlobalData[$relation]['friends']['active'] = $friends;
        $this->userGlobalData[$relation]['friends']['pending'] = $friendsPending;
        $this->userGlobalData[$relation]['friends']['online'] = $friendsOnline;

        /* Passing parameters to any child view */
        $this->view->params[$relation]['friends']['active'] = $activeFriends;
        $this->view->params[$relation]['friends']['online'] = $activeOnlineFriends;
        $this->view->params[$relation]['friends']['pending'] = $pendingFriends;

        $this->view->params[$relation]['friends']['count']['active'] = count($friends);
        $this->view->params[$relation]['friends']['count']['pending'] = count($friendsPending);
        $this->view->params[$relation]['friends']['count']['online'] = count($friendsOnline);
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
