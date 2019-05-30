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

        $this->boardOwnerUserId = Yii::$app->getRequest()->getQueryParam('userId') ?? Yii::$app->user->id ?? 0;

        /* For AJAXed requests we skip saving */
        if (!Yii::$app->request->isAjax) {
            return;
        }
        /* x TODO: Make local 5 minutes checktime to prevent DB trigerring every time on page refresh, move to User model */
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

        $this->layout = 'social';
        $this->userId = Yii::$app->user->id ?? 0;

        if (!$this->userId) {
            throw new HttpException(401, Yii::t('err', 'User is not logged in'));
        }

        /**
        * Working with _csrf tokens
        *
        * Here we are comparing our tokens from IC requests with $_POST ones
        * Since we don't want direct access to content, we should perform token check every time we access the controller
        *
        * If we want to allow direct access to certain 'entry points', we should rely on paths and generate fresh token
        * @see: $allowedEntryPoints
        *
        **/

        /* TODO Move this code to a more appropriate placeholder */
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
            return parent::beforeAction($event);
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
        $this->setUserMessages($userId = Yii::$app->user->id, $relation = Board::BOARD_STATE_OWNER);

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
        /* ! Active user friends, who are actually in a friendship with this user */
        $friends = Friend::getUserFriends([Friend::FRIENDSHIP_STATUS_ACTIVE, Friend::FRIENDSHIP_STATUS_ONESIDED], $userId)
          ->limit(Friend::$friendsPageSize)
          ->asArray()
          ->all();
        $friends = $activeFriends = Friend::createFriendsArrayForUser($friends);
        $friends = Friend::getFriendsDescription($friends);

        /* ! Friends who are not yet confirmed friendship status */
        $friendsPending = Friend::getUserFriends(Friend::FRIENDSHIP_STATUS_PENDING, $userId)
          ->limit(Friend::$friendsPendingPageSize)
          ->asArray()
          ->all();
        $friendsPending = $pendingFriends = Friend::createFriendsArrayForUser($friendsPending, $userId);
        $friendsPending = Friend::getFriendsDescription($friendsPending, $userId);

        /* ! Friends who are online and are in a friendship */
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
        $this->userGlobalData[$relation]['friends']['selected'] = []; /* When selecting friend (i.e. to add in dialog) */

        /* Passing parameters to any child view */
        $this->view->params[$relation]['friends']['active'] = $activeFriends;
        $this->view->params[$relation]['friends']['online'] = $activeOnlineFriends;
        $this->view->params[$relation]['friends']['pending'] = $pendingFriends;
        $this->view->params[$relation]['friends']['selected'] = [];

        $this->view->params[$relation]['friends']['count']['active'] = count($friends);
        $this->view->params[$relation]['friends']['count']['pending'] = count($friendsPending);
        $this->view->params[$relation]['friends']['count']['online'] = count($friendsOnline);
        $this->view->params[$relation]['friends']['count']['selected'] = 0;
    }

    protected function setUserMessages($userId = 0, $relation = Board::BOARD_STATE_OWNER)
    {
        $messages = MessageStatus::getUnread(0, $userId)
            ->limit(MessageStatus::$messagesUnreadLimit)
            ->select('message_id, dialog')
            ->asArray()
            ->all();

        $messagesArray = MessageStatus::createMessagesArrayForUser($messages, $userId);

        /* Passing parameters to any child view */
        $this->view->params[$relation]['messages']['unread'] = $messagesArray;
        $this->view->params[$relation]['messages']['count']['unread'] = count($messages);
    }

    /**
     * Setting up JS global data for clientside scripts to access
     */
    protected function setGlobalJsParams()
    {
        $this->view->registerJs(
          'var OUTSTYLE_GLOBALS = '.Json::encode($this->view->params), \yii\web\View::POS_HEAD);
    }
}
