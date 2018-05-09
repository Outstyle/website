<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;
/**
 * @author andrij200390 <andrij200390@gmail.com>
 *
 * @since 1.0
 */
use yii\web\AssetBundle;
class ErrorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [];
    public $js = [
        'js/outstyle.googletags.js',
        'js/outstyle.ya.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}