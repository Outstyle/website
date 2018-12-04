
<?php

    use yii\helpers\Html;
    use yii\helpers\Url;

    /* @var $this yii\web\View */
    $this->title = Yii::t('app', 'Сообщения').' - '.Yii::$app->name;
    $this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
    $this->params['breadcrumbs'][] = Yii::t('app', '');

?>

<script>
  (function ($) {
    $.fn.scrollSync = function () {
      var $this = $(this);
      $this.on('wheel', function (e) {
        var $sender = $(e.currentTarget);
          if ($sender.filter(':hover')) {
          $this.not($sender).each(function (i, other) {
            $(other).css('top', $this.css('top'));
          });
        }
      });
    };
  })(jQuery);
</script>


  <div class="chat">
    <div class="wp-search-friend-block">
      <div class="wrapper-list-input-checkbox">
        <input type="text" id="search" class="search-field-friend">
        <input type="submit" value="" class="search-button-friend">
      </div>
    </div>
    <div class="chat-wrap">
    <div class="list-users scrollable">
      <?php if (!empty($modelDialogs)) {
    foreach ($modelDialogs as $key => $dialog) {
        ?>

          <div data-index="3" id="<?=$dialog['dialog']?>" data-id="<?=$dialog['user_id']?>"  class="user-dialog user3">
            <div class="user-ava-chat">
              <?php if (!empty($dialog['countNewMessage'])) {
            ?>
                <div class="number-message">
                  <?=$dialog['countNewMessage']?>
                </div>
              <?php 
        } ?>
              <img src="<?php echo Yii::$app->homeUrl; ?>images/avatar/<?=$dialog['user_id']?>_small.jpg" alt=''>
            </div>
            <div class="name-time-chat">
              <div class="name-user-chat">
                <?=$dialog['user_name']?>
              </div>
              <div class="user-oline-time">
                <?=$dialog['created']?>
              </div>
            </div>
            <?php if ($dialog['online']!=0) {
            ?>
              <div class="online-ofline-chat">
              </div>
            <?php 
        } ?>
          </div>
      <?php 
    }
} else {
    ?>
        <div class="new-uzer-massege">
          У Вас нет ещё диалогов <br> Напишите сообщение своим друзьям
        </div>
      <?php 
}?>

    </div>
    <div class="list-dialogs">
      <div class="last-messages-from-users scrollable">
        <?php if (!empty($modelDialogs)) {
    foreach ($modelDialogs as $key => $dialog) {
        ?>
            <div class="last-message-from-user">
              <div class="message-from-user">
                <?=$dialog['lastMessage']?>
              </div>
            </div>
        <?php 
    }
}?>
<!--        <div class="last-message-from-user">
          <div class="message-from-user">
            Человекообразные обезьянки!<br>
            <img src="<?//php echo Yii::$app->homeUrl;?>css/img/icon-audio.png"> Аудиозапись
          </div>
        </div>
        <div class="last-message-from-user">
          <div class="message-from-user">
            <img src="<?//php echo Yii::$app->homeUrl;?>css/img/icon-video.png">  Видеозапись
          </div>
        </div>
        <div class="last-message-from-user">
          <div class="message-from-user">
            Лучшее что нашел<br>
            <img src="<?//php echo Yii::$app->homeUrl;?>css/img/icon-img.png"> Изображение
          </div>
        </div> -->
      </div>
      <div data-content-index="3" class="messeges-vs-select-user select-user3">
        <div class="users-panel-to-add">
          <div class="insert-users">
            <div class="view-user">
              <div class="ava-view-user">
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/ava-user-from-ins.png">
              </div>
              <div class="name-view-user">
                  Kolobok
              </div>
              <div class="close-view-user">
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Close-ICO.png">
              </div>
            </div>
            <div class="view-user">
              <div class="ava-view-user">
                <div class="number-message">
                  9873
                </div>
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/ava-user-from-ins.png">
              </div>
              <div class="name-view-user">
                  Nuguin
              </div>
              <div class="close-view-user">
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Close-ICO.png">
              </div>
            </div>
            <div class="group-users">
              <div class="ava-group-users">
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/group-user2.png">
                <img class="img-group-user-2" src="<?php echo Yii::$app->homeUrl; ?>css/img/group-user1.png">
              </div>
              <div class="names-users">
                Neguin, Kolobok, Trix ...
              </div>
              <div class="close-view-user">
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Close-ICO.png">
              </div>
            </div>
            <div class="drop-down-list">
              <div class="seting">
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Plus-icon.png">
                <div class="drop-down">
                  <img src="<?php echo Yii::$app->homeUrl; ?>css/img/arrow_up.png">
                  <ul>
                    <li><img src="<?php echo Yii::$app->homeUrl; ?>css/img/Add_Friend_ICO.png"> Добавить собеседника</li>
                    <li><img src="<?php echo Yii::$app->homeUrl; ?>css/img/Addition-ICO.png"> Материалы беседы</li>
                    <li><img src="<?php echo Yii::$app->homeUrl; ?>css/img/Search_ICO.png"> Поиск по истории</li>
                    <li><img src="<?php echo Yii::$app->homeUrl; ?>css/img/Trash_ICO.png"> Очистить историю</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="dialog">
        </div>
      </div>
    <!--  <div class="messeges-vs-select-user select-user4">
        4 user
      </div> -->
    </div>
    </div>
        <div class="name-column">
        <h2>Просмотр диалогов</h2>
         <h3>список диалогов</h3>
        </div>
        <div class="name-column1">
        <div class="chat-list">
          <h2>список диалогов</h2>
          <h3>Просмотр диалогов</h3>
        </div>
        </div>
      <div class="write-message">
          <div class="ava-user">
            <img style="height: 31px; width: 31px;" src="<?php echo Yii::$app->homeUrl; ?>images/avatar/<?=Yii::$app->user->id?>_small.jpg" alt=''>
          </div>
          <!-- <div class="message-area"> -->
            <textarea name="" id="message" cols="30" rows="1"></textarea>
            <!-- <div class="smile">
            <img src="<?php echo Yii::$app->homeUrl; ?>css/img/smile.png">
            </div> -->
            <!-- <div class="add-setings">
            <div class="drop-list">
              <ul>
                <li><img src="<?php echo Yii::$app->homeUrl; ?>css/img/Graffity_ICO_add.png"></li>
                <li><img src="<?php echo Yii::$app->homeUrl; ?>css/img/Music_ICO_add.png"></li>
                <li><img src="<?php echo Yii::$app->homeUrl; ?>css/img/Video_ICO_add.png"></li>
                <li><img src="<?php echo Yii::$app->homeUrl; ?>css/img/Photo_ICO_add.png"></li>
              </ul>
              <img src="<?php echo Yii::$app->homeUrl; ?>css/img/arrow_down.png">
            </div>
            <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Addition_ICO.png">
            </div> -->
          <!-- </div> -->
          <div id="" class="send-icon"></div>
      </div>


