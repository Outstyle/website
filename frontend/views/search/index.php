<?php

use yii\helpers\Html; 
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\models\FriendRequests;
use frontend\models\UserDescription;
use frontend\models\Country;
use frontend\models\User;
use frontend\models\Photo;
use frontend\widgets\WidgetProfileUserMenu;
use frontend\models\UserAvatar;

use yii\helpers\Json;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Поиск друзей');
$this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Поиск друзей'), 'url' => Url::toRoute('search/')];
$this->params['breadcrumbs'][] = Yii::t('app', 'Друзья');
?>
<div class="wrapper-friends-main">
	<div class="wrapper-friends-sidebar fr-123">
		<div class="wrapper-friends-sidebar-i">
			<div class="wp-search-friend-block">
				<div class="wrapper-list-input-checkbox">
					<input type="text" id="search" class="search-field-friend">
					<input type="submit" value="" class="search-button-friend">
				</div>
			</div>
			<div class="friends-sidebar-link-list">
				<ul>
					<li title="Друзья"><a href="<?=Url::toRoute('/myprofile/friends/');?>"></a></li>
					<li title="Поиск друзей"><a href="<?=Url::toRoute('/search/');?>" class="friends-sidebar-active"></a></li>
					<li title="Мои новости"><a href="<?=Url::toRoute('/myprofile/newsfeed');?>"></a></li>
					<!-- <li><a href="<?//=Url::toRoute('/groups/');?>" ></a></li> -->
					<div class="clearboth"></div>
				</ul>
			</div>
			<div class="wrapper-list-input-checkbox">

				<div class="wrapper-sort-select_main">
					<p></p>
					<select id="sort">
						<option value="rating">По рейтингу</option>
						<option value="id">Дате регистрации</option>
					</select>
				</div>
				<p class="wp-list-input-checkbox-country-i-title">Страна</p>
				<div class="wp-list-input-checkbox-country-i">
					<p></p>
					<select id="country" >
						<option value="0">-Не выбрано-</option>
						<?=Country::getCountryOption();?>
					</select>
				</div>
				<p class="wp-list-input-checkbox-country-i-title">Город</p>
				<div class="wp-list-input-checkbox-country-i">
					<p></p>
					<select id="city">
						<option value="0">-Не выбрано</option>
					</select>
				</div>
				<p class="wp-title-age">Возраст</p>
				<div class="wp-age-content">
					<input type="text" id="age_start" class="wp-age-content-from" placeholder="От">
					<div class="wp-separator-age-field">-</div>
					<input type="text" id="age_end" class="wp-age-content-before" placeholder="До"> 
					<div class="clearboth"></div>
				</div>
				<div class="wrapper-flooring-checkbox">
					<p class="wrapper-flooring-checkbox-title">Пол</p>
					<div class="wrapper-flooring-checkbox-i">
	                    <input id="wrapper-flooring-checkbox-female" type="checkbox" >
	                    <label for="wrapper-flooring-checkbox-female"><span></span>Женский</label>
	                    <div class="clearboth"></div>
                    </div>
                    <div class="wrapper-flooring-checkbox-i">
	                    <input id="wrapper-flooring-checkbox-male" type="checkbox" >
	                    <label for="wrapper-flooring-checkbox-male"><span></span>Мужской</label>
	                    <div class="clearboth"></div>
                    </div>
                </div>
				<p class="wp-list-those-culture-i-title">Кто в культуре</p>
				<div class="wp-list-those-culture-i">
					<p></p>
					<select id="culture" >
						<option value="0">-Не выбрано</option>
						<option value="1">Бибой</option>
						<option value="2">mc</option>
						<option value="3">dj</option>
						<option value="4">writer</option>
						<option value="5">Ганста</option>
					</select>
				</div>
		
				<div class="wp-list-checkbox-search-friend">
					<div class="wp-list-checkbox-search-friend-i">
	                    <input id="wp-list-checkbox-search-friend-foto" type="checkbox">
	                    <label for="wp-list-checkbox-search-friend-foto"><span></span>С фотографией</label>
	                    <div class="clearboth"></div>
                    </div>
                    <div class="wp-list-checkbox-search-friend-i">
	                    <input id="wp-list-checkbox-search-friend-online" type="checkbox">
	                    <label for="wp-list-checkbox-search-friend-online"><span></span>сейчас на сайте</label>
	                    <div class="clearboth"></div>
                    </div>
				</div>
			</div>
			
<script>
                
