var attachments = '';
var attachments_container = '#userattachments';
var attachments_allowed_elements = [5,6];

/**
 * Fires on attachments choose modal window
 */
function userShowAttachmentsModal() {
    jQuery(attachments_container+' .modal__body').empty();
    jQuery(attachments_container).trigger('openModal');
    appendNotification(attachments_container); /* outstyle.notifications.js */
}

/**
 * Fires on single attachment choose event
 */
function userHideAttachmentsModal() {
    jQuery(attachments_container).trigger('closeModal');
}

/**
 * Fires on attachments modal close
 */
jQuery(attachments_container).on('closeModal', function(e) {
    jQuery(attachments_container+' .modal__body').empty();
    removeNotification(attachments_container); /* outstyle.notifications.js */
});

/**
 * Sets attachments limit
 * @param  {int} key  Element type or element key in storage
 */
function getAttachmentsLimit(key) {
    if (key == 6) {
      return 2;
    }
    return 0;
}

/**
 * Checks attachments limit
 * @param  {int} key  Element type or element key in storage
 */
function checkAttachmentsLimit(key, count) {
    /* Count limits and hide attachment buttons (visual stuff) */
    if (count >= getAttachmentsLimit(key)) {
        jQuery('.comments_add__attachments button').hide();
    } else {
        jQuery('.comments_add__attachments button').fadeIn(500);
    }
}

/**
 * Deletes attachment from localstorage
 */
function attachmentDelete(elt) {
    var lc_key = jQuery(elt).parent().attr('data-lc-key');
    var lc_elem = jQuery(elt).parent().attr('data-lc-elem');

    if (typeof lc_key !== "undefined" && typeof lc_elem !== "undefined") {
        var attachments = way.get('attachments.'+lc_key);
        if (attachments) {
            var attachmentToRemove = attachments[lc_elem];

            /* Check if we still have something to remove from array */
            if (attachmentToRemove) {
                attachments.splice(jQuery.inArray(attachmentToRemove, attachments), 1);
                checkAttachmentsLimit(lc_key, attachments.length);
                if (attachments.length > 0) {
                    way.set('attachments.'+lc_key, attachments);
                } else {
                    way.remove('attachments.'+lc_key);
                }
            } else {
                way.remove('attachments.'+lc_key);
            }

            jQuery(elt).parent().slideUp(200);
            way.backup();
        }
    }
}

/* ATTACHMENTS ADD: CHECK AND LIST
    0. Check if element is in allowed array
    1. Restore data from users localStorage
    2. Prepare new attachment to be added to localstorage array
    3. Check for already stored (old) attachments
    4. Merge new attachment with already existing. HINT: To change attachments order, change concat elements
    5. Store and list attachments, if parent type allows X limit
    6. Trigger IC event, get the data and process nodes to activate IC events
 */
jQuery("body").on("attachmentPrepareAdd", function(event, data) {

    var key = data.attachment.elem_type;
    var limit = getAttachmentsLimit(key);
    var newAttachment = [data.attachment.attachment_type+'_'+data.attachment.attachment_id];
    var oldAttachments = way.get('attachments.'+key);
    var count = jQuery(oldAttachments).length;

    if ((jQuery.inArray(key, attachments_allowed_elements) == -1)) {
      return;
    }

    if (oldAttachments) {
        newAttachment = oldAttachments.concat(newAttachment);
    }

    way.set('attachments.'+key, newAttachment);
    way.backup();

    if (count >= limit) {
        var limitAttachments = way.get('attachments.'+key).splice(0,limit);
        way.set('attachments.'+key, limitAttachments);
        way.backup();
    }

    Intercooler.triggerRequest(event.target, function (html) {
        jQuery(event.target).html(html);
        Intercooler.processNodes(event.target);
    });

});

/* ATTACHMENT BEFORE DATA SEND */
jQuery(document).on("beforeSend.ic", function(evt, elt, data) {

    /**
     * Triggering delete action without actual AJAX request
     * Deleting data from localstorage, so we don't need to do actual request to server
     * @param  {obj} jQuery [description]
     * @return TODO: [MISC] Need to return something in case of error
     */
    if (jQuery(evt.target).attr('ic-src') == '/api/attachments/delete') {
        attachmentDelete(elt);
    }

});

/* ATTACHMENT BEFORE AJAX SEND */
jQuery(document).on("beforeAjaxSend.ic", function(event, settings) {

    /* Before attachments list action */
    if (settings.url == '/api/attachments/list') {

        attachments = way.get('attachments');

        if (attachments) {

            /* Check attachment limit for comments */
            if (attachments.hasOwnProperty(6)) {
              checkAttachmentsLimit(6, attachments[6].length);

              /* Set params for request */
              attachments = jQuery.param(attachments);
              settings.data = settings.data+'&'+attachments;
            }

        }

    }

});
