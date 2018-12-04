<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Spaceless;

use common\components\helpers\StringHelper;
use common\components\helpers\ElementsHelper;

/**
 * User dialogues list
 *
 * @var $this                    yii\web\View
 * @var $dialogs                 @frontend/models/Dialog
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
**/

Spaceless::begin();
echo Html::beginTag('div', ['id' => 'ajax']);

    echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter']);

        foreach ($dialogs as $dialog) {

            /* Dialog single row */
            echo Html::beginTag('div', [
                'id' => 'dialogbox-'.$dialog['dialog']['id'],
                'class' => 'o-grid__cell--width-100 u-window-box--xsmall dialog__box dialog__box--hoverable',
                'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
                'ic-target' => '#messages_area',
                'ic-post-to' => Url::toRoute(['messages/'.$dialog['dialog']['id']]),
                'ic-push-url' => 'true',
                'ic-select-from-response' => '#messages_area'
            ]);

            /* Dialog image. NOTE: If DialogAvatar is about to be redone, do it in `Dialog` model -> setupData() */
            $dialogImage = ArrayHelper::getValue($dialog, 'dialog.members.1.userDescription.userAvatar.path');
            echo Html::img($dialogImage, [
                'class' => 'o-image roundborder friend__avatar friend__avatar--small dialog__avatar dialog__avatar--small'
            ]);

            /* Dialog info */
            $dialogLastMessage = (!empty($dialog['message']['last'])) ? $dialog['message']['last'] : Yii::t('app', 'No messages');
            $dialogLastMessageTimestamp = $dialog['message']['time'];
            $dialogName = (!empty($dialog['dialog']['name'])) ? $dialog['dialog']['name'] : Yii::t('app', 'Unnamed');
            echo Html::tag('div',
                '<div class="dialog__name">'.StringHelper::cutString($dialogName, 35, true).'<span class="dialog__time">'.StringHelper::convertTimestampToHuman($dialogLastMessageTimestamp, 'd M').'</span></div>'.
                '<div class="dialog__lastmessage"><i class="zmdi zmdi-comment-outline zmdi-hc-lg"></i>&nbsp;'.StringHelper::cutString($dialogLastMessage, 55).'</div>',
            [
                'class' => 'o-grid__cell--width-100 u-letter-box--medium'
            ]);

            echo Html::endTag('div');
        }

    echo Html::endTag('div');

echo Html::endTag('div');
Spaceless::end();
