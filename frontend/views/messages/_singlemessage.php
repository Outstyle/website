<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

use yii\widgets\Spaceless;
use common\components\helpers\StringHelper;

/**
 * Single user message representation
 *
 * @var $this                       yii\web\View
 * @var $messages                   common\models\Message
 * @var $dialogMembers              common\models\DialogMembers
 *
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
**/

foreach ($messages as $message) {
    $messageId = $message['id'];
    $senderId = $message['sender_id'];
    $messageText = $message['message'];
    $messageTime = StringHelper::convertTimestampToHuman(strtotime($message['created']), 'H:i');
    $senderAvatar = $dialogMembers[$senderId]['userDescription']['userAvatar']['path'];
    $senderName = $dialogMembers[$senderId]['userDescription']['nickname'];

    /* --- SINGLE MESSAGE --- */
    echo Html::tag('li',
        Html::img($senderAvatar, [
            'class' => 'o-image roundborder friend__avatar friend__avatar--mini chat-thread__avatar'
        ]).

        Html::tag('div',
            '<span class="chat-thread-message__sender">'.$senderName.'</span>&nbsp;',
        [
            'class' => 'chat-thread-message__header clearfix'
        ]).

        $messageText.

        Html::tag('div',
            '<span class="chat-thread-message__time">'.$messageTime.'</span>',
        [
            'class' => 'chat-thread-message__footer clearfix'
        ]),

    [
        'id' => 'm'.$messageId,
        'class' => 'chat-thread-message',
    ]);
}
