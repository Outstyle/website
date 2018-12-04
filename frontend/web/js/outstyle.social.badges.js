/**
 * Append badge to element
 * @param {string} elementId  CSS selector
 * @param {string} type      CSS class or color, according to BlazeUI
 * @param {string} text
 * @see {@link https://www.blazeui.com/components/badges|BlazeUI Badges}
 */
function appendBadgeToElement(elementId, type, text) {
  var existingBadge = jQuery(elementId).find('span.c-badge');
  if (existingBadge.length <= 0 && text != 0) {
    var badge = jQuery('<span class="c-badge c-badge--rounded c-badge--shaded c-badge--' + type + '">' + text + '</span>');
    jQuery(elementId).append(badge);
    badge.addClass('popout');
  } else {
    existingBadge.html(text);
    if (text === 0) {
      existingBadge.remove();
    }
  }
}

function setupAllBadges() {
  appendBadgeToElement('#menu__item-friends a', 'red', outstyle_globals.owner.friends.count.pending);
  appendBadgeToElement('#friends__roundbutton-all a', 'red', outstyle_globals.owner.friends.count.pending);
}


/* Initial badge placement */
jQuery(document).ready(function() {
  setupAllBadges();
});

/* Refresh badges on every ic request complete */
jQuery("body").on("complete.ic", function(evt, elt, data) {
  setTimeout(function() {
    setupAllBadges();
  }, 125);
});