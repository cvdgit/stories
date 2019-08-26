
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

    $(".reveal > .slides").on("click", ".wikids-test-answer", function() {
        var $input = $(this).find("input");
        $input.prop("checked", !$input.prop("checked"));
    });

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

    $(".reveal > .slides").on("click", "button[data-answer-question]", answerQuestion);

    function getQuestionAnswers() {
        var answer = [];
        $(Reveal.getCurrentSlide()).find(".wikids-test-answer input:checked").each(function(i, elem) {
            answer.push($(elem).val());
        });
        return answer;
    }

    function answerQuestion() {

        var answers = getQuestionAnswers();
        if (!answers.length) {
            return;
        }

        var questionID = $(this).attr("data-answer-question"),
            $slide = $(Reveal.getCurrentSlide());

        $(".wikids-test-answers", $slide)
            .hide()
            .find("input").prop("checked", false);
        $(".wikids-test-controls", $slide).hide();

        $.getJSON(config.storeAction, {
            "question_id": questionID,
            "answers": answers.join(',')
        }).done(function(data) {
            if (data && data.success) {
                $(".wikids-test-results", $slide)
                    .show()
                    .find('p')
                    .text("Вы ответили " + (data.correctAnswer ? "правильно" : "неправильно"))
                    .end()
                    .find('button')
                    .off("click")
                    .on("click", function() {
                        Reveal.next();
                        $(".wikids-test-answers", $slide).show();
                        $(".wikids-test-controls", $slide).show();
                        $(".wikids-test-results", $slide).hide();
                    });
            }
        });
    }

    return {
        "backToStory": backToStory,
        "isQuestionSlide": function() {
            console.log(Reveal.getCurrentSlide());
            return true;
        }
    };
})();