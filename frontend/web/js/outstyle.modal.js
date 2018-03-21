/**
 * Modal related stuff
 * Depends on: misc/jquery.easyModal.js
 * TODO: ref[+], const values remove, review?
 *
 * @param  {string} modalId             Element ID (i.e. #myModal)
 * @param  {bool}   canBeOpenedByHash   If set to 'true', can be initiated by hash
 */
function modalInit(modalId, canBeOpenedByHash) {

  jQuery(modalId).easyModal({
    overlayOpacity: 0.9,
    overlayColor: '#1b2022',
    overlayClose: true,

    onOpen: function(myModal) {
      setModalDimensions(modalId);
      jQuery(myModal).addClass('modal-visible');
    },

    onClose: function(myModal) {
      jQuery(myModal).removeClass('modal-visible');
    }

  });

  jQuery('.modal-open').bind('click', function(e) {
    var target = jQuery(this).attr('href');
    jQuery(target).trigger('openModal');
    e.preventDefault();
  });

  jQuery('.modal-close').bind('click', function(e) {
    jQuery('.modal').trigger('closeModal');
  });

  if (window.location.hash) {
    var hash = window.location.hash;
    if (hash == modalId && canBeOpenedByHash) {
      jQuery(hash).trigger('openModal');
    }
  }

}

/**
 * Sets all the necessary modal properties, like width/height and boundary offsets
 * @param {string} modalId  Element ID (i.e. #myModal)
 */
function setModalDimensions(modalId) {

  var widest_label = 0;
  var help_block_offset = 6;
  var modal_width = jQuery(modalId).data('modal-width');
  var modal_height = jQuery(modalId).data('modal-height');
  var modal_top = jQuery(modalId).data('modal-top');
  var input_width = jQuery(modalId).data('input-width');

  if (modal_width) {
    jQuery(modalId).find('.modal__content').css({
      'width': modal_width + 'px'
    });
  }
  if (modal_height) {
    jQuery(modalId).find('.modal__content').css({
      'min-height': modal_height + 'px'
    });
  }
  if (modal_top) {
    jQuery(modalId).css({
      'top': modal_top + 'px',
      'margin-top': 0
    });
  }
  if (input_width) {
    jQuery(modalId).find('input').css({
      'width': input_width + 'px'
    });
  }

  jQuery(modalId)
    .find('.form-group label')
    .each(function() {
      widest_label = Math.max(widest_label, jQuery(this).outerWidth());
    }).width(widest_label);

  jQuery(modalId)
    .find('.help-block')
    .css({
      'margin-left': widest_label + help_block_offset + 'px'
    });

}

/* Photoalbums history.back() events */
jQuery(document).on("beforeHistorySnapshot.ic", function(evt, target) {
  jQuery('.modal').trigger('closeModal');
  jQuery('.lean-overlay').remove();
});

/* Photoalbums popstate events */
jQuery(document).on("handle.onpopstate.ic", function(evt) {
  jQuery('.modal').trigger('closeModal');
  jQuery('.lean-overlay').remove();
});