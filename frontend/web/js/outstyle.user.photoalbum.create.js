var photoalbum_create_modal = '#userphotoalbumcreate';
var photoalbum_create_form = '#form-create-photoalbum';
var photoalbum_area = '#albums_area';
var photoalbum_loader = '<div class="loader--smallest u-pull-right"></div>';

/* Create new photoalbum event FIXME TODO: Make this via IC, not jqAjax */
function photoalbumFormInit() {

    jQuery(photoalbum_create_form).on('beforeSubmit', function(event, jqXHR, settings) {
        var form = jQuery(this);
        if (form.find('.has-error').length) {
            return false;
        }

        var csrfToken = jQuery('meta[name="csrf-token"]').attr("content");

        jQuery.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize() + '&_csrf=' + csrfToken,

            beforeSend: function() {
                jQuery('#createphotoalbum-submit')
                    .hide()
                    .after(photoalbum_loader);
            },

            complete: function() {
                jQuery('#createphotoalbum-submit')
                    .show()
                    .next()
                    .remove();
            },

            success: function(data, status, jqXHR) {

                var contentType = jqXHR.getResponseHeader('Content-type');

                /* IF data is JSON | TODO: Make all requests JSON, untie from HTML parts */
                if (contentType == 'application/json; charset=UTF-8') {

                    /* Prevent any actions if albums limit reached - show notice instead */
                    if (data.photoalbumsLimit) {
                        jQuery('#ohsnap').css({
                            'z-index': 90000000
                        });
                        ohSnapX();
                        ohSnap(data.photoalbumsLimit[0], {
                            'color': 'red'
                        });
                    }

                    return;
                }


                /* Clearing the form itself */
                form.find('#photoalbum-name').val('');
                form.find('#photoalbum-text').val('');

                /* Hiding photoalbum creation modal window */
                userHidePhotoalbumCreateModal();

                /* Moving scrollbar of albums list to top, so we could see new album appearance */
                jQuery(photoalbum_area)
                    .overlayScrollbars({})
                    .overlayScrollbars()
                    .scroll({
                        x: 0,
                        y: 0
                    });

                /* Appending new album into the albums area + flickering effect */
                jQuery(data)
                    .prependTo(photoalbum_area + ' .os-content')
                    .addClass('album__added album__highlight')
                    .hide()
                    .fadeIn("slow")
                    .promise()
                    .done(function() {
                        jQuery(this).removeClass('album__highlight');
                        var addedAlbumId = jQuery(this).find('.album').attr('id');

                        /* Init Intrcooler stuff on element after swap is done */
                        Intercooler.processNodes(photoalbum_area + ' .os-content');
                        Intercooler.triggerRequest('#' + addedAlbumId);
                    });

            }
        });

        return false;
    });
}

/**
 * Checks if contents were already loaded via AJAX into modal body
 * If return false, will cancel AJAX call
 * @return {bool}
 */
function checkPhotoalbumCreateFormAlreadyExists() {
    var form = jQuery(photoalbum_create_modal).find(photoalbum_create_form);
    if (form.length === 1) {
        return true;
    }
    return false;
}

/* PHOTOALBUM FORM BEFORE AJAX SEND */
jQuery(document).on("beforeAjaxSend.ic", function(event, settings) {

    /* Before sending for photoalbum creation form */
    if (settings.url == '/api/forms/photoalbum/create') {

        /* If we already have the form in page, canceling AJAX request */
        if (checkPhotoalbumCreateFormAlreadyExists() === true) {
            settings.cancel = 'true';
        }
    }

});

/**
 * Fires on photoalbum creation modal window
 */
function userShowPhotoalbumCreateModal() {
    jQuery(photoalbum_create_modal).trigger('openModal');
}

function userHidePhotoalbumCreateModal() {
    jQuery(photoalbum_create_modal).trigger('closeModal');
}
jQuery(document).on("closeModal", function() {
    ohSnapX();
});

jQuery("body").on("photoalbumFormRendered", function(evt, data) {
    setTimeout(function() {
        setModalDimensions(photoalbum_create_modal); /* @see: outstyle.modal.js */
        photoalbumFormInit();
    }, 150);
});

/* Photoalbums history.back() events */
jQuery(document).on("beforeHistorySnapshot.ic", function(evt, target) {
    jQuery(photoalbum_create_modal + " .modal__body").empty();
});