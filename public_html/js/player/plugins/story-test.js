
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

    function shuffleAnswers() {
        $(".reveal > .slides .wikids-test-answers[data-mix-answers=1]").each(function() {
            var parent = $(this);
            var divs = parent.children();
            while (divs.length) {
                parent.append(divs.splice(Math.floor(Math.random() * divs.length), 1)[0]);
            }
        });
    }

    shuffleAnswers();

    var inTest = false;
    var container = $('.reveal > .slides');

    function action() {

        inTest = true;

        var test_id = $(this).data("testId"),
            slide_index = Reveal.getIndices().h;

        var test = WikidsStoryTest.create(container[0], {
            'dataUrl': '/question/get',
            'dataParams': {'testId': test_id},
            'forSlide': true,
            init: function() {
              return $.ajax({
                "url": config.initAction + '?testId=' + test_id,
                "type": "GET",
                "dataType": "json"
              })
            },
            onInitialized: function() {

              test.addEventListener("finish", storyTestResults);
              test.addEventListener("backToStory", backToStory);

              Reveal.sync();
              Reveal.slide(0);

              stack.unshift({"story_id": currentStoryID, "slide_index": slide_index});
            }
        });
        test.run();
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
        $(".reveal > .slides").empty().append(data);
        if (window["StoryBackground"]) {
            StoryBackground.init();
        }
        Reveal.sync();
        Reveal.slide(slide_index);
    }

    function backToStory() {
        if (stack.length > 0) {
            var state = stack.shift();
            inTest = false;
            getStoryData(state.story_id)
                .done(function(data) {
                    syncReveal(data.html, state.slide_index);
                    currentStoryID = state.story_id;
                });
        }
    }

    return {
        "backToStory": backToStory,
        "isQuestionSlide": function() {
            return true;
        },
        "inTest": function() {
            return inTest;
        }
    };
})();


var Education = (function() {

    var readySlides = [];

    function getCurrentSlide() {
        return Reveal.getCurrentSlide();
    }

    function initQuestions(params) {
        params = params || {};
        return $.getJSON("/question/init", params);
    }

    function loadQuestionData(params) {
        return $.getJSON("/question/get", params);
    }

  function init() {

    var elem = $("div.new-questions", getCurrentSlide());
    if (!elem.length) {
      return;
    }

    var params = elem.data();
    var test = WikidsStoryTest.create(elem[0], {
      'dataUrl': '/question/get',
      'dataParams': params,
      'forSlide': false,
      'required': params.testRequired,
      init: function() {
        return $.getJSON('/question/init', params);
      },
      onInitialized: function() {
        test.addEventListener("finish", function () {
          WikidsPlayer.right();
        });
        test.addEventListener("nextSlide", function () {
          WikidsPlayer.right();
        });
      }
    });

    test.run();


  }

    function initEducation() {
        var currentSlideID = $(getCurrentSlide()).attr('data-id');
        if (readySlides[currentSlideID]) {
            return;
        }
        readySlides[currentSlideID] = true;
        init();
    }

    Reveal.addEventListener("slidechanged", function() {
        initEducation();
    });

    Reveal.addEventListener("ready", function() {
        initEducation();
    });

    return {
    };
})();
