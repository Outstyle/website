function modalInit() {
  jQuery('.modal').each(function () {
      var modal_width = jQuery(this).data('modal-width');
      var modal_height = jQuery(this).data('modal-height');
      var input_width = jQuery(this).data('input-width');
      if (modal_width) {
          jQuery(this).find('.modal__content').css({'width':modal_width+'px'});
      }
      if (modal_height) {
          jQuery(this).find('.modal__content').css({'min-height':modal_height+'px'});
      }
      if (input_width) {
          jQuery(this).find('input').css({'width':input_width+'px'});
      }
  });

  if(jQuery('.lean-overlay').length == 0) {
    jQuery('.modal').easyModal({
      overlayOpacity: 0.9,
      overlayColor: '#1b2022',
      overlayClose: true,
      onOpen: function(myModal){

              jQuery(myModal).addClass('modal-visible');

              var widest = 0;
              var help_block_offset = 6;
              jQuery('.modal-visible .form-group label').each(function () { widest = Math.max(widest, jQuery(this).outerWidth()); }).width(widest);
              jQuery('.help-block').css({'margin-left':widest+help_block_offset+'px'});

              var modal_top = jQuery(myModal).data('modal-top');
              if (modal_top) {
                  jQuery(myModal).css({'top':modal_top+'px','margin-top':0});
              }
      },
      onClose: function(myModal){
        jQuery(myModal).removeClass('modal-visible');
      }
    });
  }

  jQuery('.modal-open').bind('click', function(e) {
      var target = jQuery(this).attr('href');
      jQuery(target).trigger('openModal');
      e.preventDefault();
  });

  jQuery('.modal-close').bind('click', function(e) {
      jQuery('.modal').trigger('closeModal');
  });

  if(window.location.hash) {
      var hash = window.location.hash;
      if (hash != '#uservideo') {
          jQuery(hash).trigger('openModal');
      }
  }
}
