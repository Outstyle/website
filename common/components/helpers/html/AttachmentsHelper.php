<?php

namespace common\components\helpers\html;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

use frontend\models\Photo;

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
     * NOTE: type check can be splitted up into separate pieces to decrease complexity, if there will be more attachment types
     *
     * @param  object   $model              What element we are working with?
     * @param  string   $elem_type          Element type (i.e. comments) @see: self::$allowedElements
     * @param  integer  $elem_id
     * @return HTML <a> tag
     */
    public static function attachmentAddLink($model = [], $elem_type = '', $elem_id = 0, $elem_type_parent = false)
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

        /* Type check: PHOTO */
        if (isset($model['img']) && isset($model['id'])) {
            $attachment_type = 'photo';
            $attachment_link = Url::toRoute('/photo-'.$model['id'], true);
            $attachment_preview_image = Photo::getByPrefixAndServiceId($model['img'], '210x126_', $model['service_id']);
            $attachment_title = Html::img($attachment_preview_image, ['class' => 'o-image u-full-width u-pull-left user__photothumbnail']).'<i class="zmdi zmdi-check-circle zmdi-hc-5x"></i><div class="clearfix"></div>';
        }

        return Html::a($attachment_title, $attachment_link,
          [
            'class' => 'user__addattachment',
            'ic-get-from' => Url::toRoute('/api/attachments/add'),
            'ic-action' => 'userHideAttachmentsModal',
            'ic-include' => '{"'.Yii::$app->request->csrfParam.'":"'.self::getCSRFToken().'","type":"'.$attachment_type.'","id":'.(int)$model['id'].',"elem_type":"'.$elem_type.'","elem_id":'.(int)$elem_id.'}',
            'ic-target' => '#board_attachments',
            'ic-indicator' => '#board_attachments_loader',
            'ic-push-url' => 'false',
          ]
        );
    }

    /**
     * Div for handling attachments list (i.e. in single comment)
     *
     * Because an attachment entity counts as a child element of any other entity and it can't be listed by itself,
     * ic-trigger-on is a must have attr (for existing ID), since it will trigger attachment list only from other elements
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
            $indicator = '#'.$elem_type.'_attachments_loader';
            $loader = '<div class="loader--small"></div>';
        } else {
            /*
              If element is not existing, that means we are working with not yet sent attachments on comments form
              Therefore, we need to pass parent element, so we could know what type of entity we are working with
             */
            $include = ',"elem_type_parent":'.self::getElementIdByControllerId($elem_type);
            $loader = '';
            $elem_type = $elem_type.'_attachments';
            $trigger_on = $elem_type;
            $indicator = '';
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

    /**
     * Loader spinner for attachments
     *
     * @param  string   $elem_type        Element type (i.e. comments)   @see: self::$allowedElements
     * @return HTML <div> tag
     */
    public static function attachmentsLoader($elem_type = '')
    {
        if (!$elem_type) {
            return;
        }

        $loader = '<div class="loader--smallest"></div>';

        return Html::tag('div', $loader, [
          'id' => $elem_type.'_attachments_loader'
        ]);
    }
}
