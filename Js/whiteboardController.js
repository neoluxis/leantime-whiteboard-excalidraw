leantime.whiteboardsController = (function () {

    var initListPage = function () {
        // Handle delete confirmations
        jQuery(".deleteWhiteboard").on("click", function (e) {
            if (!confirm(leantime.i18n.__("confirm.delete_whiteboard"))) {
                e.preventDefault();
            }
        });

        // Handle inline rename via prompt
        jQuery(".renameWhiteboard").on("click", function () {
            var id = jQuery(this).data("id");
            var currentTitle = jQuery(this).data("title");
            var newTitle = prompt(leantime.i18n.__("label.whiteboard_title") + ":", currentTitle);

            if (newTitle && newTitle.trim() !== "" && newTitle !== currentTitle) {
                jQuery.post(
                    leantime.appUrl + "/whiteboards/rename/" + id,
                    { title: newTitle },
                    function () {
                        location.reload();
                    }
                );
            }
        });
    };

    return {
        initListPage: initListPage
    };

})();
