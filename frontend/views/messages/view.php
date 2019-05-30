<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\widgets\Spaceless;

/**
 * User messages list
 *
 * @var $this                     yii\web\View
 * @var $messages                 common\models\Message
 * @var $dialog                   common\models\Dialog
 * @var $dialogId                 common\models\Dialog
 * @var $dialogMembers            common\models\DialogMembers
*/

/* ! --- NO MESSAGES AT ALL --- */
if ($messages === 0) {
    echo $this->render('_nomessages');
    return;
}

/* ! --- MESSAGES HEADER --- */
if (isset($options['showHeader'])) {
    echo Html::beginTag('div', [
        'id' => 'messages_header',
        'class' => 'u-window-box--medium',
    ]).

        $this->render('../dialog/_dialogoptions', [
            'dialog' => $dialog,
            'dialogMembers' => $dialogMembers
        ]).

    Html::endTag('div');
}

/* ! --- MESSAGES LIST --- */
echo Html::beginTag('div', ['id' => 'messages_list']);


        /* ! Messages thread */
        echo Html::beginTag('ul', [
            'class' => 'chat-thread'
        ]);

        if ($messages) {
            echo $this->render('_singlemessage', [
                'messages' => $messages,
                'dialogMembers' => $dialogMembers
            ]);
        } else {
            /* ! No messages in this dialog */
            echo Html::tag('div',
                '<div class="u-center-block__content"><i class="zmdi zmdi-collection-text zmdi-hc-5x"></i><br>'.
                    \Yii::t('app', 'No messages has been written yet... Be first and say something!').
                '</div>',
            [
                'class' => 'u-center-block u-c conversations__new'
            ]);
        }

        echo Html::endTag('ul');


echo Html::endTag('div');
