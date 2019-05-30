
var TransitionSlide = (function() {

    var config = Reveal.getConfig().transitionConfig,
        stack = [],
        currentStoryID = config.story_id;

    function getStoryData(id) {
        return $.ajax({
            "url": config.action + "/" + id,
            "type": "GET",
            "dataType": "json"
        });
    }

    function action() {

        var story_id = $(this).data("storyId"),
            filter = $(this).data("slides"),
            slide_index = Reveal.getIndices().h;

        var promise = $.ajax({
            "url": config.action + "/" + story_id + "?filter=" + filter,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {

            $(".reveal .slides").empty().append(data.html);
            Reveal.sync();
            Reveal.slide(0);

            stack.unshift({"story_id": currentStoryID, "slide_index": slide_index});
            currentStoryID = story_id;
        });
    }

    $(".reveal > .slides").on("click", "button[data-story-id]", action);

    function syncReveal(data, slide_index) {
        $(".reveal .slides").empty().append(data);
        Reveal.sync();
        Reveal.slide(slide_index);
    }

    function backToStory() {
        if (stack.length > 0) {
            var state = stack.shift();
            getStoryData(state.story_id)
                .done(function(data) {
                    syncReveal(data.html, state.slide_index);
                    currentStoryID = state.story_id;
                });
        }
    }

    return {
        "backToStory": backToStory
    };
})();