<?php
use yii\helpers\Html; 
use yii\helpers\Url;
use frontend\models\FriendRequests;
use frontend\models\UserAvatar;
use frontend\models\UserDescription;
use frontend\models\Country;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Группы').' - '.Yii::$app->name;
$this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Группы'), 'url' => Url::toRoute('friends/')];
$this->params['breadcrumbs'][] = Yii::t('app', 'Группы');
?>
<div class="wrapper-friends-main">
	<div class="wrapper-friends-sidebar">
		<div class="wrapper-friends-sidebar-i">
			<div class="wp-search-friend-block">
				<input type="search" class="search-field-friend" >
				<input type="submit" value="" class="search-button-friend">
			</div>
			<div class="friends-sidebar-link-list"> 
				<ul>
					<li><a href="<?=Url::toRoute('/myprofile/friends/');?>"></a></li>
					<li><a href="<?=Url::toRoute('/search/');?>" ></a></li>
					<li><a href="<?=Url::toRoute('/myprofile/newsfeed');?>" ></a></li>
					<li><a href="<?=Url::toRoute('/groups/');?>" class="friends-sidebar-active"></a></li>
					<div class="clearboth"></div>
				</ul>
			</div>
			<div class="wrapper-list-input-checkbox">
				<form action="" method="POST">
					<div class="wrapper-sort-select_main">
						<p></p>
						<select >
							<!--<option value="">По популярности</option>-->
							<option value="1">По дате обновления</option>
							<option value="2">По популярности</option>
							<option value="3">По численности</option>
						</select>
					</div>
					<p class="wp-list-input-checkbox-country-i-title">Тип контента</p>
					<div class="wp-list-input-checkbox-country-i">
						<p></p>
						<select >
							<option value="1">Все</option>
							<option value="2">Лента</option>
							<option value="3">Фотографии</option>
							<option value="4">Видеозаписи</option>
							<option value="5">Рекомендации</option>
							<option value="6">Друзья</option>
							<option value="7">Группы</option>
							<option value="8">Понравившиеся</option>
						</select>
					</div>
					<p class="wp-list-input-checkbox-country-i-title">Из области</p>
					<div class="wp-list-input-checkbox-country-i">
						<p></p>
						<select >
							<option value="1">Бибоинг</option>
							<option value="2">Бибоинг2</option>
						</select>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="wrapper-tabs-friends">
		<div class="groups-tabs_container">
		  <ul class="groups-tabs">
		    <li class="inl-bl active-tabs">Мои группы</li>
		    <li class="inl-bl">Управление <span class="add-group"><img src="<?php echo Yii::$app->homeUrl; ?>css/img/add-group.png"></span></li>
		    <li class="inl-bl">Все группы</li>
		    <div class="clearboth"></div>
		  </ul>
		  <div class="groups-tab_container" style="display: block;">
		    	<div class="my-groups">
		    		<div class="info-group">
		    			<div class="goup-img">
		    				<img src="<?php echo Yii::$app->homeUrl; ?>css/img/img-group.png">
		    			</div>
		    			<div class="name-type-num-group">
			    			<div class="name-group">
			    				Типичный спилберг
			    			</div>
			    			<div class="type-group">
			    				Публичная группа
			    			</div>
			    			<div class="number-of-subscribers">
			    				547 645 подписчиков
			    			</div>
		    			</div>
		    			<div class="time-group">
		    				Вчера в 17:45
		    			</div>
		    			<div class="add-group-icon">
		    				<img src="<?php echo Yii::$app->homeUrl; ?>css/img/plus-group.png">
		    			</div>
		    		</div>
		    	</div>
		    	<div class="my-groups" style="top:-1px;">
		    		<div class="info-group">
		    			<div class="goup-img">
		    				<img src="<?php echo Yii::$app->homeUrl; ?>css/img/img-group.png">
		    			</div>
		    			<div class="name-type-num-group">
			    			<div class="name-group">
			    				Типичный спилберг
			    			</div>
			    			<div class="type-group">
			    				Публичная группа
			    			</div>
			    			<div class="number-of-subscribers">
			    				7 547 645 подписчиков
			    			</div>
		    			</div>
		    			<div class="time-group">
		    				Вчера в 17:45
		    			</div>
		    			<div class="add-group-icon">
		    				<img src="<?php echo Yii::$app->homeUrl; ?>css/img/plus-group.png">
		    			</div>
		    		</div>
		    	</div>
		  </div>

		  <div class="groups-tab_container">
		   		УПРАВЛЕНИЕ
		  </div>

		  <div class="groups-tab_container">
		    	all groups
		  </div>
		</div>
	</div>

	<div class="clearboth"></div>
</div>
	<script type="text/javascript">
	$('.wrapper-sort-select_main select').each(function(){
		$(this).siblings('p').text( $(this).children('option:selected').text() );
	});
	$('.wrapper-sort-select_main select').change(function(){
		$(this).siblings('p').text( $(this).children('option:selected').text() );
	});
	
	$('.wp-list-input-checkbox-country-i select').each(function(){
		$(this).siblings('p').text( $(this).children('option:selected').text() );
	});
	$('.wp-list-input-checkbox-country-i select').change(function(){
		$(this).siblings('p').text( $(this).children('option:selected').text() );
	});

	$('.wp-list-those-culture-i select').each(function(){
		$(this).siblings('p').text( $(this).children('option:selected').text() );
	});
	$('.wp-list-those-culture-i select').change(function(){
		$(this).siblings('p').text( $(this).children('option:selected').text() );
	});

	$(document).ready(function() {  
  $('ul.groups-tabs').each(function() {
    $(this).find('li').each(function(i) {
      $(this).click(function() {
        $(this).addClass('active-tabs').siblings().removeClass('active-tabs');
        var p = $(this).parents('div.groups-tabs_container');
        p.find('div.groups-tab_container').hide();
        p.find('div.groups-tab_container:eq(' + i + ')').show();
      });
    });
  });
})
</script>		