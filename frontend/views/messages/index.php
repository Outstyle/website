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

    /* USER CONVERSATIONS SIDEBAR - 35% */
    Html::tag('div',

        Html::tag('div', '',
        [
            'id' => 'conversations__loadmore',
            'class' => 'album_area--loader loader--smallest',
            'ic-post-to' => Url::toRoute(['api/dialog/list']),
            'ic-trigger-on' => 'scrolled-into-view',
            'ic-target' => '#conversations_area',
            'ic-push-url' => 'false'
        ]).

        Html::tag('div', '', ['id' => 'conversations_area']),

    [
        'id' => 'dialogs_area',
        'class' => 'o-grid__cell o-grid__cell--width-35 conversations__list'
    ]).

    /* USER CHAT MESSAGES AREA - 65% */
    Html::tag('div',
        $this->render('view', [
            'messages' => $messages,
            'dialogMembers' => $dialogMembers,
            'dialog' => $dialog,
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
        'ic-push-url' => 'false',
    ]).

    /* Send message form */
    Html::tag('div',
        $this->render('_form', [
            'dialogId' => $dialogId,
        ]),
    [
        'id' => 'messages_sendbox',
        'class' => 'o-grid__cell o-grid__cell--width-100 messages__sendbox'
    ]),

    ['class' => 'messages__container']
);
