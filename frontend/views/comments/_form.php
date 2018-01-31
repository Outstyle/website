<?php
/**
 * Comments form (used at particular single pages)
 * Questions? Feel free to ask: <scsmash3r@gmail.com> or skype: smash3rs
 * Also used by widgets/WidgetComments.php
 */

use yii\helpers\Url;
use yii\helpers\Html;

use app\models\UserAvatar;
use common\components\helpers\ElementsHelper;
use common\components\helpers\html\AttachmentsHelper;

/* @var $modelElemId */

# COMMENTS FORM: we are showing the comment FORM input only for registered users
if (!Yii::$app->user->isGuest) {
    echo Html::tag('form',

      # COMMENTS ADD
      Html::tag('div',

        # COMMENTS FORM USER AVATAR
        Html::tag('div',

          Html::img(
            UserAvatar::getAvatarPath(Yii::$app->user->id),
            [
              'alt' => Yii::$app->user->identity->userdescription->name.' '.Yii::$app->user->identity->userdescription->last_name,
              'class' => 'roundborder color-'.Yii::$app->user->identity->userdescription->culture.'--border avatar avatar--smallest',
            ]
          ),

          [
            'class' => 'o-grid__cell o-grid__cell--top o-grid__cell--width-10 o-grid__cell--no-gutter u-c comments_add__avatar'
          ]
        ).

        # COMMENTS INPUT
        Html::tag('div',

          # COMMENTS TEXTFIELD
          Html::tag('div',
            Html::tag('textarea',
              '',
              [
                'name' => 'comments_message',
                'class' => 'c-field u-xsmall c-field--ordinary',
                'placeholder' => Yii::t('app', 'Enter your comment...')
              ]
            ),
            [
              'class' => 'o-field'
            ]
          ).

          # COMMENTS ATTACHMENTS AREA
          AttachmentsHelper::attachmentsArea(Yii::$app->controller->id),

          [
            'class' => 'o-grid__cell o-grid__cell--width-80 o-grid__cell--no-gutter comments_add__body'
          ]
        ).

        Html::tag('div',
          '',
          ['class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-10 u-c']
        ),

        [
          'class' => 'o-grid o-grid--wrap o-grid--center u-letter-box--large comments_add',
        ]
      ).

      # COMMENT OPTIONS
      Html::tag('div',

        # COMMENTS ATTACHMENT BUTTONS
        Html::tag('div',
          AttachmentsHelper::attachmentShowModalButton('photo', 'comments', Yii::$app->controller->id, 'camera').
          AttachmentsHelper::attachmentShowModalButton('video', 'comments', Yii::$app->controller->id, 'youtube-play'),
          ['class' => 'o-grid__cell o-grid__cell--width-50 comments_add__attachments']
        ).

        # COMMENTS SEND BUTTON
        Html::tag('div',
          ElementsHelper::commentAddButton(Yii::$app->controller->id, $modelElemId),
          ['class' => 'o-grid__cell o-grid__cell--width-50']
        ),

        [
          'class' => 'o-grid o-grid--wrap o-grid--center u-letter-box--large comments_options',
        ]
      ),

      [
        'way-data' => Yii::$app->controller->id.'comment',
        'way-persistent' => true
      ]
    );
}
