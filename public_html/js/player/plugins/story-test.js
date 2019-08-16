
var TestSlide = (function() {

    var config = Reveal.getConfig().testConfig,
        stack = [],
        currentStoryID = config.story_id;

    function getStoryData(id) {
        return $.ajax({
            "url": config.storyBodyAction + "/" + id,
            "type": "GET",
            "dataType": "json"
        });
    }

    function action() {

        var test_id = $(this).data("testId"),
            slide_index = Reveal.getIndices().h;

        var promise = $.ajax({
            "url": config.action + "/" + test_id + "?t=" + Math.random(),
            "type": "GET",
            "dataType": "json"
        });



        promise.done(function(data) {

            $(".reveal .slides").empty();

            WikidsStoryTest.init();
            WikidsStoryTest.addEventListener("finish", storyTestResults);
            WikidsStoryTest.addEventListener("backToStory", backToStory);
            var html =  WikidsStoryTest.load(data.json);

            $(".reveal .slides").append(html);
            Reveal.sync();
            Reveal.slide(0);

            stack.unshift({"story_id": currentStoryID, "slide_index": slide_index});
            //currentStoryID = story_id;
        });
    }

    function storyTestResults(event) {
        var promise = $.ajax({
            "url": config.storeAction,
            "type": "GET",
            "dataType": "json",
            "data": {
                "test_id": event.testID,
                "correct_answers": event.correctAnswers
            }
        });
        promise.done(function(data) {

        });
    }

    $(".reveal > .slides").on("click", "button[data-test-id]", action);

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