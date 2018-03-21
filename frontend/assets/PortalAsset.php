<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @since 1.0
 */
class PortalAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/blaze.min.css',
        'css/select2.min.css',
        'css/font/fonts.css',
        'css/owlcarousel/owl.carousel.min.css',
        'css/owlcarousel/owl.theme.default.min.css',
        'css/fancybox/jquery.fancybox.min.css',
        'css/fancybox/jquery.fancybox.min.css',
        'css/misc/OverlayScrollbars.min.css',

        'css/portal.css',
        'css/outstyle/base.layout.css',
        'css/outstyle/base.boxes.css',
        'css/outstyle/misc.layout.css',
        'css/outstyle/misc.modal.css',
        'css/outstyle/portal.news.css',

        'css/outstyle/social.comments.css',
    ];
    public $js = [
        'js/misc/jquery.easyModal.js',
        'js/misc/packery.pkgd.min.js',
        'js/misc/intercooler-1.2.1.min.js',
        'js/misc/select2/select2.full.min.js',
        'js/misc/owlcarousel/owl.carousel.min.js',
        'js/misc/fancybox/jquery.fancybox.min.js',

        'js/misc/autosize.min.js',
        'js/misc/ohsnap.min.js',
        'js/misc/echo.min.js',
        'js/misc/preciseTextResize.js',
        'js/misc/way.min.js',

        'js/outstyle.modal.js',
        'js/outstyle.comments.js',
        'js/outstyle.portal.news.js',
        'js/outstyle.portal.articles.grid.js',
        'js/outstyle.portal.article.js',
        'js/outstyle.portal.event.js',
        'js/outstyle.portal.school.js',
        #'js/outstyle.googletags.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
