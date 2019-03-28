<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\widgets\Spaceless;

use common\components\helpers\ElementsHelper;
use common\components\helpers\StringHelper;

/**
 * User messages list
 *
 * @var $this                     yii\web\View
 * @var $messages                 common\models\Message
 * @var $dialog                   common\models\Dialog
 * @var $dialogId                 common\models\Dialog
 * @var $dialogMembers            common\models\DialogMembers
*/



/* ! --- MESSAGES HEADER --- */
if (isset($options['showHeader'])) {
    echo Html::beginTag('div', ['id' => 'messages_header']);
    echo Html::endTag('div');
}

/* ! --- MESSAGES LIST --- */
echo Html::beginTag('div', ['id' => 'messages_list']);

    /* REDO: Move this. --- NO MESSAGES AT ALL - MEANING WE'RE ON MESSAGES MAIN PAGE --- */
    if ($messages === 0) {
        echo Html::tag('div',
            '<div class="u-center-block__content"><i class="zmdi zmdi-comment-more zmdi-hc-5x"></i><br>'.
                \Yii::t('app', 'Please choose a dialogue or {dialogue_action_new}', [
                'dialogue_action_new' => 'создайте новый',
                ]).
            '</div>',
        [
            'class' => 'u-center-block u-c conversations__new'
        ]);
    } else {
        /* --- NO MESSAGES IN THIS DIALOG --- */
        if (!$messages) {
            echo Html::tag('div',
                '<div class="u-center-block__content"><i class="zmdi zmdi-collection-text zmdi-hc-5x"></i><br>'.
                    \Yii::t('app', 'No messages has been written yet... Be first and say something!').
                '</div>',
            [
                'class' => 'u-center-block u-c conversations__new'
            ]);
        }

        if ($messages) {
            echo Html::beginTag('ul', [
                'class' => 'chat-thread'
            ]).

            $this->render('_singlemessage', [
                'messages' => $messages,
                'dialogMembers' => $dialogMembers
            ]).

            Html::endTag('ul');
        }
    }

echo Html::endTag('div');
