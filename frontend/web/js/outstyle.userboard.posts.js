function userboardPostsInit() {

}

jQuery("body").on("boardPostAdded", function() {
    Intercooler.triggerRequest("#menu__item-users a");
});