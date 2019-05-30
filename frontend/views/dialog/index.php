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

echo Html::beginTag('div', ['id' => 'dialogsList']);
    echo Html::beginTag('div', ['class' => 'dialogs__list noselect o-grid o-grid--wrap o-grid--no-gutter']);

        /* ! If user has no dialogues created yet... */
        if (empty($dialogs)) {
            echo Html::tag('div',
                '<div class="u-center-block__content"><i class="zmdi zmdi-alert-circle-o zmdi-hc-3x"></i><br>'.
                    \Yii::t('app', 'No dialogs...').
                '</div>',
            [
                'class' => 'u-center-block u-c dialogs__new'
            ]);
        } else {
            foreach ($dialogs as $dialog) {
                $dialogId = (int)$dialog['dialog']['id'];
            /* ! Dialog box */
            echo Html::beginTag('div', [
                'id' => 'dialogbox-'.$dialogId,
                'class' => 'o-grid__cell--width-100 u-window-box--xsmall dialog__box dialog__box--hoverable',
                'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
                'ic-target' => '#messages_area',
                'ic-post-to' => Url::toRoute(['messages/'.$dialogId]),
                'ic-push-url' => 'true',
                'ic-select-from-response' => '#messages_area'
            ]);

            /* ! Dialog image */
            /* NOTE: If DialogAvatar is about to be redone, do it in `Dialog` model -> setupData() */
            $dialogImage = ArrayHelper::getValue($dialog, 'dialog.members.0.userDescription.userAvatar.path');
                echo Html::img($dialogImage, [
                'class' => 'o-image roundborder friend__avatar friend__avatar--small dialog__avatar dialog__avatar--small'
            ]);

            /* ! Dialog members counter for conversations (more than 2 people) */
            if (isset($dialog['dialog']['membersCount'])) {
                if ($dialog['dialog']['membersCount'] > 2) {
                    echo Html::tag('div', '<i class="zmdi zmdi-account"></i>&nbsp;'.$dialog['dialog']['membersCount'],
                    [
                        'class' => 'c-badge c-badge--info c-badge--bottomleft dialog__memberscount'
                    ]);
                }
            }

            /* ! Dialog info */
            $dialogLastMessage = (!empty($dialog['message']['last'])) ? $dialog['message']['last'] : Yii::t('app', 'No messages');
                $dialogLastMessageTimestamp = $dialog['message']['time'];
                $dialogName = (!empty($dialog['dialog']['name'])) ? $dialog['dialog']['name'] : Yii::t('app', 'Unnamed');
                echo Html::tag('div',
                '<div class="dialog__name" title="'.$dialogName.'"><span class="dialog__name--dynamic">'.StringHelper::cutString(htmlspecialchars_decode($dialogName), $stringLimit = 23, $preciseCut = true).'</span><span class="dialog__time">'.StringHelper::convertTimestampToHuman($dialogLastMessageTimestamp, 'd M').'</span></div>'.
                '<div class="dialog__lastmessage"><i class="zmdi zmdi-comment-outline zmdi-hc-lg"></i>&nbsp;'.StringHelper::cutString($dialogLastMessage, $stringLimit = 60).'</div>',
            [
                'class' => 'o-grid__cell--width-100 u-letter-box--medium dialog__info'
            ]);

                echo Html::endTag('div');
            }
        }

    echo Html::endTag('div');
echo Html::endTag('div');
