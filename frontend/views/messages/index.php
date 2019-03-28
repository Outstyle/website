<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\ElementsHelper;
use common\components\helpers\SEOHelper;
use common\components\helpers\html\LoadersHelper;

/**
 * User messages page
 * This page is an entry point and can have seo meta tags
 *
 * @var $this                       yii\web\View
 * @var $messages                   common\models\Message
 * @var $dialogId                   common\models\Dialog
 * @var $dialogMembers              common\models\DialogMembers
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
**/

SEOHelper::setMetaInfo($this);

echo ElementsHelper::ajaxGridWrap(Yii::$app->controller->id, 'o-grid--no-gutter',

    /* ! --- USER CONVERSATIONS SIDEBAR - 35% --- */
    Html::tag('div',

            /* ! --- CONVERSATIONS SEARCHBOX --- */
            $this->render('../dialog/search/_dialogsform').

            /* ! --- USERS SEARCHBOX --- */
            $this->render('../dialog/search/_usersform').


        /**
         * DIALOGS LOADER (API CALL)
         * Loader element, when shown, loads data into #conversations_area
         * @see http://intercoolerjs.org/attributes/ic-post-to.html
         */
        Html::tag('div',
            Html::tag('div', '',
            [
                'id' => 'conversations__loadmore',
                'class' => 'loader--smallest',
                'ic-post-to' => Url::toRoute(['api/dialog/list']),
                'ic-trigger-on' => 'scrolled-into-view',
                'ic-target' => '#conversations_area',
                'ic-push-url' => 'false'
            ]),
        [
            'id' => 'conversations_area',
            'class' => 'noselect',
            'data-switch-layout' => 'friends_dialogs'
        ]).

        /**
         * FRIENDS IN DIALOGS LOADER (API CALL)
         * Loader element, when shown, loads data into #friends_in_dialogs_area
         * This request is needed to fetch active friends of user and list them
         * User then can add any user into dialog or initiate a new one
         * @see http://intercoolerjs.org/attributes/ic-post-to.html
         */
        Html::tag('div',
            Html::tag('div', '',
            [
                'id' => 'friends__loadonce',
                'class' => 'loader--smallest',
                'ic-post-to' => Url::toRoute(['api/friends/filter']),
                'ic-trigger-on' => 'once',
                'ic-target' => '#friends_in_dialogs_content',
                'ic-indicator' => '#friends__loadonce',
                'ic-select-from-response' => '#friendsList',
                'ic-push-url' => 'false',
                'ic-on-success' => "jQuery('#friends__loadonce').remove()"
            ]).
            Html::tag('div', '', [
                'id' => 'friends_in_dialogs_content'
            ]),
        [
            'id' => 'friends_in_dialogs_area',
            'class' => 'noselect u-i',
            'data-switch-layout' => 'friends_dialogs'
        ]),

    [
        'id' => 'dialogs_area',
        'class' => 'o-grid__cell o-grid__cell--width-35'
    ]).

    /* ! --- USER CHAT MESSAGES AREA - 65% --- */
    Html::tag('div',
        $this->render('view', [
            'messages' => $messages,
            'dialogMembers' => $dialogMembers,
            'dialog' => $dialog,
            'dialogId' => $dialogId,
            'options' => [
                'showHeader' => true,
            ]
        ]),
    [
        'id' => 'messages_area',
        'class' => 'o-grid__cell o-grid__cell--width-65 messages__list',
        'ic-append-from' => Url::toRoute(['api/messages/get']),
        'ic-poll' => '5s',
        'ic-target' => '.chat-thread',
        'ic-select-from-response' => '#content',
        'ic-trigger-from' => '.message-last',
        'ic-push-url' => 'false',
    ]).

    /* ! --- MESSAGES BOTTOM PANE --- */
    Html::tag('div',

        /* ! SEND MESSAGE FORM */
        $this->render('_form', ['dialogId' => $dialogId]).

        /* ! CREATE NEW DIALOG */
        $this->render('../dialog/_createnew'),

    [
        'id' => 'messages_bottompanel',
        'class' => 'o-grid__cell o-grid__cell--width-100 u-i'
    ]),

    ['class' => 'messages__container']
);
