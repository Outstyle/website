<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',

        'css/jquery.datetimepicker.min.css',
        'css/select2.min.css',
        'css/backend.ckeditor.css',
    ];
    public $js = [
        '../frontend/web/js/misc/ohsnap.min.js',
        '../frontend/web/js/misc/jquery.maskedinput.min.js',
        '../frontend/web/js/intercooler-1.0.3.min.js',
        'js/misc/jquery.slimscroll.min.js',
        'js/misc/jquery.datetimepicker.full.min.js',
        'js/select2/select2.full.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
