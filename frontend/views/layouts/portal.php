<?php
/**
 * Main PORTAL layout
 * Questions? Feel free to ask: <scsmash3r@gmail.com> / skype: smash3rs / Telegram: @scsmash3r
 * Github: https://github.com/scsmash3r
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Menu;
use yii\widgets\Spaceless;

use frontend\assets\PortalAsset;

PortalAsset::register($this);

$this->beginPage();
Spaceless::begin();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo Html::encode($this->title); ?></title>
<?php $this->head(); ?>
<?php echo Html::csrfMetaTags(); ?>
<link rel="shortcut icon" type="image/png" href="/css/favicon.png">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="//maps.googleapis.com/maps/api/js?key=<?=Yii::$app->params['googleMapsApiKey'];?>&amp;extension=.js"></script>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>
<body class="portal user<?php echo (Yii::$app->user->id) ? '-registered' : '-guest'; ?>" ic-history-elt=''>
<?php
$this->beginBody();

    echo Html::beginTag('div', ['class' => 'wrap']),

        Html::beginTag('header', ['class' => 'o-grid portal__header']),
            Html::beginTag('div', ['class' => 'o-grid__cell o-grid__cell--width-15 u-center-block']),
                Html::a('', Url::to(['/']), ['id'=>'logo', 'class'=>'u-center-block__content']),
            Html::endTag('div'),
            Html::beginTag('div', ['class' => 'o-grid__cell o-grid__cell--width-60 u-center-block']);
                /* --- Top navigation menu with items highlight --- */
                $checkController = function ($route) {
                    return $route === $this->context->getUniqueId(); /* Checks only top-level */
                };
                $checkRoute = function ($route) {
                    return $route === $this->context->getRoute(); /* Checks all levels, including child pages */
                };
                echo Html::beginTag('nav', [
                        'class' => 'c-nav c-nav--inline u-center-block__content',
                        'ic-target' => '#content',
                        'ic-push-url' => 'true',
                        'ic-select-from-response' => '#ajax'
                    ]),
                    Html::beginTag('div',[
                            'class'=>'menu__icon'
                    ]),
                    Html::tag('span'),
                    Html::tag('span'),
                    Html::tag('span'),
                    Html::tag('span'),
                    Html::endTag('div'),
                    Html::beginTag('div',[
                            'class'=>'menu__links'
                    ]),

                    Menu::widget([
                        'itemOptions' => ['tag' => 'span'],
                        'options' => ['tag' => false],
                        'encodeLabels' => false,
                        'activeCssClass' =>'c-text--loud',
                        'linkTemplate' => '<a href="{url}" ic-get-from="{url}" ic-trigger-delay="500ms" ic-indicator="#outstyle_loader" class="c-nav__item c-text--shadow">{label}<i></i></a>',
                        'items' => [
                            ['label' => Yii::t('app', 'Новости'), 'url' => ['/'], 'active' => $checkController('news')],
                            ['label' => Yii::t('app', 'Статьи'), 'url' => ['article/index'], 'active' => $checkController('article')],
                            ['label' => Yii::t('app', 'Афиша'), 'url' => ['events/index'], 'active' => $checkController('events')],
                            ['label' => Yii::t('app', 'Школа'), 'url' => ['school/index'], 'active' => $checkController('school')],
                            ['label' => Yii::t('app', 'О нас'), 'url' => ['page/about'], 'active' => $checkRoute('page/about')],
                        ],
                    ]),
                    Html::endTag('div'),
                Html::endTag('nav'),
            Html::endTag('div'),
            Html::beginTag('div', ['class' => 'o-grid__cell o-grid__cell--width-25 o-grid__cell--center u-center-block']),
                $this->render('@forms/headerLoginForm'),
            Html::endTag('div'),
        Html::endTag('header'),

        Html::beginTag('div', ['id' => 'content', 'class' => 'content u-window-box--small']),
            Html::beginTag('main'),
                $content,
            Html::endTag('main'),
        Html::endTag('div');

        /*
            ==================================
            Actions & stuff for unlogged users
            ==================================
        */
        if (!Yii::$app->user->id) {
            echo $this->render('@modals/passwordRestore');
            echo $this->render('@modals/loginRequire');
            echo $this->render('@modals/register');
        }

    echo Html::endTag('div');

$this->endBody();

/**
 * Other additional stuff for handling data - i.e. containers for modal content or 'back to top' button, that is present on every page
 * JS code also needs to go here, but ONLY that is related to THIS layout and it's selectors! (i.e. highlight active menu items)
 * Little exception: we need to get back original 'height' of the #content element with every new page request
 */
?>

