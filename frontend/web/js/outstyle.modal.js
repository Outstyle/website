/**
 * Modal related stuff
 * Depends on: misc/jquery.easyModal.js
 * TODO: ref[+], const values remove, review?
 * Explanations: we need to use setModalDimensions before easyModal inits to center modal window
 * After modal is opened, we also need to set size for inner elements and work with modal absolute positioning
 *
 * @param  {string} modalId             Element ID (i.e. #myModal)
 * @param  {bool}   canBeOpenedByHash   If set to 'true', can be initiated by hash
 */
function modalInit(modalId, canBeOpenedByHash) {

  setModalDimensions(modalId);

  jQuery(modalId).easyModal({
    overlayOpacity: 0.9,
    overlayColor: '#1b2022',
    overlayClose: true,
    zIndex: function() {
      return 99999;
    },
    updateZIndexOnOpen: false,

    onOpen: function(myModal) {
      setModalInnerDimensions(modalId);
      jQuery('.lean-overlay').slice(1).remove();
      jQuery('.lean-overlay').show();
      jQuery(myModal).addClass('modal-visible');
    },

    onClose: function(myModal) {
      jQuery(myModal).removeClass('modal-visible');
      jQuery('.lean-overlay').hide();
    }

  });

  jQuery('.modal-open').bind('click', function(e) {
    var target = jQuery(this).attr('href');
    jQuery(target).trigger('openModal');
    e.preventDefault();
  });

  jQuery('.modal-close, .lean-overlay').bind('click', function(e) {
    jQuery('.modal').trigger('closeModal');
    e.preventDefault();
  });

  if (window.location.hash) {
    var hash = window.location.hash;
    if (hash == modalId && canBeOpenedByHash) {
      jQuery(hash).trigger('openModal');
    }
  }

}

/**
 * Sets all the necessary modal properties, like width/height (initial window stuff)
 * @param {string} modalId  Element ID (i.e. #myModal)
 */
function setModalDimensions(modalId) {

  var modal_width = jQuery(modalId).data('modal-width');
  var modal_height = jQuery(modalId).data('modal-height');


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

}

/**
 * Sets all the necessary modal inline properties, like boundary offsets (inside stuff)
 * @param {string} modalId  Element ID (i.e. #myModal)
 */
function setModalInnerDimensions(modalId) {

  var widest_label = 0;
  var help_block_offset = 6;
  var modal_top = jQuery(modalId).data('modal-top');
  var input_width = jQuery(modalId).data('input-width');

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

  jQuery(modalId).find('.form-group label')
    .each(function() {
      widest_label = Math.max(widest_label, jQuery(this).outerWidth());
    }).width(widest_label);

  jQuery(modalId).find('.help-block')
    .css({
      'margin-left': widest_label + help_block_offset + 'px'
    });
}

function closeAllModals() {
  jQuery('.modal').trigger('closeModal');
  jQuery('.lean-overlay').not(':last-child').remove();
}

/* Removing all overlays on history.back() events */
jQuery(document).on("beforeHistorySnapshot.ic", function(evt, target) {
  jQuery('.lean-overlay').not(':last-child').remove();
});

/* Removing all overlays on popstate events */
jQuery(document).on("handle.onpopstate.ic", function(evt) {
  jQuery('.lean-overlay').not(':last-child').remove();
  jQuery('.lean-overlay').hide();
});