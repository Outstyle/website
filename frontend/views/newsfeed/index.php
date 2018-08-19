<?php
use yii\helpers\Html; 
use yii\helpers\Url;
use frontend\models\FriendRequests;
use frontend\models\UserAvatar;
use frontend\models\UserDescription;
use frontend\models\Country;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Новости');
$this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Новости'), 'url' => Url::toRoute('friends/')];
$this->params['breadcrumbs'][] = Yii::t('app', 'Новости');
?>
<div class="wrapper-friends-main news-uzer">
  <div class="content-menu"></div>
  <div class="wrapper-friends-sidebar">
    <div class="wrapper-friends-sidebar-i">
      <div class="wp-search-friend-block">
        <input type="search" class="search-field-friend" >
        <input type="submit" value="" class="search-button-friend">
      </div>
      <div class="friends-sidebar-link-list"> 
        <ul>
          <li title="Друзья"><a href="<?=Url::toRoute('/myprofile/friends/');?>"></a></li>
          <li title="Поиск друзей"><a href="<?=Url::toRoute('/search/');?>" ></a></li>
          <li title="Мои новости"><a href="<?=Url::toRoute('/myprofile/newsfeed');?>" class="friends-sidebar-active"></a></li>
          <!-- <li><a href="<?//=Url::toRoute('/groups/');?>" ></a></li> -->
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
          <div class="wrapper-flooring-checkbox-i">
            <input id="wrapper-flooring-checkbox-male" type="checkbox">
            <label for="wrapper-flooring-checkbox-male"><span></span>С видео</label>
            <div class="clearboth"></div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="wrapper-tabs-friends">
    <div class="groups-tabs_container">
      <ul class="groups-tabs">
        <li class="inl-bl active-tabs">Новости</li>
        <!-- <li class="inl-bl">Обновления</li>
        <li class="inl-bl">Коментарии</li> -->
        <div class="clearboth"></div>
      </ul>
      <div class="groups-tab_container cont123" style="display: block;">
        <? if (empty($modelsNewsFriends)){ ?>
          <div class="new-uzer news-no">
            У Ваших друзей еще нет обновлений<br>
          </div>
          <? } ?>
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

  var z = 0;
  var m = 0;
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


          //вывод постов
        var data = {
                    idOwner : <?=Yii::$app->user->id?>,
                    page : 1
                };
                console.log(data);
                $.ajax({
                    dataType: 'JSON',
                    type : 'get',
                    data : data,
                    url: "/newsfeed/showfriendsnews/",
                }).then(function(data){
                    console.log(data)
                    if (data.modelsNewsFriends){
                        var count = data.modelsNewsFriends.length;
                        for (var i = 0; i < count; i++) {
                            var post = data.modelsNewsFriends[i];
                            var attachment = post.attachment;
                            var coments = post.comments;
                            var likeActive2 = (post['myLike'] == 1) ?  'likeActive2' :  '';

                            var full = "";

                            $('.groups-tab_container').append("<div class='news-post' id='post-"+post.id+"'><div id="+post.id+" data-userId="+post.idOwner+" class='delete-user-post'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/Delete-user-comments.png"+"></div><div class='user-ava-post ava_height'><a href='/profile/"+post.idOwner+"/'><img class='user-ava-img' src="+"<?php echo Yii::$app->homeUrl;?>"+"images/avatar/"+post.idOwner+"_small.jpg"+"></a></div><div class='mesage-name-area js-area'><a href='/profile/"+post.idOwner+"/'><div class='name-user-post'>"+post.nameOwner+"</div></a><div class='time-user-post'>"+post.timeRecord+"</div><div class='message-post'><p class='ttt' id='"+ 'js-' + z +"'>"+post.text+"</p></div>"+ full +"<div class='like-share'><div class='post-share share' id="+post.id+">" +
                              "</div><div id="+post.id+" class='post-like "+likeActive2+"'><div class='like-Count'>"+post.likeCount+"</div></div></div><div class='show-all-coment'>Показать все <span class='coment-count-news'>"+post.commentsCount+"</span> <span class='word'></span></div><div class='hide-all-coments'>Cкрыть все отзывы</div><div class='all-comments'></div><div class='write-message'><div class='ava-user'><img src=" + "<?php echo Yii::$app->homeUrl; ?>" + "images/avatar/<?=Yii::$app->user->id?>_small.jpg" + " alt=''></div><div class='message-area'>" +
                              "<textarea id='mess-user-index' class='coment-message' rows='1'></textarea>" +
                              "<div id="+post.id+" class='send-icon'></div></div></div></div>");

                            if (attachment != null) {
                               if (attachment.type == 'photo') {
                                   $('#post-' + post.id+' .mesage-name-area > .message-post').append("<div class='foto-in-album'><img src=" + "<?php echo Yii::$app->homeUrl; ?>" + "images/photo/" + attachment[0].user + "/" + attachment[0].album + "/normal_" + attachment[0].img + "></div>");
                               }
                               if (attachment.type == 'video') {
                                   $('#post-' + post.id+' .mesage-name-area > .message-post').append("<div class='video-title'>" + attachment.modelVideo.title + "</div><iframe width='528' height='250' src=" + attachment.modelVideo.url_iframe + " frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>");
                               }
                            }

                            for (var x=0 ; x < coments.length; x++) {
                                var coment = coments[x];
                                var likeActive2 = (coment['myLike'] == 1) ?  'likeActive2' :  '';
                                var del = "";

                                var ComText = getBrString(coment.comment);

                                if("<?=Yii::$app->user->id;?>" == coment.user_id){
                                  del = "<div id="+coment.id+" class='del-coment'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/close.png"+"></div>";
                                }

                                $('#post-' + post.id+' .mesage-name-area > .all-comments').append(" <div class='coment'><div class='user-ava'><a href='/profile/"+coment.user_id+"/'><img class='user-ava-img' src=" + "<?php echo Yii::$app->homeUrl; ?>" + "images/avatar/" + coment.user_id + "_small.jpg" + " alt=''></a></div><div class='info-user'>"+ del +"<div class='for-user-vs-time'><a href='/profile/"+coment.user_id+"/'><div class='user-name'>"+coment.user_name+"</div></a><div class='time'>"+coment.created+"</div></div><div class='user-message'>"+ComText+"</div><div class='reply'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/Share_ICO.png"+"></div><div id="+coment.id+" class='like "+likeActive2+"'><div class='like-Count'>"+coment.likeCount+"</div></div></div></div>");
                            }
                            var thisShowEl = $('#post-' + post.id+' .mesage-name-area > .show-all-coment');
                            var thisHideEl = $('#post-' + post.id+' .mesage-name-area > .hide-all-coments');

                            if ( post.commentsCount <= 2) {
                                $(thisShowEl).hide();
                            } else {
                                $(thisShowEl).show();
                                $('#post-' + post.id+' .mesage-name-area > .all-comments > .coment').hide();
                                $('#post-' + post.id+' .mesage-name-area > .all-comments > .coment:last').show();
                                $('#post-' + post.id+' .mesage-name-area > .all-comments > .coment:last').prev().show();
                            } 

                            $('.show-all-coment').on("click",function(){
                                $(this).parent().children('.all-comments').children('.coment').show();
                                $(this).parent().children('.hide-all-coments').show();
                                $(this).hide();
                            });

                             $('.hide-all-coments').on("click",function(){
                                $(this).parent().children('.all-comments').children('.coment').hide();
                                $(this).hide();
                                $(this).parent().children('.all-comments').children('.coment:last').show();
                                $(this).parent().children('.all-comments').children('.coment:last').prev().show();
                                $(this).parent().children('.show-all-coment').show();
                            });

                            var num = post.commentsCount;
                            num.toString();
                            var numLast = num.charAt(num.length - 1);
                            var numLasts = num.slice(- 2);

                            if (numLasts == 11 || numLasts == 12 || numLasts == 13 || numLasts == 14) {
                                $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзывов"); 
                            } 
                            else {
                                if (numLast == 2 || numLast == 3 || numLast == 4) {
                                    $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзыва");
                                } else if (numLast == 0 || numLast >= 5) {
                                    $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзывов");
                                } else {
                                    $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзыв");
                                }
                            };
                        }
                        $('.user-ava-img').error(function() {
                            $(this).attr( "src", "http://devoutstyle.org/images/avatar/def_avatar.jpg" );
                        });

                        addMoreTextBtn( $('.message-post'), 5, 120 );
           
                        console.log(data);

          /// Лайки для коментариев
          $('.like').on('click', function(event){
              event.stopPropagation();
              var likeIns =  $(this).find(".like-Count");
              var like = $(this);
              var data = {
                      id : $(this).attr('id'),
                      elem_type : "comments"
              };
              // console.log(data);
              $.ajax({
                      dataType: 'JSON',
                      type : 'get',
                      data : data,
                      url: "/newsfeed/like/"
              }).then(function(data){
                      console.log(data);
                      var likeCount = data.likeCount;
                      var myLike = data.myLike;
                      (myLike) ? like.addClass('likeActive2') : like.removeClass('likeActive2');
                      likeIns.html(likeCount);

              });
          });
           //// Удаление комментария
           $('.del-coment').on('click', function(){
              var thisComent = $(this).parent().parent();
              var data = {
                  idComment : $(this).attr('id')
              };
              console.log(data);
              $.ajax({
                      dataType: 'JSON',
                      type : 'get',
                      data : data,
                      url: "/newsfeed/delcomment/"
              }).then(function(data){
                      console.log(data);
                      $(thisComent[0]).fadeOut('slow');

              });
          });
                              var num = post.commentsCount;
                        num.toString();
                        var numLast = num.charAt(num.length - 1);
                        var numLasts = num.slice(- 2);

                        if (numLasts == 11 || numLasts == 12 || numLasts == 13 || numLasts == 14) {
                            $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзывов"); 
                        } 
                        else {
                            if (numLast == 2 || numLast == 3 || numLast == 4) {
                                $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзыва");
                            } else if (numLast == 0 || numLast >= 5) {
                                $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзывов");
                            } else {
                                $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзыв");
                            }
                        };
                        
                    }
             


                  

          /// Лайки для коментариев
          $('.like').on('click', function(event){
              // var thisComent = $(this).parent();
              event.stopPropagation();
              var likeIns =  $(this).find(".like-Count");
              var like = $(this);
              var data = {
                      id : $(this).attr('id'),
                      elem_type : "comments"
              };
              // console.log(data);
              $.ajax({
                      dataType: 'JSON',
                      type : 'get',
                      data : data,
                      url: "/newsfeed/like/"
              }).then(function(data){
                      console.log(data);
                      var likeCount = data.likeCount;
                      var myLike = data.myLike;
                      (myLike) ? like.addClass('likeActive2') : like.removeClass('likeActive2');
                      likeIns.html(likeCount);

              });
          });
           //// Удаление комментария
           $('.del-coment').on('click', function(){
              var thisComent = $(this).parent().parent();
              var data = {
                  idComment : $(this).attr('id')
              };
              console.log(data);
              $.ajax({
                      dataType: 'JSON',
                      type : 'get',
                      data : data,
                      url: "/newsfeed/delcomment/"
              }).then(function(data){
                      console.log(data);
                      $(thisComent[0]).fadeOut('slow');

              });
          });
//Лайк поста
 $('.post-like').on('click', function(event){
    event.stopPropagation();
    var likeIns =  $(this).find(".like-Count");
    var like = $(this);

    var data = {
            id : $(this).attr('id'),
            elem_type : "board"
    };
    $.ajax({
            dataType: 'JSON',
            type : 'get',
            data : data,
            url: "/newsfeed/like/"
    }).then(function(data){
            console.log(data);
            var likeCount = data.likeCount;
            var myLike = data.myLike;
            (myLike) ? like.addClass('likeActive2') : like.removeClass('likeActive2');
            likeIns.html(likeCount);

    });
});
//Удаление поста Фида
$('.delete-user-post').on('click', function(){ 
    var thisFriend = $(this).parent();
    var data = {
            elem_id : $(this).attr('id') ,
            elem_type : 'board',
            user_id : $(this).attr('data-userId'), 
    };
    console.log(data);
    $.ajax({
            dataType: 'JSON',
            type : 'get',
            data : data,
            url: '/newsfeed/del/'
    }).then(function(data){
        console.log(data);
        $(thisFriend).fadeOut('slow');
    });
});
      //Добавление коментария
        $('.send-icon').off().on('click', function(){
                var commentMessage = $(this).parent(".message-area").children('textarea').val();
                var thisComent = $(this).parent().parent().parent().find(".all-comments");

                if ($.trim(commentMessage) == "") return;

                var data = {
                        idPost : $(this).attr('id'),
                        text : commentMessage
                };
                // console.log(data);
                $.ajax({
                        dataType: 'JSON',
                        type : 'get',
                        data : data,
                        url: "/newsfeed/comment/"
                }).then(function(data){
                    var ComText = getBrString(data.comment);

                    $(thisComent).append("<div class='coment'><div class='user-ava'><a href='/profile/"+coment.user_id+"/'><img class='user-ava-img' src="+"<?php echo Yii::$app->homeUrl; ?>images/avatar/"+data.user_id+"_small.jpg"+" alt=''></a></div><div class='info-user'><div id="+data.idComment+" class='del-coment'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/close.png"+"></div><div class='for-user-vs-time'><a href='/profile/"+data.user_id+"/'><div class='user-name'>"+data.user_name+"</div></a><div class='time'>"+data.created+"</div></div><div class='user-message'>"+ComText+"</div><div class='reply'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/Share_ICO.png"+"></div><div id="+data.idComment+" class='like'><div class='like-Count'>"+data.likeCount+"</div></div></div></div>");
                    $(".message-area > textarea").val(" ");
                    
                    $('.user-ava-img').error(function() {
                        $(this).attr( "src", "http://devoutstyle.org/images/avatar/def_avatar.jpg" );
                    });

                           /// Лайки для коментариев
                              $('.like').off();
                                $('.like').on('click', function(event){
                                    event.stopPropagation();
                                    var likeIns =  $(this).find(".like-Count");
                                    var like = $(this);
                                    var data = {
                                            id : $(this).attr('id'),
                                            elem_type : "comments"
                                    };
                                    // console.log(data);
                                    $.ajax({
                                            dataType: 'JSON',
                                            type : 'get',
                                            data : data,
                                            url: "/newsfeed/like/"
                                    }).then(function(data){
                                            console.log(data);
                                            var likeCount = data.likeCount;
                                            var myLike = data.myLike;
                                            (myLike) ? like.addClass('likeActive2') : like.removeClass('likeActive2');
                                            likeIns.html(likeCount);

                                    });
                                });
                                //// Удаление комментария
                                 $('.del-coment').on('click', function(){
                                    var thisComent = $(this).parent().parent();
                                    var data = {
                                        idComment : $(this).attr('id')
                                    };
                                    console.log(data);
                                    $.ajax({
                                            dataType: 'JSON',
                                            type : 'get',
                                            data : data,
                                            url:  "/newsfeed/delcomment/"
                                    }).then(function(data){
                                            console.log(data);
                                            $(thisComent[0]).fadeOut('slow');

                                    });
                                });


                            });
                        });


                titleInit();
                  textareaInit();

                });




