<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Главная').' - '.Yii::$app->name;
$this->registerMetaTag(['name' => 'description', 'content' => $this->title]);

?>
<div class="site-index">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-4 col-md-3 sidebar">
        <h6>- <?=Yii::t('app', 'Essentials'); ?> -</h6>
        <ul class="nav nav-sidebar">
          <li class="active"><a href="#"><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Backend overview</a></li>
          <li><a href="#"><span class="glyphicon glyphicon-file" aria-hidden="true"></span> For developers</a></li>
          <li><a href="#"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Contacts</a></li>
        </ul>
        <h6>- <?=Yii::t('app', 'Settings'); ?> -</h6>
        <ul class="nav nav-sidebar">
          <li><a href="<?=Url::to(['/category']); ?>"><span class="glyphicon glyphicon-fire" aria-hidden="true"></span> Elements (Categories)</a></li>
          <li><a href="<?=Url::to(['/photoalbum']); ?>"><span class="glyphicon glyphicon-camera" aria-hidden="true"></span> Photoalbums</a></li>
        </ul>
      </div>
      <div class="col-sm-8 offset-sm-3 col-md-9 offset-md-2 main">
        <h3><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Ecosystem Status</h3>

        <h4>Thirdparty Services Availability</h4>
        <ul class="list-group">

            <li class="list-group-item d-flex justify-content-between align-items-center" id="vk_api_check">
                api.vk.com
                <div id="vk_api_loader" class="loader loader--medium u-r"></div>
                <?php
                echo Html::tag('span', '', [
                    'class' => 'badge badge-pill',
                    'ic-get-from' => Url::to('/api/geo/vk/check'),
                    'ic-indicator' => '#vk_api_loader',
                    'ic-trigger-on' => 'scrolled-into-view'
                ]);
                ?>
                <div class="list-group-info">
                    Using: <b>CURL</b><br>
                    Proxy enabled: <b><?php echo (Yii::$app->params['CURLHelper']['useProxies']) ? 'true' : 'false';?></b><br>
                    Timeout: <b><?php echo Yii::$app->params['CURLHelper']['timeout'];?></b><br>
                    <div class="proxylist info u-r c-red"></div>
                </div>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center" id="googlemaps_api_check">
                maps.googleapis.com
                <div id="googlemaps_api_loader" class="loader loader--medium u-r"></div>
                <?php
                echo Html::tag('span', '', [
                    'class' => 'badge badge-pill',
                    'ic-get-from' => Url::to('/api/geo/google/check'),
                    'ic-indicator' => '#googlemaps_api_loader',
                    'ic-trigger-on' => 'scrolled-into-view'
                ]);
                ?>
                <div class="list-group-info">
                    Using: <b>file_get_contents()</b><br>
                    <div class="proxylist info u-r c-red"></div>
                </div>
            </li>
        </ul>

      </div>
    </div>
  </div>
</div>
<?php
/*
    ----------------------------------------------------------------------------
    JS stuff, that is related ONLY to this view
    ----------------------------------------------------------------------------
*/
?>
<script>
jQuery(document).ready(function () {

    /* VK CHECK HANDLERS */
    jQuery("body").on("vkApiError", function(event, data) {
        jQuery("#vk_api_check").find('.badge').addClass('alert-red');
        if (data.proxy) {
            jQuery("#vk_api_check").find('.list-group-info .proxylist').append(data.proxy);
        }
    });

    jQuery("body").on("vkApiSuccess", function() {
        jQuery("#vk_api_check").find('.badge').addClass('alert-green');
    });

    /* GOOGLE CHECK HANDLERS */
    jQuery("body").on("googleApiError", function(event, data) {
        jQuery("#googlemaps_api_check").find('.badge').addClass('alert-red');
        if (data) {
            jQuery("#googlemaps_api_check").find('.list-group-info .info').append(data);
        }
    });

    jQuery("body").on("googleApiSuccess", function() {
        jQuery("#googlemaps_api_check").find('.badge').addClass('alert-green');
    });

});
</script>