var page = 1;
var loadMore = true;
////вывод всех пользователей

////поиск пользователей
$(document).ready(function() {

    var city = $("#city option:selected").val();
    var country = $("#country option:selected").val();

    if ( country == 0) {
        $( "#city").parent().find('p').html("-Не выбрано");
        city = 0;
    }

    var data = {
        search : $("#search").val(),
        sort : $("#sort option:selected").val(),
        country : $("#country option:selected").val(),
        city : city,
        age_start : $('#age_start').val(),
        age_end : $('#age_end').val(),
        female : $('#wrapper-flooring-checkbox-female').prop("checked"),
        male : $('#wrapper-flooring-checkbox-male').prop("checked"),
        culture : $("#culture option:selected").val(),
        foto : $('#wp-list-checkbox-search-friend-foto').prop("checked"),
        online : $('#wp-list-checkbox-search-friend-online').prop("checked"),
        page : 1,
    };
    //console.log(data);
    $.ajax({
            dataType: 'JSON',
            type : 'get',
            data : data,
            url: '/search/list/'
    }).then(function(data){
//                        $('img').error(function() {
//                            $(this).attr( "src", "<?php echo Yii::$app->homeUrl; ?>"+"images/avatar/def_avatar.jpg" );
//                        });
//                        console.log(data);
        if (data.users){
           var users = data.users;
           var count = users.length;

            for (var i = 0 ; i < count; i++ ){
                var userId = users[i].id;
                    if (users[i]['onlineInd'] == 1){
                        var  status = "<div style='background:#00a651' class='all-list-friends-i-manipulation-block-status'></div>";
                    }else{
                        var  status = "<div style='background:red' class='all-list-friends-i-manipulation-block-status'></div>";
                    } 

                    if (users[i]['isFriend'] == 1){
                        var  isFriend = "<div style='color:green'>&#10004;</div>";
                    }else if(users[i]['isFriend'] == 0){
                        var  isFriend = (users[i]['id'] != <?=Yii::$app->user->id?>) ? "<div id="+users[i]['id']+" class='all-list-friends-i-manipulation-block-plus-friend'></div>" : "";
                    }else if(users[i]['isFriend'] == 2){
                        var  isFriend = "<div class='loading'><div class='ball'></div><div class='ball1'></div></div>";
                    }

                    $('.wp-all-list-friends').append("<div class='wp-all-list-friends-i'><div class='wp-all-list-friends-i-ico'><a href='/profile/"+userId+"/'><img src="+"<?php echo Yii::$app->homeUrl; ?>"+"images/avatar/"+users[i]['id']+"_small.jpg"+" class='ava_class'></a></div><div class='wp-all-list-friends-i-info'><a href='/profile/"+users[i].id+"/' class='all-list-friends-i-info-nickname'>"+users[i]['name']+"</a><p class='all-list-friends-i-info-sity'>"+users[i]['city']+"</p></div><div class='wp-all-list-friends-i-manipulation-block'><a id="+users[i]['id']+" href='#modal-send-message' onclick='modalUse()' class='all-list-friends-i-manipulation-block-message open_modal'></a> "+ status +" "+ isFriend +"  </div><div class='clearboth'></div></div>");

           //  	if (<?=Yii::$app->user->id;?> == users[i].id ){
                    // $('.all-list-friends-i-manipulation-block-message').hide();
                    // $('.all-list-friends-i-manipulation-block-plus-friend').hide();
              //   }
            }

//                            $('img').each(function(){
//                                var self = this;
//                                var img = new Image();
//                                img.src = this.src;
//                                img.onerror = function() {
//                                    $(self).attr('src', '/images/avatar/def_avatar.jpg');
//                                }
//                            }); 
            $('.ava_class').error(function() {
                $(this).attr( "src", "<?php echo Yii::$app->homeUrl; ?>"+"images/avatar/def_avatar.jpg" );
            });
            //пересылаем id пользователя	
                $('.all-list-friends-i-manipulation-block-message').on('click',function(){
                    var userId = $(this).attr('id');
                    $('.content-send-message > .message-area > .foot-modal > .send').attr('id',userId);
                });
            //////заявкa в друзья
            $('.all-list-friends-i-manipulation-block-plus-friend').on('click', function(){ 
                var data = {
                    idAdd : $(this).attr('id'),
                };
                //console.log(data);
                $.ajax({
                    dataType: 'JSON',
                    type : 'get',
                    data : data,
                    url: '/friends/request/'
                }).then(function(data){
                    //console.log(data);
                    alert('Заявка на добавление успешно отправлена!')

                });
            });
        }
        titleInit();
    });
    $('.wrapper-list-input-checkbox').on('change', function(){
        // Пробрасываем в глобальную область видимости для корректной работы скролла
        window.page = 1;
        window.loadMore = true;

        var newCountry = $("#country option:selected").val();
        if ( newCountry == country ) {
            city = $("#city option:selected").val();
        } else {
            country = newCountry;
            $( "#city").parent().find('p').html("-Не выбрано");
            city = 0;
        }
        var data = {
            search : $("#search").val(),
            sort : $("#sort option:selected").val(),
            country : $("#country option:selected").val(),
            city : city,
            age_start : $('#age_start').val(),
            age_end : $('#age_end').val(),
            female : $('#wrapper-flooring-checkbox-female').prop("checked"),
            male : $('#wrapper-flooring-checkbox-male').prop("checked"),
            culture : $("#culture option:selected").val(),
            foto : $('#wp-list-checkbox-search-friend-foto').prop("checked"),
            online : $('#wp-list-checkbox-search-friend-online').prop("checked"),
            page : 1,
        };

        //console.log(data);

        $.ajax({
            dataType: 'JSON',
            type : 'get',
            data : data,
            url: '/search/list/'
        }).then(function(data){

//                            $('img').error(function() {
//                                $(this).attr( "src", "<?php echo Yii::$app->homeUrl; ?>"+"images/avatar/def_avatar.jpg" );
//                            });
//		                    console.log(data);
            if (data.users){
                $('.wp-all-list-friends-i').remove();
                var users = data.users;
                var count = users.length;

                for (var i = 0 ; i < count; i++ ){
                    var userId = users[i].id;
                        if (users[i]['onlineInd'] == 1) {
                            var  status = "<div style='background:#00a651' class='all-list-friends-i-manipulation-block-status'></div>";
                        } else {
                            var  status = "<div style='background:red' class='all-list-friends-i-manipulation-block-status'></div>";
                        } 
                        if (users[i]['isFriend'] == 1){
                            var  isFriend = "<div style='color:green'>&#10004;</div>";
                        }else if(users[i]['isFriend'] == 0){
                            var  isFriend = (users[i]['id'] != <?=Yii::$app->user->id?>) ? "<div id="+users[i]['id']+" class='all-list-friends-i-manipulation-block-plus-friend'></div>" : "";
                        }else if(users[i]['isFriend'] == 2){
                            var  isFriend = "<div class='loading'><div class='ball'></div><div class='ball1'></div></div>";
                        }
                        $('.wp-all-list-friends').append("<div class='wp-all-list-friends-i'><div class='wp-all-list-friends-i-ico'><a href='/profile/"+userId+"/'><img src="+"<?php echo Yii::$app->homeUrl; ?>"+"images/avatar/"+users[i]['id']+"_small.jpg"+" class='ava_class'></a></div><div class='wp-all-list-friends-i-info'><a href='/profile/"+users[i].id+"/' class='all-list-friends-i-info-nickname'>"+users[i]['name']+"</a><p class='all-list-friends-i-info-sity'>"+users[i]['city']+"</p></div><div class='wp-all-list-friends-i-manipulation-block'><a id="+users[i]['id']+" href='#modal-send-message' onclick='modalUse()' class='all-list-friends-i-manipulation-block-message open_modal'></a> "+ status +" "+ isFriend +"  </div><div class='clearboth'></div></div>");
            // 		if (<?=Yii::$app->user->id;?> == users[i].id ){
                        // $('.all-list-friends-i-manipulation-block-message').hide();
                        // $('.all-list-friends-i-manipulation-block-plus-friend').hide();
                  //   }
                }
                $('.ava_class').error(function() {
                    $(this).attr( "src", "<?php echo Yii::$app->homeUrl; ?>"+"images/avatar/def_avatar.jpg" );
                });

            /////пересылаем id пользователя
                $('.all-list-friends-i-manipulation-block-message').on('click',function(){
                    var userId = $(this).attr('id');
                    $('.content-send-message > .message-area > .foot-modal > .send').attr('id',userId);
                });
            //////заявкa в друзья
                $('.all-list-friends-i-manipulation-block-plus-friend').off('click').on('click', function(){
                    var data = {
                        idAdd : $(this).attr('id'),
                    };
                    //console.log(data);
                    $.ajax({
                        dataType: 'JSON',
                        type : 'get',
                        data : data,
                        url: '/friends/request/'
                    }).then(function(data){
                        //console.log(data);
                        alert('Заявка на добавление успешно отправлена!')
                    });	
                });
            }
        });
    });

        $(window).on('scroll', function(){
            if($(window).scrollTop() == $(document).height() - $(window).height() && loadMore == true){
                page = page + 1;
                $('div#loadmoreajaxloader').show();
                
                var newCountry = $("#country option:selected").val();
                if ( newCountry == country ) {
                    city = $("#city option:selected").val();
                } else {
                    country = newCountry;
                    $( "#city").parent().find('p').html("-Не выбрано");
                    city = 0;
                }
                var data = {
                    search : $("#search").val(),
                            sort : $("#sort option:selected").val(),
                            country : $("#country option:selected").val(),
                            city : city,
                            age_start : $('#age_start').val(),
                            age_end : $('#age_end').val(),
                            female : $('#wrapper-flooring-checkbox-female').prop("checked"),
                            male : $('#wrapper-flooring-checkbox-male').prop("checked"),
                            culture : $("#culture option:selected").val(),
                            foto : $('#wp-list-checkbox-search-friend-foto').prop("checked"),
                            online : $('#wp-list-checkbox-search-friend-online').prop("checked"),
                            page : page,
                };
                console.log(data);
                $.ajax({
                    dataType: 'JSON',
                    type : 'get',
                    data : data,
                    url: '/search/list/',
                    success: function(data){
//                        console.log(data);
                        loadMore = data.loadMore;
//                        console.log('loadMore');
//                        console.log(loadMore);

                       if (data.users){
	                   	   var users = data.users;
	                   	   var count = users.length;

	                    	for (var i = 0 ; i < count; i++ ){
	                    		var userId = users[i].id;
	                    		 	if (users[i]['onlineInd'] == 1){
	                    		 	 	var  status = "<div style='background:#00a651' class='all-list-friends-i-manipulation-block-status'></div>";
	                    		 	}else{
	                    		 		var  status = "<div style='background:red' class='all-list-friends-i-manipulation-block-status'></div>";
	                    		 	} 

	                    		 	if (users[i]['isFriend'] == 1){
	                    		 		var  isFriend = "<div style='color:green'>&#10004;</div>";
	                    		 	}else if(users[i]['isFriend'] == 0){
	                    		 		var  isFriend = (users[i]['id'] != <?=Yii::$app->user->id?>) ? "<div id="+users[i]['id']+" class='all-list-friends-i-manipulation-block-plus-friend'></div>" : "";
	                    		 	}else if(users[i]['isFriend'] == 2){
	                    		 		var  isFriend = "<div class='loading'><div class='ball'></div><div class='ball1'></div></div>";
	                    		 	}


	                    			$('.wp-all-list-friends').append("<div class='wp-all-list-friends-i'><div class='wp-all-list-friends-i-ico'><a href='/profile/"+userId+"/'><img src="+"<?php echo Yii::$app->homeUrl; ?>"+"images/avatar/"+users[i]['id']+"_small.jpg"+" class='ava_class'></a></div><div class='wp-all-list-friends-i-info'><a href='/profile/"+users[i].id+"/' class='all-list-friends-i-info-nickname'>"+users[i]['name']+"</a><p class='all-list-friends-i-info-sity'>"+users[i]['city']+"</p></div><div class='wp-all-list-friends-i-manipulation-block'><a id="+users[i]['id']+" href='#modal-send-message' onclick='modalUse()' class='all-list-friends-i-manipulation-block-message open_modal'></a> "+ status +" "+ isFriend +"  </div><div class='clearboth'></div></div>");

	                    // 		if (<?=Yii::$app->user->id;?> == users[i].id ){
	                				// $('.all-list-friends-i-manipulation-block-message').hide();
	                				// $('.all-list-friends-i-manipulation-block-plus-friend').hide();
			                  //   }
	                    	}
                            
                            $('.ava_class').error(function() {
                                $(this).attr( "src", "<?php echo Yii::$app->homeUrl; ?>"+"images/avatar/def_avatar.jpg" );
                            });
	                    				
	                    	//пересылаем id пользователя
		                    	$('.all-list-friends-i-manipulation-block-message').on('click',function(){
		                    		var userId = $(this).attr('id');
		                    		$('.content-send-message > .message-area > .foot-modal > .send').attr('id',userId);
		                    	});
							//////заявкa в друзья
							$('.all-list-friends-i-manipulation-block-plus-friend').off('click').on('click', function(){
								var data = {
									idAdd : $(this).attr('id'),
					            };

								console.log(data);

					            $.ajax({
					                dataType: 'JSON',
					                type : 'get',
					                data : data,
					                url: '/friends/request/'
					           	}).then(function(data){

					                console.log(data);
					                alert('Заявка на добавление успешно отправлена!')
					            });
					        });

	                    }

                    }
                });
            }
	 titleInit();
        });


				});



			</script>
		</div>
	</div>
	<div class="wrapper-tabs-friends">
		<div class="friends-tabs_container">
		  <ul class="friends-tabs">
		    <li class="inl-bl active-tabs">Поиск друзей</li>
		    
		    <div class="clearboth"></div>
		  </ul>
		  <div class="friends-tab_container" style="display: block;">
		    <div class="wp-all-list-friends">

