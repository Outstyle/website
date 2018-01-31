way.restore();

/* COMMENT DELETE */
jQuery("body").on("commentDelete", function(event, data) {
    if (jQuery.type(data) === "number") {
        jQuery(".user-registered div[data-comment-id='"+data+"']").addClass('comment__deleted').fadeOut('slow');
    }
});

/* COMMENT ADD */
jQuery("body").on("commentAdd", function(event, data) {

    /* Timeout is needed for making an animation of new comment flicker to apply */
    setTimeout(function(){
        if (jQuery.type(data.elem_id) === "number") {

            /* Make freshly added message flicker */
            jQuery(".user-registered div[data-comment-id='"+data.elem_id+"']")
            .addClass('comment__added comment__highlight')
            .hide()
            .fadeIn("slow", function() {
                jQuery(this).removeClass('comment__highlight');
            });

            /**
             * Working with localstorage
             * - Removing stored comment conents to clear the comments_message form
             * - Removing stored attachments
             */
            way.restore();
            way.remove(data.elem_type+'comment');
            way.remove('attachments'); /* TODO: separate types .5 .6 */
            way.backup();

            jQuery('#comments_section__'+data.elem_id+' .comments_add__body textarea').html(); /* Clearing message textarea */
            jQuery('#comments_section__'+data.elem_id+' .comments_add__body #board_attachments').empty(); /* Clearing attachments textarea */
            jQuery('#ohsnap').empty();

            ohSnap('Dev debug: commentAdd triggered', {'color':'blue'});

        } else {
          /* --- If we encounter an error --- */
          ohSnap(data[Object.keys(data)[0]], {'color':'red'});
        }

    },50);

});


/* OVERALL COMMENTS INITIALIZATION */
function commentsInit(controllerId) {

    way.restore();

    /* --- Showing 'delete' icon on single comment area hover [ONLY FOR REGISTERED] --- */
    jQuery('.user-registered .comment__wrap').hover(function() {
         jQuery(this).find('.comment__delete').show();
    }, function() {
         jQuery(this).find('.comment__delete').hide();
    });

    /* --- Autosizing for textareas: http://www.jacklmoore.com/autosize/ --- */
    autosize(jQuery('textarea'));

    var comment = way.get(controllerId+'comment.comments_message');
    if (comment) {
        jQuery('#comments_section__'+controllerId+' .comments_add__body textarea').html(comment);
    }

    /* COMMENT BEFORE AJAX SEND */
    jQuery(document).on("beforeAjaxSend.ic", function(event, settings) {

      /* Before comment add action */
      if (settings.url == '/api/comments/add') {
          var comment = way.get(controllerId+'comment');
          var comment_attachments = way.get('attachments.6');

          /* Set comment params for request */
          if (comment) {
              comment = jQuery.param(comment);
              settings.data = settings.data+'&'+comment;
          }

          /* Set attachments params for request */
          if (comment_attachments) {
              settings.data = settings.data+'&attachments='+comment_attachments;
          }

          /* Clearing attachments container */
          jQuery('#comments_section__'+controllerId+' .comments_add__body #board_attachments').empty(); /* Clearing attachments textarea */
      }

    });

}

/* COMMENTS SHOW FORM EVENT */
jQuery("body").on("commentsShow", function(event, data) {

    if (data.elem_type == 'board' && data.elem_id) {

        var appendTo = jQuery('#'+data.target).closest('.post');
        jQuery(".post").removeClass('active-post');
        appendTo
          .addClass('active-post')
          .find('.show-comment-form-link')
          .hide();

        jQuery(".show-comment-form-link").show();

        jQuery(".post__comments").empty();
        jQuery("#comments_section__board")
          .hide()
          .detach()
          .appendTo(appendTo)
          .slideDown(284)
          .find('.comments_options .i-send')
          .attr({
            'ic-include': '{"elem_type":"'+data.elem_type+'","elem_id":'+data.elem_id+'}',
            'ic-target': '#comments-'+data.elem_id+' .comments_body',
          });

        window.scroll(0,jQuery(".active-post").position().top );
        autosize.update(jQuery('.comments_add__body textarea'));

        /* Check for localstorage attachments and trigger element with props */
        var attachments = way.get('attachments');
        if (typeof attachments !== "undefined") {
            if (attachments.hasOwnProperty(6)) {
              Intercooler.triggerRequest('#'+data.elem_type+'_attachments');
            }
        }

    }

});
