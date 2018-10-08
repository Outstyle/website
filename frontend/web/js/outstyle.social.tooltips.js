function photoalbumsTooltipsInit() {
  jQuery('#photo__editbutton').css('visibility', 'visible');
  if (jQuery('#photo__editbutton').hasClass("tooltipstered")) {
    return;
  }

  /* Activating tooltip on "+" button */
  /* @see: http://iamceege.github.io/tooltipster/ */
  jQuery('#photo__editbutton').tooltipster({
    zIndex: 1337,
    trigger: 'click',
    side: 'bottom',
    distance: -3,
    contentAsHTML: true,
    /* contentCloning: true -> Only for one instance (will not work if there are more than 2 tooltips on page) */
    contentCloning: true,
    interactive: true,
    functionInit: function(instance, helper) {
      var content = jQuery('#photos_edit_tooltip_content');
      instance.content(content);
    },
    functionAfter: function(instance, helper) {
      jQuery('#photos_edit_tooltip_content').appendTo('.tooltip_templates');
    }
  });
}

function photoalbumsTooltipsClose() {
  jQuery('#photo__editbutton').tooltipster('close');
}


function friendTooltipInit(elemId) {
  if (jQuery(elemId).hasClass("tooltipstered")) {
    return;
  }

  /* @see: http://iamceege.github.io/tooltipster/ */
  jQuery(elemId).tooltipster({
    zIndex: 1337,
    trigger: 'click',
    side: 'bottom',
    distance: -12,
    contentAsHTML: true,
    contentCloning: true,
    interactive: true,
    animationDuration: 0,
    functionInit: function(instance, helper) {
      var content = jQuery('.friend_options_tooltip_content');
      instance.content(content);
    }
  });
}