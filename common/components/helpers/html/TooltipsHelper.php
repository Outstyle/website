<?php

namespace common\components\helpers\html;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\ElementsHelper;

/**
 * TooltipsHelper provides a set of static methods for working with everything that is related to 'tooltip' entity.
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @since 1.0
 */
class TooltipsHelper extends ElementsHelper
{
    /**
     * Tooltip template for photoalbum
     * @see: http://iamceege.github.io/tooltipster/
     *
     * @return HTML
     */
    public static function tooltipContainerForPhotoalbum()
    {
        return Html::tag('div',

            # for: #photo__editbutton
            Html::tag('span',

                Html::a(Yii::t('app', 'Add photo'),
                  'javascript:void(0)',
                  [
                    'href' => 'javascript:void(0)',
                    'ic-action' => 'userShowUploadArea',
                  ]
                ).

                Html::a(Yii::t('app', 'Add album'),
                  'javascript:void(0)',
                  [
                    'href' => 'javascript:void(0)',
                    'ic-action' => 'userShowPhotoalbumCreateModal',
                    'ic-indicator' => '#userphotoalbumcreate .modal__loader',
                    'ic-target' => '#userphotoalbumcreate .modal__body',
                    'ic-post-to' => Url::toRoute(['api/forms/photoalbum/create']),
                    'ic-push-url' => 'false',
                    'ic-select-from-response' => '#photoalbum-activeform',
                  ]
                ),

              ['id' => 'photos_edit_tooltip_content']
            ),

          ['class' => 'tooltip_templates']
        );
    }
}
