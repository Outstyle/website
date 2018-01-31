<?php

namespace common\components\helpers\html;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\ElementsHelper;

/**
 * Provides all the needed HTML tag elements for better work with attachments
 * @see: @common\components\helpers\ElementsHelper
 */
class AttachmentsHelper extends ElementsHelper
{

    /**
     * Generates an active button element to send API requests for showing attachments modal
     * @param  string $attachment_type    Equals to supported controller ID (i.e. 'video' for video attachment type)
     * @param  string $elem_type          What type of element this attachments will belong to? Defaults to 'comments'
     * @param  string $elem_type_parent   What type of element this {$elem_type} is child of? Defaults to 'board'
     * @param  string $icon               Default icon for button
     * @return HTML <button> tag
     */
    public static function attachmentShowModalButton($attachment_type = '', $elem_type = 'comments', $elem_type_parent = 'board', $icon = '')
    {
        if (!in_array($attachment_type, self::$allowedElements)) {
            return;
        }

        return
        Html::button(
          Html::tag('i', '', [
            'class' => "u-pillar-box--xsmall zmdi zmdi-{$icon} zmdi-hc-2x",
          ]),
        [
          'class' => preg_replace('!\s+!', ' ', trim("zmdi-icon--hoverable i-show{$attachment_type}modal u-pull-left")),
          'title' => Yii::t('app', 'Add {type} as attachment', ['type' => $attachment_type]),
          'ic-action' => 'userShowAttachmentsModal',
          'ic-include' => '{"'.Yii::$app->request->csrfParam.'":"'.self::getCSRFToken().'","elem_type":"'.$elem_type.'","elem_type_parent":"'.$elem_type_parent.'"}',
          'ic-get-from' => Url::toRoute('/api/attachments/get/'.$attachment_type),
          'ic-target' => '#userattachments .modal__body',
          'ic-indicator' => '#userattachments .modal__loader',
          'ic-push-url' => 'false',
        ]);
    }

    /**
     * Generates an active link element to send API requests for adding an attachment to element
     * For the first parameter we are passing object, that will tell us, what type/kind of entity we are working with.
     * We also need to return our active token to reach route.
     *
     * @param  object   $model              What element we are working with?
     * @param  string   $elem_type          Element type (i.e. comments) @see: self::$allowedElements
     * @param  integer  $elem_id
     * @return HTML <a> tag
     */
    public static function attachmentAddLink($model = [], $elem_type = '', $elem_id = 0)
    {
        if (!isset($model['id']) || !$elem_type) {
            return;
        }

        /* Type check: VIDEO --- illogical behaviour [?]*/
        if (isset($model['video_id']) && isset($model['hash'])) {
            $attachment_type = 'video';
            $attachment_link = Url::toRoute('/video-'.$model['hash'], true);
            $attachment_title = Html::img($model['video_img'], ['class' => 'o-image u-full-width u-pull-left user__videothumbnail']).'<i class="zmdi zmdi-check-circle zmdi-hc-5x"></i><div class="clearfix"></div>';
        }

        return Html::a($attachment_title, $attachment_link,
          [
            'class' => 'user__addattachment',
            'ic-get-from' => Url::toRoute('/api/attachments/add'),
            'ic-action' => 'userHideAttachmentsModal',
            'ic-include' => '{"'.Yii::$app->request->csrfParam.'":"'.self::getCSRFToken().'","type":"'.$attachment_type.'","id":'.(int)$model['id'].',"elem_type":"'.$elem_type.'","elem_id":'.(int)$elem_id.'}',
            'ic-target' => '#board_attachments',
            'ic-indicator' => self::DEFAULT_AJAX_LOADER,
            'ic-push-url' => 'false',
          ]
        );
    }

    /**
     * Div for handling attachments list
     * Because an attachment entity counts as a child element of any other entity and it can't be listed by itself,
     * ic-trigger-on is a must have attr, since it will trigger attachment list only from other elements
     * @param  string   $elem_type        Element type (i.e. comments)   @see: self::$allowedElements
     * @param  integer  $elem_id
     * @return HTML <div> tag
     */
    public static function attachmentsArea($elem_type = '', $elem_id = 0)
    {
        /* If $elem_id is already set, that means we are getting an attachments for already existing entity (i.e. comment) */
        if ($elem_id) {
            $include = ',"elem_id":'.(int)$elem_id.',"elem_type":'.self::getElementIdByControllerId($elem_type);
            $elem_type = $elem_type.'-'.$elem_id;
            $trigger_on = 'scrolled-into-view';
            $indicator = $elem_type.' .loader';
            $loader = '<div class="loader--small"></div>';
        } else {
            $include = '';
            $elem_type = $elem_type.'_attachments';
            $trigger_on = $elem_type;
            $indicator = self::DEFAULT_AJAX_LOADER;
            $loader = '';
        }

        return Html::tag('div', $loader, [
          'id' => $elem_type,
          'class' => 'attachments_area',
          'ic-get-from' => Url::toRoute('/api/attachments/list'),
          'ic-trigger-on' => $trigger_on,
          'ic-include' => '{"'.Yii::$app->request->csrfParam.'":"'.self::getCSRFToken().'"'.$include.'}',
          'ic-indicator' => $indicator,
          'ic-push-url' => 'false'
        ]);
    }
}
