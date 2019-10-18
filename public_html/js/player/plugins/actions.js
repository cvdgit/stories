
var WikidsActions = window.WikidsActions || (function(transition) {
    "use strict";

    $(".reveal > .slides").on("click", "img[data-action=1]", executeAction);

    function executeAction() {
        var storyID = $(this).attr("data-action-story"),
            slideID = $(this).attr("data-action-slide");
        transition.goToSlide(storyID, slideID);
    }

})(TransitionSlide);