<script>
    $('body').addClass('unick');
</script>

<script>
$(document).ready(function() {

    $('.user-ava-chat').error(function() {
        $(this).attr( "src", "http://devoutstyle.org/images/avatar/def_avatar.jpg" );
    });
  ///поиск собеседника//////
    $('.wrapper-list-input-checkbox').on('change', function(){
        var data = {
            search : $("#search").val()
        };
        $.ajax({
            dataType: 'JSON',
            type : 'get',
            data : data,
            url : '/chat/search/'
        }).then(function(data){

            if ( data ) {
                $('.list-users').html(' ');
                $('.last-messages-from-users').html(' ');

                var countNewMessage = (data.countNewMessage !== undefined && data.countNewMessage != 0) ? "<div class='number-message'>" + data.countNewMessage + "</div>" : '';
                var online = (data.online != 0) ? "<div class='online-ofline-chat'></div>" : '';
                var created = (data.created) ? data.created : "";
                $('.list-users').append("<div data-index='3' id='" + data.dialog +"' data-id='" + data.user_id + "'  class='user-dialog user3'><div class='user-ava-chat'>" + countNewMessage + "<img src='<?php echo Yii::$app->homeUrl; ?>images/avatar/" + data.user_id + "_small.jpg' alt=''></div><div class='name-time-chat'><div class='name-user-chat'>" +data.user_name + "</div><div class='user-oline-time'>" + created + "</div></div>" + online + "</div>");
                $('.last-messages-from-users').append("<div class='last-message-from-user'><div class='message-from-user'>" +data.lastMessage + "</div></div>");
            } else {
                $('.list-users').html(' ');
                $('.last-messages-from-users').html(' ');
                $('.list-users').html('Диалоги не найдены');
            }

            $('.user-ava-chat').error(function() {
                $(this).attr( "src", "http://devoutstyle.org/images/avatar/def_avatar.jpg" );
            });

              //Выгрузка сообщений пользователя
              $('.user-dialog').on('click', function(){
                $('.dialog').html(' ');
                $('textarea').val("");
                var message = $(this).parent().children("textarea").val();
                var that = $(this);
                var index = that.data('index');
                $('.list-users, .list-dialogs, .jspTrack').off('.jspSync');
                $('.user-dialog').removeClass('active');
                that.addClass('active');
                $('.last-messages-from-users').hide();
                if (index <= 3){
                  $('.messeges-vs-select-user').hide();
                  $('[data-content-index="' + index + '"]').show();
                  $('.write-message').show();
                  $('.name-column').show();
                  $('.name-column1').hide();
                }

                if($(this).attr('id')){
                    var data = {
                        dialog: $(this).attr('id'),
                        page : 1
                    };
                }else{
                    var data = {
                        user_id:  $(this).attr('data-id')
                    };
                }
                $.ajax({
                    dataType: 'JSON',
                    type : 'get',
                    data : data,
                    url : '/chat/onedialog/'
                }).then(function(data){
                  //var idDialog = data.idDialog;
                    $('.write-message > .send-icon').attr("id",data.idDialog);

                    if(data.messages){
                        var count = data.messages.length;
                        for (var i = 0; i < count ; i++) {
                            var message = data.messages[i];
                            var comText = getBrString(message.message);
                            if (<?=Yii::$app->user->id?> == message.user_id){
                              $('.dialog').prepend("<div id="+message.idMessage+" class='to-user-message'><div class='message'><div class='for-message'>"+comText+"</div><div class='angle'><img src='<?php echo Yii::$app->homeUrl; ?>css/img/angle2.png'></div></div><a href='/profile/" + message.user_id + "/'><div class='user-message-ava'><img style='height: 31px; width: 31px;' src="+"<?php echo Yii::$app->homeUrl;?>"+"images/avatar/"+"<?=Yii::$app->user->id?>"+"_small.jpg"+"></div></a><div class='message-time'><h3>"+message.created+"</h3></div></div>");
                            }else{
                              $('.dialog').prepend("<div id="+message.idMessage+" class='from-user-message'><div class='user-message-ava'><a href='/profile/" + message.user_id + "/'><img style='height: 31px; width: 31px;' src='"+"<?php echo Yii::$app->homeUrl;?>"+"images/avatar/"+message.user_id+"_small.jpg"+"'></div></a><div class='message'>"+message.message+"<div class='for-message'></div><div class='angle'><img src='<?php echo Yii::$app->homeUrl; ?>css/img/angle.png'></div></div><div class='message-time'><h3>"+message.created+"</h3></div></div>");
                            }
                        };
                        setTimeout(function() {
                            apiListDialog.scrollToBottom();
                        }, 500);
                    } else {
                        $('.write-message').attr("id",data.user_id);
                    }
                });
            });
        });
    });

    var timerId;

    //Выгрузка сообщений пользователя
    $('.user-dialog').on('click', function(){
      $('.dialog').html(' ');
      $('textarea').val("");
      var message = $(this).parent().children("textarea").val();
      var that = $(this);
      var index = that.data('index');
      $('.list-users, .list-dialogs, .jspTrack').off('.jspSync');
      $('.user-dialog').removeClass('active');
      that.addClass('active');
      $('.last-messages-from-users').hide();
      if (index <= 3){
        $('.messeges-vs-select-user').hide();
        $('[data-content-index="' + index + '"]').show();
        $('.write-message').show();
        $('.name-column').show();
        $('.name-column1').hide();
      }

        $('.send-icon').attr('recipient', $(this).attr('data-id'));

        var d = {
            dialog: 0,
            page: 1,
            user_id: 0
        };
        if( $(this).attr('id') != '' ){
            d.dialog = $(this).attr('id');
            d.page = 1;
        };

        if ( $(this).attr('data-id') != '' ) {
           d.user_id = $(this).attr('data-id');
        }

      $.ajax({
          dataType: 'JSON',
          type : 'get',
          data : d,
          url : '/chat/onedialog/'
      }).then(function(data){

        clearTimeout(timerId);

        //var idDialog = data.idDialog;
//        if ( data.idDialog != 0 ) {
            $('.write-message > .send-icon').attr("id",data.idDialog);
//        }

        if(data.messages){
          var count = data.messages.length;
          for (var i = 0; i < count ; i++) {
            var message = data.messages[i];
            var comText = getBrString(message.message);
            if (<?=Yii::$app->user->id?> == message.user_id){
              $('.dialog').prepend("<div id="+message.idMessage+" class='to-user-message'><div class='message'><div class='for-message'>"+comText+"</div><div class='angle'><img src='<?php echo Yii::$app->homeUrl; ?>css/img/angle2.png'></div></div><a href='/profile/" + message.user_id + "/'><div class='user-message-ava'><img style='height: 31px; width: 31px;' src="+"<?php echo Yii::$app->homeUrl;?>"+"images/avatar/"+"<?=Yii::$app->user->id?>"+"_small.jpg"+"></div></a><div class='message-time'><h3>"+message.created+"</h3></div></div>");
            }else{
              $('.dialog').prepend("<div id="+message.idMessage+" class='from-user-message'><div class='user-message-ava'><a href='/profile/" + message.user_id + "/'><img style='height: 31px; width: 31px;' src='"+"<?php echo Yii::$app->homeUrl;?>"+"images/avatar/"+message.user_id+"_small.jpg"+"'></div></a><div class='message'>"+message.message+"<div class='for-message'></div><div class='angle'><img src='<?php echo Yii::$app->homeUrl; ?>css/img/angle.png'></div></div><div class='message-time'><h3>"+message.created+"</h3></div></div>");
            }

          };

          setTimeout(function() {
            apiListDialog.scrollToBottom();
          }, 500);
        }else{
          $('.write-message').attr("id",data.user_id);
        }
        //проверка новых сообщений в диалоге
        var check = function checkHandler(){
            var lastMessage = $(".dialog > div").last().attr("id");
            var idDialog = $('.write-message > .send-icon').attr("id");
            var data = {
                idDialog: idDialog,
                idMessage : lastMessage,
            };
            $.ajax({
                dataType: 'JSON',
                type : 'get',
                data : data,
                url : "/chat/checknewmessage/"
            }).then(function(data){

                if (data != false){
                    if(data.messages[0].user_id !=  <?=Yii::$app->user->id;?> ){
                        $('.dialog').append("<div id="+data.messages[0].idMessage+" class='from-user-message'><a href='/profile/" + data.messages[0].user_id + "/'><div class='user-message-ava'><img style='height: 31px; width: 31px;' src='"+"<?php echo Yii::$app->homeUrl;?>"+"images/avatar/"+data.messages[0].user_id+"_small.jpg"+"'></div></a><div class='message'>"+data.messages[0].message+"<div class='for-message'></div><div class='angle'><img src='<?php echo Yii::$app->homeUrl; ?>css/img/angle.png'></div></div><div class='message-time'><h3>"+data.messages[0].created+"</h3></div></div>");
                    }
                }
            });
            timerId = setTimeout(checkHandler, 2000);
        };

        timerId = setTimeout( check, 2000);

      });

    });
// отправка сообщений
    $('body').on('click', '.send-icon', function(){
        var mes = $(this).parent().children("textarea");

        var d = {
            dialog: 0,
            message: $(this).parent().children("textarea").val(),
            user: 0
        };
        if( $(this).attr('id') ){
            d.dialog = $(this).attr('id');
        };
        if( $(this).attr('recipient') ) {
            d.user = $(this).attr('recipient');
        };

      $.ajax({
          dataType: 'JSON',
          type : 'get',
          data : d,
          url: "/chat/sendmessage/"
      }).then(function(data){

        clearTimeout(timerId);

        if (data){
          var comText = getBrString(data.message);
          $('.write-message > .send-icon').attr("id",data.idDialog);
          $('.active').attr("id",data.idDialog);
          $('.dialog').append("<div id="+data.idMessage+" class='to-user-message'><div class='message'><div class='for-message'>"+comText+"</div><div class='angle'><img src='<?php echo Yii::$app->homeUrl; ?>css/img/angle2.png'></div></div><a href='/profile/" + data.user_id + "/'><div class='user-message-ava'><img style='height: 31px; width: 31px;' src="+"<?php echo Yii::$app->homeUrl;?>"+"images/avatar/"+"<?=Yii::$app->user->id?>"+"_small.jpg"+"></div></a><div class='message-time'><h3>"+data.created+"</h3></div></div>");
            mes.val(' ');
        }
        //проверка новых сообщений в диалоге
        var check = function checkHandler(){
          var lastMessage = $(".dialog > div").last().attr("id");
          var idDialog = $('.write-message > .send-icon').attr("id");
          var data = {
            idDialog: idDialog,
            idMessage : lastMessage
          };
          $.ajax({
              dataType: 'JSON',
              type : 'get',
              data : data,
              url : "/chat/checknewmessage/"
          }).then(function(data){
          });
          timerId = setTimeout( checkHandler, 2000 );
        }

        timerId = setTimeout( check, 2000 );

      });
  });
textareaInit();


// jScrollPane init ----------------------------------------------------------------------------

  var $listUsers = $('.list-users').jScrollPane({
    showArrows: true,
    verticalDragMinHeight: 50,
    verticalDragMaxHeight: 150,
    horizontalDragMinWidth: 50,
    horizontalDragMaxWidth: 150,
    stickToBottom : true,
    autoReinitialiseDelay : 100,
    contentWidth : 213,
    autoReinitialise: true
  });
  var $listDialog = $('.list-dialogs').jScrollPane({
    showArrows: true,
    verticalDragMinHeight: 50,
    verticalDragMaxHeight: 150,
    horizontalDragMinWidth: 50,
    horizontalDragMaxWidth: 150,
    stickToBottom : true,
    autoReinitialiseDelay : 100,
    autoReinitialise: true
  });

  var apiListUsers = $listUsers.data('jsp');
  var apiListDialog = $listDialog.data('jsp');



  //sync the two boxes to scroll together
  $('.list-users').on('jsp-scroll-y.jspSync.syncListUsers', listUsersHandler);
  $('.list-dialogs').on('jsp-scroll-y.jspSync.syncListDialog', listDialogHandler);

  $(".jspTrack",".list-users").on('mousedown.jspSync', function() {
    apiListDialog.scrollToY(apiListUsers.getContentPositionY());
  });
  $(".jspTrack",".list-dialogs").on('mousedown.jspSync', function() {
    apiListUsers.scrollToY(apiListDialog.getContentPositionY());
  });


//  $('body').on('click', '.user-dialog', function(){
//    $('.list-users, .list-dialogs, .jspTrack').off('.jspSync');
//
//    var that = $(this);
//    var index = that.data('index');
//    $('.user-dialog').removeClass('active');
//    that.addClass('active');
//    $('.last-messages-from-users').hide();
//    if (index <= 3){
//      $('.messeges-vs-select-user').hide();
//      $('[data-content-index="' + index + '"]').show();
//      $('.write-message').show();
//      $('.name-column').show();
//      $('.name-column1').hide();
//    }
//
//    setTimeout(function() {
//      apiListDialog.scrollToBottom();
//    }, 500);
//  });


  function listUsersHandler(event, scrollPositionY) {
//    $('.list-dialogs').off('.syncListDialog', listDialogHandler);

    var hasClass = $(this).find(".jspDrag").hasClass("jspActive");

    if (hasClass) {
      apiListDialog.scrollToY(scrollPositionY);
    } else {
      apiListDialog.scrollToY(scrollPositionY);
    }

  }

  function listDialogHandler(event, scrollPositionY) {
//    $('.list-users').off('.syncListUsers', listUsersHandler);

    var hasClass = $(this).find(".jspDrag").hasClass("jspActive");

    if (hasClass) {
      apiListUsers.scrollToY(scrollPositionY);
    } else {
      apiListUsers.scrollToY(scrollPositionY);
    }

  }

  sendCtrlEnter('.send-icon');

});
  </script>