<script>
    /*Only for mobile menu (close menu)*/
    jQuery(document).mouseup(function (e) {
        var container = $(".c-nav");
        if (container.has(e.target).length === 0){

            if(jQuery('.c-nav').hasClass('menu_state_open')){
                jQuery('.c-nav').removeClass('menu_state_open');
                jQuery('.menu__icon').closest('.c-nav');
            }
        }

    });
    jQuery(document).ready(function () {
        function windowSize(){
            if (jQuery(window).width() >= '779'){
             if(jQuery('.c-nav').hasClass('menu_state_open')){
                 jQuery('.c-nav').removeClass('menu_state_open');
             }
            }
        }
        jQuery(window).load(windowSize);
        jQuery(window).resize(windowSize);
        jQuery(window).on('load resize',windowSize);

    jQuery("a.c-nav__item").on("click", function() {
        var activeClass = 'c-text--loud';
        jQuery('#outstyle_loader').show();
        jQuery('nav span').removeClass(activeClass);
        jQuery(this).parent().addClass(activeClass);
        /*For mobile menu*/
        jQuery('.menu__icon').closest('.c-nav').toggleClass('menu_state_open');
    });
    /*For mobile menu*/
    jQuery('.menu__icon').on('click', function() {
        jQuery(this).closest('.c-nav').toggleClass('menu_state_open');
    });

    /*For Login form and mobile version (width 380px and small)*/
    jQuery('.form-login-ico').on('click', function() {
        jQuery('.login-form').toggleClass('login_form_open');
        jQuery(this).toggleClass('login_form_open');
    });






        /* --- Before Intercooler headers processing --- */
    jQuery(document).on("afterHeaders.ic", function(event, element, xhr) {

        /* --- Changing title (if there is one) after each XHR request --- */
        var responseTitle = xhr.getResponseHeader("X-IC-Title");
        if (responseTitle !== null) {
            document.title = decodeURIComponent(responseTitle);
        }

    });

    /* --- After Intercooler has finished everything --- */
    jQuery(document).on("complete.ic", function(evt, elt, data, status, xhr, requestId) {
        jQuery.urlParam = function(name) {
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(data);
            if (results==null){
               return null;
            }
            else{
               return results[1] || 0;
            }
        }

        /**
         * We need to automatically scroll page on top if we're refreshing contents
         * So let's depend on IC's target ID
         */
        if (jQuery.urlParam('ic-target-id')) {
            window.scroll(0 ,0);
        }

    });

    jQuery(document).mouseup(function(e) {
        var container = jQuery("#filter-box");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.hide();
        }
    });

    /* --- Scroll to top stuff --- */

      jQuery('#scrollup i').mouseover( function(){
        jQuery( this ).animate({opacity: 1},100);
      }).mouseout( function(){
        jQuery( this ).animate({opacity: 0.65},100);
      }).click( function(){
        window.scroll(0 ,0);
        return false;
      });

      jQuery(window).scroll(function(){
        if ( jQuery(document).scrollTop() > 0 ) {
          jQuery('#scrollup').fadeIn('fast');
        } else {
          jQuery('#scrollup').fadeOut('fast');
        }
      });


});
</script>
<script>
/**
* For responsive design. The script will check on the browser opera mini.
*/
var _ua = window.navigator.userAgent;
var browser = {
    version: (_ua.match( /.+(?:me|ox|on|rv|it|era|opr|ie)[\/: ]([\d.]+)/ ) || [0,'0'])[1],
    opera: (/opera/i.test(_ua) || /opr/i.test(_ua)),
    msie: (/msie/i.test(_ua) && !/opera/i.test(_ua) || /trident\//i.test(_ua)),
    msie6: (/msie 6/i.test(_ua) && !/opera/i.test(_ua)),
    msie7: (/msie 7/i.test(_ua) && !/opera/i.test(_ua)),
    msie8: (/msie 8/i.test(_ua) && !/opera/i.test(_ua)),
    msie9: (/msie 9/i.test(_ua) && !/opera/i.test(_ua)),
    mozilla: /firefox/i.test(_ua),
    chrome: /chrome/i.test(_ua),
    safari: (!(/chrome/i.test(_ua)) && /webkit|safari|khtml/i.test(_ua)),
    iphone: /iphone/i.test(_ua),
    ipod: /ipod/i.test(_ua),
    iphone4: /iphone.*OS 4/i.test(_ua),
    ipod4: /ipod.*OS 4/i.test(_ua),
    ipad: /ipad/i.test(_ua),
    android: /android/i.test(_ua),
    bada: /bada/i.test(_ua),
    mobile: /iphone|ipod|ipad|opera mini|opera mobi|iemobile|android/i.test(_ua),
    msie_mobile: /iemobile/i.test(_ua),
    safari_mobile: /iphone|ipod|ipad/i.test(_ua),
    opera_mobile: /opera mini|opera mobi/i.test(_ua),
    opera_mini: /opera mini/i.test(_ua),
    mac: /mac/i.test(_ua),
    search_bot: /(yandex|google|stackrambler|aport|slurp|msnbot|bingbot|twitterbot|ia_archiver|facebookexternalhit)/i.test(_ua)
};
if(browser.opera_mini){
    var cssId = 'OperaMiniCss';
    var head  = document.getElementsByTagName('head')[0];
    var link  = document.createElement('link');
    link.id   = cssId;
    link.rel  = 'stylesheet';
    link.type = 'text/css';
    link.href = '/css/outstyle/portal.media.operamini.css';
    link.media = 'all';
    head.appendChild(link);
    alert('Рекомендуем воспользоваться другим браузером: Chrome или Safari. В Opera Mini сайт может отображаться некорректно');
}
</script>

<div id="scrollup"><i class="icon-up-open-big icon-huge"></i></div>
<div id="outstyle_loader"><div class="loader"></div></div>
<div id="ohsnap"></div>
</body>
</html>
<?php
Spaceless::end();
$this->endPage(); ?>
