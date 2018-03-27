<?php

namespace common\components\helpers\html;

use Yii;
use yii\helpers\Html;

use common\components\helpers\ElementsHelper;

/**
 * LoadersHelper provides a set of static methods for working with everything that is related to 'loader' entity.
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @since 1.0
 */
class LoadersHelper extends ElementsHelper
{
    /**
     * Generates an img tag for showing a praticular loader
     * TODO: [?] Make routes to be in config file
     *
     * @param string Loader type/style
     * @param string img tag class
     *
     * @return html IMG tag with predefined image
     */
    public static function loaderImage($image = '', $class = '')
    {
        if ($image == 'breakdance') {
            return '<img src="/frontend/web/images/images/breakdance_loader.gif" class="'.$class.'">';
        }

        return '<img src="/frontend/web/images/images/breakdance_loader.gif">';
    }

    /**
     * Generates a div tag for showing a particular loader
     * @param  string $elemClass
     * @param  string $loaderSize
     * @return html Div with predefined class
     */
    public static function loaderDiv($elemClass = 'default', $loaderSize = 'smallest')
    {
        return Html::tag('div', '',
        [
          'class' => $elemClass.'--loader loader--'.$loaderSize
        ]);
    }
}
