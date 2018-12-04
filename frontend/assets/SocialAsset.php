<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @since 1.0
 */
class SocialAsset extends AssetBundle
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
        'css/misc/OverlayScrollbars.min.css',
        'css/misc/tooltipster.bundle.min.css',

        'css/outstyle/base.layout.css',
        'css/outstyle/base.boxes.css',
        'css/outstyle/misc.layout.css',
        'css/outstyle/misc.modal.css',
        'css/outstyle/misc.tooltips.css',
        'css/outstyle/misc.uploader.css',
        'css/outstyle/social.layout.css',
        'css/outstyle/social.header.css',
        'css/outstyle/social.sidebar.css',
        'css/outstyle/social.user.css',
        'css/outstyle/social.user.attachments.css',
        'css/outstyle/social.video.css',
        'css/outstyle/social.photo.css',
        'css/outstyle/social.friends.css',
        'css/outstyle/social.comments.css',
        'css/outstyle/social.dialogs.css',
        'css/outstyle/social.chat.css',
        'css/outstyle/social.board.post.css',

    ];
    public $js = [
        'js/misc/jquery.easyModal.js',
        'js/misc/packery.pkgd.min.js',
        'js/misc/select2/select2.full.min.js',
        'js/misc/owlcarousel/owl.carousel.min.js',
        'js/misc/fancybox/jquery.fancybox.min.js',
        'js/misc/jquery.overlayScrollbars.min.js',
        'js/misc/jquery.dm-uploader.min.js',

        /* non-jQuery JS libs */
        'js/misc/intercooler-1.2.1.min.js',
        'js/misc/autosize.min.js',
        'js/misc/ohsnap.min.js',
        'js/misc/echo.min.js',
        'js/misc/preciseTextResize.js',
        'js/misc/way.min.js',
        'js/misc/tooltipster.bundle.min.js',

        /* website related JS code */
        'js/outstyle.social.sidebar.js',
        'js/outstyle.social.tooltips.js',
        'js/outstyle.social.badges.js',
        'js/outstyle.user.video.js',
        'js/outstyle.user.photo.js',
        'js/outstyle.user.friends.js',
        'js/outstyle.user.attachments.js',
        'js/outstyle.user.photoalbums.js',
        'js/outstyle.user.photoalbum.create.js',
        'js/outstyle.user.photoalbum.edit.js',
        'js/outstyle.user.photoalbum.delete.js',
        'js/outstyle.user.messages.js',
        'js/outstyle.userboard.js',
        'js/outstyle.userboard.posts.js',
        'js/outstyle.modal.js',
        'js/outstyle.comments.js',
        'js/outstyle.files.upload.js',

        /* misc */
        #'js/outstyle.googletags.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\widgets\ActiveFormAsset',
        'yii\validators\ValidationAsset'
    ];
}
