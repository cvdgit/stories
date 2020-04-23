
var WikidsStoryTest = function() {
    "use strict";

    var numQuestions,
        currentQuestionIndex = 0,
        correctAnswersNumber = 0,
        testData = {},
        dom = {},
        remoteTest = false;

    var questionHistory = [],
        skipQuestion = [];

    function getTestData() {
        return testData;
    }

    function getQuestionsData() {
        return testData.storyTestQuestions.filter(function(el) {
            return (skipQuestion.indexOf(el.id) === -1);
        });
    }

    function getAnswersData(question) {
        return question.storyTestAnswers;
    }

    function init(remote) {
        //console.debug('WikidsStoryTest.init');
        remoteTest = remote || false;
        dom.wrapper = $("<div/>").addClass("wikids-test");
        dom.wrapper.empty();
    }

    function load(data, for_slide) {

        testData = data[0];
        numQuestions = getQuestionsData().length;
        if (numQuestions === 0) {
            return;
        }

        setupDOM();
        addEventListeners();
        start();

        if (for_slide === undefined) {
            for_slide = true;
        }
        if (for_slide) {
            return $("<section/>")
                .attr("data-background-color", "#ffffff")
                .append(dom.wrapper);
        }
        return dom.wrapper;
    }

    function setupDOM() {
        dom.header = createHeader(getTestData());
        dom.questions = createQuestions(getQuestionsData());
        dom.controls = createControls();
        dom.nextButton = $("<button/>")
            .addClass("wikids-test-next")
            .text('Следующий вопрос')
            .appendTo($(".wikids-test-buttons", dom.controls));
        dom.finishButton = $("<button/>")
            .addClass("wikids-test-finish")
            .hide()
            .text('Закончить тест')
            .appendTo($(".wikids-test-buttons", dom.controls));
        dom.restartButton = $("<button/>")
            .addClass("wikids-test-reset")
            .hide()
            .text('Пройти еще раз')
            .appendTo($(".wikids-test-buttons", dom.controls));
        dom.backToStoryButton = $("<button/>")
            .addClass("wikids-test-back")
            .hide()
            .text('Вернуться к истории')
            .appendTo($(".wikids-test-buttons", dom.controls));
        dom.continueButton = $("<button/>")
            .addClass("wikids-test-continue")
            .hide()
            .text('Продолжить')
            .appendTo($(".wikids-test-buttons", dom.controls));
        dom.results = createResults();
        dom.wrapper
            .append(dom.header)
            .append(dom.questions)
            .append(dom.results)
            .append(dom.controls);
    }

    function addEventListeners() {
        dom.nextButton.off("click").on("click", nextQuestion);
        dom.finishButton.off("click").on("click", finish);
        dom.restartButton.off("click").on("click", restart);
        dom.backToStoryButton.off("click").on("click", backToStory);
        dom.continueButton.off("click").on("click", continueTestAction);
    }

    function createAnswer(answer, questionType) {

        var type = "radio";
        if (parseInt(questionType) === 1) {
            type = "checkbox";
        }

        var $element = $("<input/>")
            .attr("id", "answer" + answer.id)
            .attr("type", type)
            .attr("name", "qwe")
            .attr("value", answer.id);

        var $answer = $("<div/>").addClass("wikids-test-answer")
            .on("click", function() {
                var $input = $(this).find("input");
                $input.prop("checked", !$input.prop("checked"));
            })
            .append($element);

        if (answer.image) {
            var $image = $("<img/>")
                .attr("src", answer.image)
                .attr("width", 180);
            $answer.append($image);
        }

        var $label = $("<label/>")
            .attr("for", "answer" + answer.id)
            .text(answer.name);
        $answer.append($label);

        return $answer;
    }

    function shuffle(array) {
        var counter = array.length;
        // While there are elements in the array
        while (counter > 0) {
            // Pick a random index
            var index = Math.floor(Math.random() * counter);
            // Decrease counter by 1
            counter--;
            // And swap the last element with it
            var temp = array[counter];
            array[counter] = array[index];
            array[index] = temp;
        }
        return array;
    }

    function createAnswers(answers, questionType, mixAnswers) {
        var $answers = $("<div/>").addClass("wikids-test-answers");
        mixAnswers = mixAnswers || 0;
        if (mixAnswers === 1) {
            answers = shuffle(answers);
        }
        answers.forEach(function(answer) {
            $answers.append(createAnswer(answer, questionType));
        });
        var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-4 question-image"></div><div class="col-md-8 question-wrapper"></div></div>');
        $wrapper.find(".question-wrapper").append($answers);
        return $wrapper;
    }

    function createQuestion(question) {
        return $("<div/>")
            .hide()
            .addClass("wikids-test-question")
            .html('<p class="question-title">' + question.name + '</p>')
            .attr("data-question-id", question.id)
            .data("question", question);
    }

    function createQuestions(questions) {
        var $questions = $("<div/>").addClass("wikids-test-questions");
        questions.forEach(function(question) {
            var $question = createQuestion(question);
            var $answers = createAnswers(getAnswersData(question), question.type, question.mix_answers);
            $answers.appendTo($question);
            if (question.image) {
                $('<img/>').attr("src", question.image).appendTo($(".question-image", $question));
            }
            $questions.append($question);
        });

        return $questions;
    }

    function createControls() {
        var $controls = $("<div/>").addClass("wikids-test-controls"),
            $buttons = $("<div/>").addClass("wikids-test-buttons");
        $buttons.appendTo($controls);
        return $controls;
    }

    function createResults() {
        return $("<div/>")
            .addClass("wikids-test-results")
            .hide()
            .append($("<p/>"));
    }

    function createHeader(test) {
        return $("<div/>")
            .addClass("wikids-test-header")
            .append($("<h3/>").text(test.title))
            .append($("<p/>").text(test.description));
    }

    function start() {

        correctAnswersNumber = 0;
        currentQuestionIndex = 0;

        var $question = $(".wikids-test-question:first-child", dom.questions);

        $question
            .show()
            .addClass("wikids-test-active-question");

        dom.results.hide();
        dom.nextButton.show();
        dom.restartButton.hide();
        dom.backToStoryButton.hide();
        dom.continueButton.hide();
    }

    function finish() {
        $('.wikids-test-active-question').hide().removeClass('wikids-test-active-question');
        dom.finishButton.hide();
        dom.results
            .html("<p>Верных ответов " + correctAnswersNumber + " из " + numQuestions + "</p>")
            .show();
        dom.restartButton.show();
        dom.backToStoryButton.show();
        dispatchEvent("finish", {
            "testID": getTestData().id,
            "correctAnswers": correctAnswersNumber
        });
    }

    function restart() {
        $(".wikids-test-question:first-child", dom.questions)
            .show()
            .addClass("wikids-test-active-question");
        $(".wikids-test-question input").prop("checked", false);
        dom.results.hide();
        correctAnswersNumber = 0;
        currentQuestionIndex = 0;
        dom.nextButton.show();
        dom.restartButton.hide();
        dom.backToStoryButton.hide();
        dom.continueButton.hide();
    }

    function backToStory() {

        dispatchEvent("backToStory", {});
    }

    function answerQuestion(element, answer) {
        var questionID = element.attr("data-question-id");
        var question = getQuestionsData().filter(function(elem) {
            return parseInt(elem.id) === parseInt(questionID);
        });
        var correctAnswers = getAnswersData(question[0]).filter(function(elem) {
            return parseInt(elem.is_correct) === 1;
        });
        correctAnswers = correctAnswers.map(function(elem) {
            return elem.id;
        });
        var correct = false;
        if (answer.length === correctAnswers.length && answer.sort().every(function(value, index) { return value === correctAnswers.sort()[index];})) {
            correctAnswersNumber++;
            correct = true;
        }
        return correct;
    }

    function getQuestionAnswers(element) {
        var answer = [];
        element.find(".wikids-test-answer input:checked").each(function(i, elem) {
            answer.push($(elem).val());
        });
        return answer;
    }

    var answerIsCorrect,
        currentQuestion;

    function getCurrentQuestion() {
        return currentQuestion;
    }

    function nextQuestion() {

        var $activeQuestion = $('.wikids-test-active-question');

        var answer = getQuestionAnswers($activeQuestion);
        if (answer.length === 0) {
            return;
        }

        answerIsCorrect = answerQuestion($activeQuestion, answer);
        currentQuestion = $activeQuestion.data('question');

        if (answerIsCorrect) {
            skipQuestion.push(currentQuestion.id);
        }

        $activeQuestion
            .hide()
            .removeClass('wikids-test-active-question')
            .next('.wikids-test-question')
            .addClass('wikids-test-active-question');

        dom.results
            .html("<p>Ответ " + (answerIsCorrect ? "" : "не ") + "верный.</p>")
            .show();
        dom.nextButton.hide();
        dom.continueButton.show();
    }

    function continueTestAction() {

        dom.continueButton.hide();

        if (++currentQuestionIndex === numQuestions) {
            if (remoteTest) {
                if (!answerIsCorrect) {
                    var params = {
                        'entity_id': getCurrentQuestion().entity_id,
                        'relation_id': getCurrentQuestion().relation_id,
                    };
                    $.getJSON('/question/get-related-slide', params).done(function (data) {
                        if (data && data['slide_id'] && data['story_id']) {
                            TransitionSlide.goToSlide(data.story_id, data.slide_id, true);
                        } else {
                            WikidsPlayer.right();
                        }
                    });
                }
                else {
                    WikidsPlayer.right();
                }
            }
            else {
                dispatchEvent("backToStory", {});
            }
        }
        else {
            if (!answerIsCorrect && remoteTest) {
                var params = {
                    'entity_id': getCurrentQuestion().entity_id,
                    'relation_id': getCurrentQuestion().relation_id,
                };
                $.getJSON('/question/get-related-slide', params).done(function(data) {
                    if (data && data['slide_id'] && data['story_id']) {
                        TransitionSlide.goToSlide(data.story_id, data.slide_id, true);
                    }
                });
            }
            else {
                dom.results.hide();
                var $activeQuestion = $('.wikids-test-active-question');
                $activeQuestion.show();
                dom.nextButton.show();
            }
        }
    }

    function extend(a, b) {
        for (var i in b) {
            a[i] = b[i];
        }
        return a;
    }

    function dispatchEvent(type, args) {
        var event = document.createEvent("HTMLEvents", 1, 2);
        event.initEvent(type, true, true);
        extend(event, args);
        dom.wrapper[0].dispatchEvent(event);
    }

    return {
        "init": init,
        "load": load,
        "addEventListener": function(type, listener, useCapture) {
            if ('addEventListener' in window) {
                dom.wrapper[0].addEventListener(type, listener, useCapture);
            }
        },
    };
}();
