<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

use frontend\models\DialogMembers;

use common\components\helpers\html\TooltipsHelper;

/**
 * Dialog options (i.e. in messages section)
 *
 * @var $this                       yii\web\View
 * @var $dialog                     common\models\Dialog
 * @var $dialogName                 common\models\Dialog
 * @var $dialogMembers              common\models\DialogMembers
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
**/

/**
 * Dialog name
 * @var string
 */
$dialogName = $dialog['name'] ?? Yii::t('app', 'Unnamed');

/**
 * Returns 'true' if current user is a dialog owner.
 * This check allows to show or hide certain elements in template
 * @var boolean
 */
$isCurrentUserDialogOwner = DialogMembers::checkMemberForDialogOwner(Yii::$app->user->id, $dialogMembers);

echo Html::beginTag('div', [
    'class' => 'o-grid o-grid--wrap ',
]).

    /* ! --- DIALOG OPTIONS - 90% --- */
    Html::tag('div',
        Html::tag('div',

            /* Dialog name changeable, if it is a conversation */
            Html::tag('div',
                (count($dialogMembers) != 2 ? Html::input('text', 'Dialog[name]', $dialogName, [
                    'class' => 'c-field c-field--rounded c-field--editable dialog__name--dynamic',
                    'autocomplete' => 'off',
                    'maxlength' => 40,
                    'ic-post-to' => Url::toRoute(['api/dialog/update']),
                    'ic-indicator' => '#outstyle_loader',
                    'ic-trigger-on' => 'change',
                    'ic-trigger-delay' => '1000ms'
                ]) : '<span class="c-field c-field--noteditable">'.$dialogName.'</span>'),
            [
                'class' => 'c-text--medium'
            ]),

        [
            'class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-90'
        ]),
    [
        'class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-90'
    ]).

    /* ! --- DIALOG ICON - 10% --- */
    Html::tag('div',
        Html::button(
            Html::tag('i', '', [
                'class' => "zmdi zmdi-more zmdi-hc-lg zmdi-hc-2x",
            ]),
        [
            'class' => 'zmdi-icon--hoverable i-dialogbutton u-pull-right dialog__settingsbutton',
            'title' => Yii::t('app', 'Dialog settings')
        ]),
    [
        'class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-10'
    ]).

Html::endTag('div');

/* ! Dialog options tooltip containers*/
echo TooltipsHelper::tooltipContainerForDialogOptions();

/* ! Dialog modals */
if ($dialogMembers) {
    echo $this->render('@modals/userDialogUserList', [
        'dialogMembers' => $dialogMembers,
        'dialog' => $dialog,
        'isCurrentUserDialogOwner' => $isCurrentUserDialogOwner,
    ]);

    echo $this->render('@modals/userDialogLeave', [
        'dialog' => $dialog,
    ]);
}
