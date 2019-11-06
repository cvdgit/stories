
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
            slide_id = $(this).data("slides"),
            backToNextSlide = ($(this).attr("data-backtonextslide") === "1"),
            slide_index = Reveal.getIndices().h;
        goToSlide(story_id, slide_id, backToNextSlide);
    }

    var inTransitionStory = false;

    function goToSlide(storyID, slideID, backToNextSlide) {
        backToNextSlide = backToNextSlide || false;
        var slide_index = Reveal.getIndices().h;
        var promise = $.ajax({
            "url": config.getSlideAction + "?story_id=" + storyID + "&slide_id=" + slideID,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {

            $(".reveal .slides").empty().append(data.html);
            if (window["StoryBackground"]) {
                StoryBackground.init();
            }

            Reveal.sync();
            Reveal.slide(0);

            inTransitionStory = true;

            if (slide_index === 0 && window["WikidsVideo"]) {
                // Если переход происходит с первого (0) слайда, то событие slidechanged не генерится
                WikidsVideo.createPlayer();
            }

            stack.unshift({"story_id": currentStoryID, "slide_index": backToNextSlide ? slide_index : slide_index + 1, "slide_id": slideID});
            currentStoryID = storyID;
        });
    }

    $(".reveal > .slides").on("click", "button[data-story-id]", action);

    function syncReveal(data, slide_index) {
        $(".reveal .slides").empty().append(data);
        if (window["StoryBackground"]) {
            StoryBackground.init();
        }
        Reveal.sync();
        Reveal.slide(slide_index);
        inTransitionStory = false;
    }

    function backToStory() {
        if (stack.length > 0) {
            var state = stack.shift();
            getStoryData(state.story_id)
                .done(function(data) {
                    syncReveal(data.html, state.slide_index);
                    currentStoryID = state.story_id;
                    if (state.slide_index === 0 && window["WikidsVideo"]) {
                        WikidsVideo.createPlayer();
                    }
                });
        }
    }

    return {
        "backToStory": backToStory,
        "goToSlide": goToSlide,
        "getInTransition": function() {
            return inTransitionStory;
        }
    };
})();