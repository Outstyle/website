var outstyle_sidebar = {
  'activeMenuClass': 'c-nav--active',
  'itemElement': 'a.c-nav__item'
};

jQuery(document).ready(function() {
  jQuery(outstyle_sidebar.itemElement).on("click", function() {
    sidebarHighlightActiveMenuItem(jQuery(this).parent());
  });
});

function sidebarHighlightActiveMenuItem(elementId) {
  jQuery('nav span').removeClass(outstyle_sidebar.activeMenuClass);
  jQuery(elementId).addClass(outstyle_sidebar.activeMenuClass);
}