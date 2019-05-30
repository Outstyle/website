<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use common\components\helpers\ElementsHelper;
use common\components\helpers\html\LoadersHelper;

/**
 * Who is in current dialog?
 * @see /js/jquery.easyModal.js
 *
 * @var $this                       yii\web\View
 * @var $dialog                     common\models\Dialog
 * @var $dialogMembers              common\models\DialogMembers
 * @var $isCurrentUserDialogOwner   frontend\view\dialog\_dialogoptions
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
**/

$modal_id = 'dialoguserslist';

echo Html::beginTag('div', [
    'id' => $modal_id,
    'class' => 'modal',
    'role' => 'dialog',
    'data-modal-width' => 380,
    'data-modal-top' => 45,
]);

echo Html::beginTag('div', ['class' => 'modal__content']);

        echo Html::tag('div',

            # Modal header
            Html::tag('span',
                Yii::t('app', 'Members list'),
                ['class' => 'modal__caption c-text--shadow']
            ).

            # Modal button close
            Html::button('<i class="zmdi zmdi-close"></i>',
                [
                    'class' => 'c-button c-button--close modal-close',
                    'title' => Yii::t('app', 'Close')
                ]
            ),

            ['class' => 'modal__header modal__header--branded u-window-box--medium']
        );


        # Modal body
        echo Html::beginTag('div', ['class' => 'modal__body']);

            /* ! --- USERS GRID --- */
            echo Html::beginTag('div', ['id' => 'membersList']);
                echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter']);

                    /* Showing all dialog members as a list except current user */
                    foreach ($dialogMembers as $userId => $dialogMember) {
                        if ($userId != Yii::$app->user->id) {

                            /* ! MEMBER SINGLE ROW */
                            echo Html::beginTag('div', [
                              'id' => 'memberbox-'.$dialogMember['user'],
                              'class' => 'o-grid__cell--width-100 u-window-box--small member__box'
                            ]);
                            echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter']);


                            echo Html::tag('div',
                              /* ! MEMBER IMAGE */
                              ElementsHelper::linkElement('friend',
                                Html::img($dialogMember['userDescription']['userAvatar']['path'], ['class' => 'o-image u-pull-left roundborder member__avatar member__avatar--small']),
                              Url::to(['/id'.$dialogMember['user']], true), false, $dialogMember['fullname']).

                              /* ! MEMBER INFO BLOCK */
                              Html::tag('div',
                                ($dialogMember['is_owner'] == 1 ? '<i class="zmdi zmdi-star"></i> ' : ''). /* Indicator: Is dialog owner? */
                                html_entity_decode($dialogMember['fullname']).
                                ($isCurrentUserDialogOwner ? '<br><a href="">Удалить из диалога</a>' : ''),
                                ['class' => 'member__info u-letter-box--small']),
                              ['class' => 'o-grid__cell--width-80']
                            );

                            echo Html::endTag('div');
                            echo Html::endTag('div');
                        }
                    }

                echo Html::endTag('div');
            echo Html::endTag('div');
            /* ! --- USERS GRID END --- */

        echo Html::endTag('div');


echo Html::endTag('div');
echo Html::endTag('div');

/* JS: @see js/outstyle.modal.js */
?>
<script>jQuery(document).ready(function(){modalInit('#<?=$modal_id;?>');});</script>
