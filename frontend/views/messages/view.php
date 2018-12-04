<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\widgets\Spaceless;

use common\components\helpers\ElementsHelper;
use common\components\helpers\StringHelper;
use frontend\models\UserNickname;

/**
 * User messages list
 *
 * @var $this                     yii\web\View
 * @var $messages                 common\models\Message
 * @var $dialogMembers            common\models\DialogMembers
*/

Spaceless::begin();

if (isset($showHeader)) {
}

/* --- MESSAGES LIST --- */
echo Html::beginTag('div', ['id' => 'messages_list']);

    /* --- NO MESSAGES OR MAIN MESSAGES PAGE --- */
    if (!$messages) {
        echo Html::tag('div',
            '<div class="u-center-block__content"><i class="zmdi zmdi-comment-more zmdi-hc-5x"></i><br>'.
                \Yii::t('app', 'Please choose a dialogue or {dialogue_action_new}', [
                'dialogue_action_new' => 'создайте новый',
                ]).
            '</div>',
        [
            'class' => 'u-center-block u-c conversations__new'
        ]);
    }

    if ($messages) {
        echo Html::beginTag('ul', [
            'class' => 'chat-thread'
        ]);
        foreach ($messages as $messageId => $message) {
            $senderId = $message['sender_id'];
            $senderAvatar = $dialogMembers[$senderId]['userDescription']['userAvatar']['path'];
            $senderName = $dialogMembers[$senderId]['userDescription']['nickname'];

            /* Message output */
            echo Html::tag('li',
                Html::img($senderAvatar, [
                    'class' => 'o-image roundborder friend__avatar friend__avatar--mini chat-thread__avatar'
                ]).

                Html::tag('div',
                    '<span class="chat-thread-message__sender">'.$senderName.'</span>&nbsp;',
                [
                    'class' => 'chat-thread-message__header clearfix'
                ]).

                $message['message'].

                Html::tag('div',
                    '<span class="chat-thread-message__time">'.StringHelper::convertTimestampToHuman(strtotime($message['created']), 'H:i').'</span>',
                [
                    'class' => 'chat-thread-message__header clearfix'
                ]),

            [
                'class' => 'chat-thread-message'
            ]);
        }
        echo Html::endTag('ul');
    }


    /* Load more messages_list
    echo ElementsHelper::loadMore(Url::toRoute('api/photo/get'), '.photoalbum__photos', '{"album_id":'.(int)Yii::$app->request->post('album_id').'}');
    */

echo Html::endTag('div');
d($messages);
Spaceless::end();