//infinete scroll 

var page = 1;
var loadMore = true;
$(window).on('scroll', function(){

            if($(window).scrollTop() == $(document).height() - $(window).height() && loadMore == true){

                page = page + 1;
                // $('div#loadmoreajaxloader').show();
                var data = {
                    idOwner : <?=Yii::$app->user->id?>,
                    page : page
                };
                console.log(data);
                $.ajax({
                    dataType: 'JSON',
                    type : 'get',
                    data : data,
                    url: "/newsfeed/showfriendsnews/",
                    success: function(data){




                          if (data.modelsNewsFriends){
                    var count = data.modelsNewsFriends.length;
                    for (var i = 0; i < count; i++) {
                        var post = data.modelsNewsFriends[i];
                        var attachment = post.attachment;
                        var coments = post.comments;
                        var likeActive2 = (post['myLike'] == 1) ?  'likeActive2' :  '';


                        var full = '';

                        $('.groups-tab_container').append("<div class='news-post' id='post-"+post.id+"'><div id="+post.id+" data-userId="+post.idOwner+" class='delete-user-post'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/Delete-user-comments.png"+"></div><div class='user-ava-post ava_height'><a href='/profile/"+post.idOwner+"/'><img class='user-ava-img' src="+"<?php echo Yii::$app->homeUrl;?>"+"images/avatar/"+post.idOwner+"_small.jpg"+"></a></div><div class='mesage-name-area js-area'><a href='/profile/"+post.idOwner+"/'><div class='name-user-post'>"+post.nameOwner+"</div></a><div class='time-user-post'>"+post.timeRecord+"</div><div class='message-post'><p class='ttt' id='"+ 'js-' + z +"'>"+post.text+"</p></div>"+ full +"<div class='like-share'><div class='post-share share' id="+post.id+">" +
                        "</div><div id="+post.id+" class='post-like "+likeActive2+"'><div class='like-Count'>"+post.likeCount+"</div></div></div><div class='show-all-coment'>Показать все <span class='coment-count-news'>"+post.commentsCount+"</span> <span class='word'></span></div><div class='hide-all-coments'>Cкрыть все отзывы</div><div class='all-comments'></div><div class='write-message'><div class='ava-user'><img src=" + "<?php echo Yii::$app->homeUrl; ?>" + "images/avatar/<?=Yii::$app->user->id?>_small.jpg" + " alt=''></div><div class='message-area'>" +
                        "<textarea id='mess-user-index' class='coment-message' rows='1'></textarea>" +
                        "<div id="+post.id+" class='send-icon'></div></div></div></div>");

                        if (attachment != null) {
                           if (attachment.type == 'photo') {
                               $('#post-' + post.id+' .mesage-name-area > .message-post').append("<div class='foto-in-album'><img src=" + "<?php echo Yii::$app->homeUrl; ?>" + "images/photo/" + attachment[0].user + "/" + attachment[0].album + "/normal_" + attachment[0].img + "></div>");
                           }
                           if (attachment.type == 'video') {
                               $('#post-' + post.id+' .mesage-name-area > .message-post').append("<div class='video-title'>" + attachment.modelVideo.title + "</div><iframe width='528' height='250' src=" + attachment.modelVideo.url_iframe + " frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>");
                           }
                        }

                        for (var x=0 ; x < coments.length; x++) {
                            var coment = coments[x];
                            var likeActive2 = (coment['myLike'] == 1) ?  'likeActive2' :  '';
                            var ComText = getBrString(coment.comment);

                            var del = "";

                            if("<?=Yii::$app->user->id;?>" == coment.user_id){
                                del = "<div id="+coment.id+" class='del-coment'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/close.png"+"></div>";
                            }

                            $('#post-' + post.id+' .mesage-name-area > .all-comments').append(" <div class='coment'><div class='user-ava'><a href='/profile/"+coment.user_id+"/'><img class='user-ava-img' src=" + "<?php echo Yii::$app->homeUrl; ?>" + "images/avatar/" + coment.user_id + "_small.jpg" + " alt=''></a></div><div class='info-user'>"+ del +"<div class='for-user-vs-time'><a href='/profile/"+coment.user_id+"/'><div class='user-name'>"+coment.user_name+"</div></a><div class='time'>"+coment.created+"</div></div><div class='user-message'>"+ComText+"</div><div class='reply'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/Share_ICO.png"+"></div><div id="+coment.id+" class='like "+likeActive2+"'><div class='like-Count'>"+coment.likeCount+"</div></div></div></div>");
                        }
                        var thisShowEl = $('#post-' + post.id+' .mesage-name-area > .show-all-coment');
                        var thisHideEl = $('#post-' + post.id+' .mesage-name-area > .hide-all-coments');

                        if ( post.commentsCount <= 2) {
                            $(thisShowEl).hide();
                        }else{
                            $(thisShowEl).show();
                            $('#post-' + post.id+' .mesage-name-area > .all-comments > .coment').hide();
                            $('#post-' + post.id+' .mesage-name-area > .all-comments > .coment:last').show();
                            $('#post-' + post.id+' .mesage-name-area > .all-comments > .coment:last').prev().show();
                        } 
                        $('.show-all-coment').on("click",function(){
                            $(this).parent().children('.all-comments').children('.coment').show();
                            $(this).parent().children('.hide-all-coments').show();
                            $(this).hide();
                        });
                        $('.hide-all-coments').on("click",function(){
                            $(this).parent().children('.all-comments').children('.coment').hide();
                            $(this).hide();
                            $(this).parent().children('.all-comments').children('.coment:last').show();
                            $(this).parent().children('.all-comments').children('.coment:last').prev().show();
                            $(this).parent().children('.show-all-coment').show();
                        });
                        var num = post.commentsCount;
                        num.toString();
                        var numLast = num.charAt(num.length - 1);
                        var numLasts = num.slice(- 2);

                        if (numLasts == 11 || numLasts == 12 || numLasts == 13 || numLasts == 14) {
                            $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзывов"); 
                        } else {
                            if (numLast == 2 || numLast == 3 || numLast == 4) {
                                $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзыва");
                            } else if (numLast == 0 || numLast >= 5) {
                                $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзывов");
                            } else {
                                $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзыв");
                            }
                        };
                    }
                    $('.user-ava-img').error(function() {
                        $(this).attr( "src", "http://devoutstyle.org/images/avatar/def_avatar.jpg" );
                    });
           
                    if (data.modelsNewsFriends){
                    var count = data.modelsNewsFriends.length;
                    for (var i = 0; i < count; i++) {
                        z++;
                        m++;
                        var post = data.modelsNewsFriends[i];
                        var attachment = post.attachment;
                        var coments = post.comments;
                        var likeActive2 = (post['myLike'] == 1) ?  'likeActive2' :  '';

                        var full = '';

                        $('.groups-tab_container').append("<div class='news-post' id='post-"+post.id+"'><div id="+post.id+" data-userId="+post.idOwner+" class='delete-user-post'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/Delete-user-comments.png"+"></div><div class='user-ava-post ava_height'><a href='/profile/"+post.idOwner+"/'><img class='user-ava-img' src="+"<?php echo Yii::$app->homeUrl;?>"+"images/avatar/"+post.idOwner+"_small.jpg"+"></a></div><div class='mesage-name-area js-area'><a href='/profile/"+post.idOwner+"/'><div class='name-user-post'>"+post.nameOwner+"</div></a><div class='time-user-post'>"+post.timeRecord+"</div><div class='message-post'><p class='ttt' id='"+ 'js-' + z +"'>"+post.text+"</p></div>"+ full +"<div class='like-share'><div class='post-share share' id="+post.id+">" +
                        "</div><div id="+post.id+" class='post-like "+likeActive2+"'><div class='like-Count'>"+post.likeCount+"</div></div></div><div class='show-all-coment'>Показать все <span class='coment-count-news'>"+post.commentsCount+"</span> <span class='word'></span></div><div class='hide-all-coments'>Cкрыть все отзывы</div><div class='all-comments'></div><div class='write-message'><div class='ava-user'><img src=" + "<?php echo Yii::$app->homeUrl; ?>" + "images/avatar/<?=Yii::$app->user->id?>_small.jpg" + " alt=''></div><div class='message-area'>" +
                        "<textarea id='mess-user-index' class='coment-message' rows='1'></textarea>" +
                        "<div id="+post.id+" class='send-icon'></div></div></div></div>");
                        
                        if (attachment != null) {
                            if (attachment.type == 'photo') {
                                $('#post-' + post.id+' .mesage-name-area > .message-post').append("<div class='foto-in-album'><img src=" + "<?php echo Yii::$app->homeUrl; ?>" + "images/photo/" + attachment[0].user + "/" + attachment[0].album + "/normal_" + attachment[0].img + "></div>");
                            }
                            if (attachment.type == 'video') {
                                $('#post-' + post.id+' .mesage-name-area > .message-post').append("<div class='video-title'>" + attachment.modelVideo.title + "</div><iframe width='528' height='250' src=" + attachment.modelVideo.url_iframe + " frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>");
                            }
                        };


                        for (var x=0 ; x < coments.length; x++) {
                            var coment = coments[x];
                            var likeActive2 = (coment['myLike'] == 1) ?  'likeActive2' :  '';
                            var ComText = getBrString(coment.comment);

                            $('#post-' + post.id+' .mesage-name-area > .all-comments').append(" <div class='coment'><div class='user-ava'><a href='/profile/"+coment.user_id+"/'><img src=" + "<?php echo Yii::$app->homeUrl; ?>" + "images/avatar/<?=Yii::$app->user->id?>_small.jpg" + " alt=''></a></div><div class='info-user'><div id="+coment.id+" class='del-coment'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/close.png"+"></div><div class='for-user-vs-time'><a href='/profile/"+coment.user_id+"/'><div class='user-name'>"+coment.user_name+"</div></a><div class='time'>"+coment.created+"</div></div><div class='user-message'>"+ComText+"</div><div class='reply'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/Share_ICO.png"+"></div><div id="+coment.id+" class='like "+likeActive2+"'><div class='like-Count'>"+coment.likeCount+"</div></div></div></div>");
                        }
                        var thisShowEl = $('#post-' + post.id+' .mesage-name-area > .show-all-coment');
                        var thisHideEl = $('#post-' + post.id+' .mesage-name-area > .hide-all-coments');

                        if ( post.commentsCount <= 2) {
                            $(thisShowEl).hide();
                        }else{
                            $(thisShowEl).show();
                            $('#post-' + post.id+' .mesage-name-area > .all-comments > .coment').hide();
                            $('#post-' + post.id+' .mesage-name-area > .all-comments > .coment:last').show();
                            $('#post-' + post.id+' .mesage-name-area > .all-comments > .coment:last').prev().show();
                        }

                        $('.show-all-coment').on("click",function(){
                            $(this).parent().children('.all-comments').children('.coment').show();
                            $(this).parent().children('.hide-all-coments').show();
                            $(this).hide();
                        });

                         $('.hide-all-coments').on("click",function(){
                            $(this).parent().children('.all-comments').children('.coment').hide();
                            $(this).hide();
                            $(this).parent().children('.all-comments').children('.coment:last').show();
                            $(this).parent().children('.all-comments').children('.coment:last').prev().show();
                            $(this).parent().children('.show-all-coment').show();
                        });

                    }
                    $('.user-ava-img').error(function() {
                        $(this).attr( "src", "http://devoutstyle.org/images/avatar/def_avatar.jpg" );
                    });
          /// Лайки для коментариев
          $('.like').off();
          $('.like').on('click', function(event){
              event.stopPropagation();
              var likeIns =  $(this).find(".like-Count");
              var like = $(this);
              var data = {
                      id : $(this).attr('id'),
                      elem_type : "comments"
              };
              // console.log(data);
              $.ajax({
                      dataType: 'JSON',
                      type : 'get',
                      data : data,
                      url: "/newsfeed/like/"
              }).then(function(data){
                      console.log(data);
                      var likeCount = data.likeCount;
                      var myLike = data.myLike;
                      (myLike) ? like.addClass('likeActive2') : like.removeClass('likeActive2');
                      likeIns.html(likeCount);

              });
          });
           //// Удаление комментария
           $('.del-coment').on('click', function(){
              var thisComent = $(this).parent().parent();
              var data = {
                  idComment : $(this).attr('id')
              };
              console.log(data);
              $.ajax({
                      dataType: 'JSON',
                      type : 'get',
                      data : data,
                      url: "/newsfeed/delcomment/"
              }).then(function(data){
                      console.log(data);
                      $(thisComent[0]).fadeOut('slow');

              });
          });
                              var num = post.commentsCount;
                        num.toString();
                        var numLast = num.charAt(num.length - 1);
                        var numLasts = num.slice(- 2);

                        if (numLasts == 11 || numLasts == 12 || numLasts == 13 || numLasts == 14) {
                            $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзывов");
                        }
                        else {
                            if (numLast == 2 || numLast == 3 || numLast == 4) {
                                $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзыва");
                            } else if (numLast == 0 || numLast >= 5) {
                                $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзывов");
                            } else {
                                $('#post-' + post.id+' .mesage-name-area > .show-all-coment > .word').text("отзыв");
                            }
                        };

                        
                              
                    }
           


                  }
          /// Лайки для коментариев
          $('.like').off();
          $('.like').on('click', function(event){
              // var thisComent = $(this).parent();
              event.stopPropagation();
              var likeIns =  $(this).find(".like-Count");
              var like = $(this);
              var data = {
                      id : $(this).attr('id'),
                      elem_type : "comments"
              };
              // console.log(data);
              $.ajax({
                      dataType: 'JSON',
                      type : 'get',
                      data : data,
                      url: "/newsfeed/like/"
              }).then(function(data){
                      console.log(data);
                      var likeCount = data.likeCount;
                      var myLike = data.myLike;
                      (myLike) ? like.addClass('likeActive2') : like.removeClass('likeActive2');
                      likeIns.html(likeCount);

              });
          });
           //// Удаление комментария
           $('.del-coment').on('click', function(){
              var thisComent = $(this).parent().parent();
              var data = {
                  idComment : $(this).attr('id')
              };
              console.log(data);
              $.ajax({
                      dataType: 'JSON',
                      type : 'get',
                      data : data,
                      url: "/newsfeed/delcomment/"
              }).then(function(data){
                      console.log(data);
                      $(thisComent[0]).fadeOut('slow');

              });
          });
//Лайк поста
  $('.post-like').off();
 $('.post-like').on('click', function(event){
    event.stopPropagation();
    var likeIns =  $(this).find(".like-Count");
    var like = $(this);
    var data = {
            id : $(this).attr('id'),
            elem_type : "board"
    };
    // console.log(data);
    $.ajax({
            dataType: 'JSON',
            type : 'get',
            data : data,
            url: "/newsfeed/like/"
    }).then(function(data){
            console.log(data);
            var likeCount = data.likeCount;
          var myLike = data.myLike;
          (myLike) ? like.addClass('likeActive2') : like.removeClass('likeActive2');
            likeIns.html(likeCount);

    });
});
//Удаление поста Фида
$('.delete-user-post').on('click', function(){ 
    var thisFriend = $(this).parent();
    var data = {
            elem_id : $(this).attr('id') ,
            elem_type : 'board',
            user_id : $(this).attr('data-userId'), 
    };
    console.log(data);
    $.ajax({
            dataType: 'JSON',
            type : 'get',
            data : data,
            url: '/newsfeed/del/'
    }).then(function(data){
        console.log(data);
        $(thisFriend).fadeOut('slow');
    });
});
      //Добавление коментария
      $('.send-icon').off();
        $('.send-icon').on('click', function(){
                var commentMessage = $(this).parent('.message-area').children('textarea').val();
                var thisComent = $(this).parent().parent().parent().children(".all-comments");

                if ($.trim(commentMessage) == "") return;

                var data = {
                        idPost : $(this).attr('id'),
                        text : commentMessage
                };
                // console.log(data);
                $.ajax({
                        dataType: 'JSON',
                        type : 'get',
                        data : data,
                        url: "/newsfeed/comment/"
                }).then(function(data) {
                    console.log(data);
                    var ComText = getBrString(data.comment);
                    console.log(ComText);

                    $(thisComent).append("<div class='coment'><div class='user-ava'><a href='/profile/"+data.user_id+"/'><img class='user-ava-img' src="+"<?php echo Yii::$app->homeUrl; ?>images/avatar/"+data.user_id+"_small.jpg"+" alt=''></a></div><div class='info-user'><div id="+data.idComment+" class='del-coment'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/close.png"+"></div><div class='for-user-vs-time'><a href='/profile/"+data.user_id+"/'><div class='user-name'>"+data.user_name+"</div></a><div class='time'>"+data.created+"</div></div><div class='user-message'>"+ComText+"</div><div class='reply'><img src="+"<?php echo Yii::$app->homeUrl;?>"+"css/img/Share_ICO.png"+"></div><div id="+data.idComment+" class='like'><div class='like-Count'>"+data.likeCount+"</div></div></div></div>");
                    $(".message-area > textarea").val(" ");
                    
                    $('.user-ava-img').error(function() {
                        $(this).attr( "src", "http://devoutstyle.org/images/avatar/def_avatar.jpg" );
                    });

                           /// Лайки для коментариев
                              $('.like').off();
                                $('.like').on('click', function(event){
                                    event.stopPropagation();
                                    var likeIns =  $(this).find(".like-Count");
                                    var like = $(this);
                                    var data = {
                                            id : $(this).attr('id'),
                                            elem_type : "comments"
                                    };
                                    // console.log(data);
                                    $.ajax({
                                            dataType: 'JSON',
                                            type : 'get',
                                            data : data,
                                            url: "/newsfeed/like/"
                                    }).then(function(data){
                                            console.log(data);
                                            var likeCount = data.likeCount;
                                            var myLike = data.myLike;
                                            (myLike) ? like.addClass('likeActive2') : like.removeClass('likeActive2');
                                            likeIns.html(likeCount);

                                    });
                                });
                                //// Удаление комментария
                                 $('.del-coment').on('click', function(){
                                    var thisComent = $(this).parent().parent();
                                    var data = {
                                        idComment : $(this).attr('id')
                                    };
                                    console.log(data);
                                    $.ajax({
                                            dataType: 'JSON',
                                            type : 'get',
                                            data : data,
                                            url:  "/newsfeed/delcomment/"
                                    }).then(function(data){
                                            console.log(data);
                                            $(thisComent[0]).fadeOut('slow');

                                    });
                                });

                            });
                        });

                      addMoreTextBtn( $('.message-post'), 5, 120 );
                    }
                });
              
            }

   titleInit();
   textareaInit();

        });

  sendCtrlEnter('.send-icon');


 
//end infinete scroll


$(document).ready(function() { 
  var heightDiv = $('.content').height();
  $(".wrapper-friends-sidebar").height(heightDiv + 30);

});





// $(document).ready(function() { 
//  var heightNews = $('.js-area').height();
//  // console.log(heightDiv);
//  $(".js-area").height(heightNews + 29);
// });
  // wrapper-tabs-friends
  var heightDiv = $('.content').height();
  $(".wrapper-friends-sidebar");

  // $(window).scroll();

  textareaInit();

  //// Репост
  $(document).on('click', '.share', function(){
    var postText = $(this).closest('.js-area').find('.message-post').text();
    var postID = $(this).attr('id');
    var data = {
      id : postID,
      text: postText
    };
    console.log(data);
    $.ajax({
      dataType: 'JSON',
      type : 'get',
      data : data,
      url : '/board/repost/'
    }).then(function(data){
      alert('Репост отправлен к вам на стену');
    });
  });

  addMoreTextBtn( $('.message-post'), 5, 120 );
  toggleMoreTextBtn();
});

</script>   