<!-- Этот блок будем строить в цикле -->
	<!-- 
		    	<div class="wp-all-list-friends-i">
		    		<div class="wp-all-list-friends-i-ico"><img src="/css/img/user-ava1.png"></div>
		    		<div class="wp-all-list-friends-i-info">
		    			<a href="#" class="all-list-friends-i-info-nickname">Kolobok</a>
		    			<p class="all-list-friends-i-info-sity">Киев</p>
		    		</div>
		    		<div class="wp-all-list-friends-i-manipulation-block">
		    			<a href="#" class="all-list-friends-i-manipulation-block-message"></a>
		    			<div class="all-list-friends-i-manipulation-block-status"></div>
		    			<div id="АЙДИ ЮЗЕРА" class="all-list-friends-i-manipulation-block-plus-friend"></div>
		    		</div>
		    		<div class="clearboth"></div>
		    	</div> -->
		    	
<!-- Этот блок будем строить в цикле -->
		    	
		    	<div class="clearboth"></div>
		    </div>
		  </div>
		  <div class="friends-tab_container">
		    
		  </div>
		  <div class="friends-tab_container">
		    
		    </div>
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

        $('#country').on( 'change', function(){
            $.post( "/board/city/?id="+$(this).val(), function( data ){
                $( "#city" ).html( data );
                $( "#city").parent().find('p').html("-Не выбрано");
                $( "#city option[value='0']" ).prop( "selected", true );
            });
        });
    
	  $('ul.friends-tabs').each(function() {
	    $(this).find('li').each(function(i) {
	      $(this).click(function() {
	        $(this).addClass('active-tabs').siblings().removeClass('active-tabs');
	        var p = $(this).parents('div.friends-tabs_container');
	        p.find('div.friends-tab_container').hide();
	        p.find('div.friends-tab_container:eq(' + i + ')').show();
	      });
	    });
	  });


		//Отправка сообщение позьзователю
		$('body').on('keydown', function(e) {
			if (e.ctrlKey && e.keyCode == 13) {
				$('.content-send-message > .message-area > .foot-modal > .send').trigger('click');
			}
		});

		$('.content-send-message > .message-area > .foot-modal > .send').on('click', function(){
			var self = $(this);
			var message = $(this).parent().parent().children("textarea").val();
			var data = {
				user : $(this).attr('id'),
				message : message,
			};
			//console.log(data);

			$.ajax({
				dataType: 'JSON',
				type : 'get',
				data : data,
				url: "/chat/sendmessage/"
			}).then(function(data){
                //console.log(data);
				if ( data.error ){
                    alert(data.message);
                    $('#modal-send-message').css('opacity', '0');
                    $('#modal-send-message').css('display', 'none');
                    $('#overlay').css('display', 'none');
                } else {
					self.closest('.modal-send-message').find('.modal_close').trigger('click');
					self.closest('.modal-send-message').find('textarea').val('');
				}
			});
		});

		titleInit();

	});
</script>	
 <div id="modal-send-message" class="modal-send-message">
        <div class="modal_close"> <h1>Написать сообщение</h1><div id="close_for_modal" class="close-foto"></div></div>
        <div class="content-send-message">
        	<div class="user-ava">
        	</div>
        	<div class="message-area">
        		<textarea type="text" placeholder="Введите ваше сообщение"></textarea>
        		<div class="foot-modal"><input id="" class="send" type="button" value="Отправить"></div>
        	</div>
        </div>

    </div>
    <div id="overlay"></div><!-- Подложка -->
