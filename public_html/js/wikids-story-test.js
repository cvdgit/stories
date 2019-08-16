
/*
var WikidsStoryTest = function() {

    "use strict";

    var numQuestions,
        currentQuestionIndex = 0,
        correctAnswersNumber = 0,
        testData = {};

    this.dom = [];

    function getTestData() {
        return testData;
    }

    function getQuestionsData() {
        return testData.storyTestQuestions;
    }

    function getAnswersData(question) {
        return question.storyTestAnswers;
    }

    this.init = function() {
        this.dom['wrapper'] = $("<div/>").addClass("wikids-test");
        this.dom['wrapper'].empty();
    };

    this.load = function(data) {

        testData = data[0];
        numQuestions = getQuestionsData().length;
        this.setupDOM();

        this.addEventListeners();

        this.start();

        return $("<section/>")
            .attr("data-background-color", "#ffffff")
            .append(this.dom['wrapper']);
    };

    this.setupDOM = function() {

        this.dom['header'] = createHeader(getTestData());
        this.dom['questions'] = createQuestions(getQuestionsData());
        this.dom['controls'] = createControls();

        this.dom['nextButton'] = $("<button/>")
            .addClass("wikids-test-next")
            .text('Следующий вопрос')[0];
        $(".wikids-test-buttons", this.dom['controls']).append(this.dom['nextButton']);

        this.dom['finishButton'] = $("<button/>")
            .addClass("wikids-test-finish")
            .hide()
            .text('Закончить тест')
            .appendTo($(".wikids-test-buttons", this.dom['controls']));
        this.dom['restartButton'] = $("<button/>")
            .addClass("wikids-test-reset")
            .hide()
            .text('Пройти еще раз')
            .appendTo($(".wikids-test-buttons", this.dom['controls']));
        this.dom['backToStoryButton'] = $("<button/>")
            .addClass("wikids-test-back")
            .hide()
            .text('Вернуться к истории')
            .appendTo($(".wikids-test-buttons", this.dom['controls']));
        this.dom['results'] = createResults();
        this.dom['wrapper']
            .append(this.dom['header'])
            .append(this.dom['questions'])
            .append(this.dom['results'])
            .append(this.dom['controls']);
    };

     this.addEventListeners = function() {
         var that = this;
        $(this.dom['nextButton']).off("click").on("click", function() { that.nextQuestion(); });
         this.dom['finishButton'].off("click").on("click", function() { that.finish(); });
         this.dom['restartButton'].off("click").on("click", function() { that.restart(); });
         this.dom['backToStoryButton'].off("click").on("click", function() { that.backToStory(); });
    };

    function createAnswer(answer, questionType) {

        var type = "radio";
        if (questionType === "1") {
            type = "checkbox";
        }

        var $element = $("<input/>")
            .attr("id", "answer" + answer.id)
            .attr("type", type)
            .attr("name", "qwe")
            .attr("value", answer.id);

        var $answer = $("<div/>").addClass("wikids-test-answer")
            .on("click", function() {
                var $input = $(this).find("input")
                $input.prop("checked", !$input.prop("checked"));
            })
            .append($element);

        if (answer.image) {
            var $image = $("<img/>")
                .attr("src", "/test_images/" + answer.image)
                .attr("width", 110);
            $answer.append($image);
        }
        else {
            var $label = $("<label/>")
                .attr("for", "answer" + answer.id)
                .text(answer.name);
            $answer.append($label);
        }

        return $answer;
    }

    function createAnswers(answers, questionType) {
        var $answers = $("<div/>").addClass("wikids-test-answers");
        answers.forEach(function(answer) {
            $answers.append(createAnswer(answer, questionType));
        });
        return $answers;
    }

    function createQuestion(question) {
        return $("<div/>")
            .hide()
            .addClass("wikids-test-question")
            .html('<b>' + question.name + '</b>')
            .attr("data-question-id", question.id);
    }

    function createQuestions(questions) {
        var $questions = $("<div/>").addClass("wikids-test-questions");
        questions.forEach(function(question) {
            var $question = createQuestion(question);
            var $answers = createAnswers(getAnswersData(question), question.type);
            $answers.appendTo($question);
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

    this.start = function() {
        correctAnswersNumber = currentQuestionIndex = 0;
        $(".wikids-test-question:first-child", this.dom.questions)
            .show()
            .addClass("wikids-test-active-question");
        this.dom['results'].hide();
        $(this.dom['nextButton']).show();
        this.dom['restartButton'].hide();
        this.dom['backToStoryButton'].hide();
    }

    this.finish = function() {
        $('.wikids-test-active-question').hide().removeClass('wikids-test-active-question');
        this.dom['finishButton'].hide();
        this.dom['results']
            .html("<p>Верных ответов " + correctAnswersNumber + " из " + numQuestions + "</p>")
            .show();
        this.dom['restartButton'].show();
        this.dom['backToStoryButton'].show();
        this.dispatchEvent("finish", {
            "testID": getTestData().id,
            "correctAnswers": correctAnswersNumber
        });
    };

    this.restart = function() {
        $(".wikids-test-question:first-child", this.dom.questions)
            .show()
            .addClass("wikids-test-active-question");
        this.dom['results'].hide();
        correctAnswersNumber = currentQuestionIndex = 0;
        this.dom['nextButton'].show();
        this.dom['restartButton'].hide();
        this.dom['backToStoryButton'].hide();
    };

    this.backToStory = function() {

        this.dispatchEvent("backToStory", {});
    }

    function answerQuestion(element, answer) {
        var questionID = element.attr("data-question-id")
        var question = getQuestionsData().filter(function(elem) {
            return elem.id === questionID;
        });
        var correctAnswers = getAnswersData(question[0]).filter(function(elem) {
            return elem.is_correct === "1";
        });
        correctAnswers = correctAnswers.map(function(elem) {
            return elem.id;
        });
        if (answer.length === correctAnswers.length && answer.sort().every(function(value, index) { return value === correctAnswers.sort()[index];})) {
            correctAnswersNumber++;
        }
    }

    function getQuestionAnswers(element) {
        var answer = [];
        element.find(".wikids-test-answer input:checked").each(function(i, elem) {
            answer.push($(elem).val());
        });
        return answer;
    }

    this.nextQuestion = function() {

        console.log(this.dom);

        var $activeQuestion = $('.wikids-test-active-question');

        var answer = getQuestionAnswers($activeQuestion);
        if (answer.length === 0) {
            return;
        }

        answerQuestion($activeQuestion, answer);

        $activeQuestion
            .hide()
            .removeClass('wikids-test-active-question')
            .next('.wikids-test-question')
            .show()
            .addClass('wikids-test-active-question');

        if (++currentQuestionIndex === numQuestions) {

            console.log('currentQuestionIndex = ' + currentQuestionIndex, 'numQuestions = ' + numQuestions);

            //$(".wikids-test-next").hide();
            //$(".wikids-test-finish").show();

            $(this.dom['nextButton']).hide();
            this.dom['finishButton'].show();
            this.dom['results']
                .html("<p>Тест завершен. Нажмите Закончить тест, что бы узнать результат.</p>")
                .show();

        }
    };

    function extend(a, b) {
        for (var i in b) {
            a[i] = b[i];
        }
        return a;
    }

    this.dispatchEvent = function (type, args) {
        var event = document.createEvent("HTMLEvents", 1, 2);
        event.initEvent(type, true, true);
        extend(event, args);
        this.dom['wrapper'][0].dispatchEvent(event);
    }

    this.addEventListener = function(type, listener, useCapture) {
        if ('addEventListener' in window) {
            this.dom['wrapper'][0].addEventListener(type, listener, useCapture);
        }
    };

};

*/

var WikidsStoryTest = function() {

    "use strict";

    var numQuestions,
        currentQuestionIndex = 0,
        correctAnswersNumber = 0,
        testData = {},
        dom = {};

    function getTestData() {
        return testData;
    }

    function getQuestionsData() {
        return testData.storyTestQuestions;
    }

    function getAnswersData(question) {
        return question.storyTestAnswers;
    }

    function init() {
        dom.wrapper = $("<div/>").addClass("wikids-test");
        dom.wrapper.empty();
    }

    function load(data) {

        testData = data[0];
        numQuestions = getQuestionsData().length;

        setupDOM();

        addEventListeners();

        start();

        return $("<section/>")
            .attr("data-background-color", "#ffffff")
            .append(dom.wrapper);
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
    }

    function createAnswer(answer, questionType) {

        var type = "radio";
        if (questionType === "1") {
            type = "checkbox";
        }

        var $element = $("<input/>")
            .attr("id", "answer" + answer.id)
            .attr("type", type)
            .attr("name", "qwe")
            .attr("value", answer.id);

        var $answer = $("<div/>").addClass("wikids-test-answer")
            .on("click", function() {
                var $input = $(this).find("input")
                $input.prop("checked", !$input.prop("checked"));
            })
            .append($element);

        if (answer.image) {
            var $image = $("<img/>")
                .attr("src", "/test_images/" + answer.image)
                .attr("width", 110);
            $answer.append($image);
        }
        else {
            var $label = $("<label/>")
                .attr("for", "answer" + answer.id)
                .text(answer.name);
            $answer.append($label);
        }

        return $answer;
    }

    function createAnswers(answers, questionType) {
        var $answers = $("<div/>").addClass("wikids-test-answers");
        answers.forEach(function(answer) {
            $answers.append(createAnswer(answer, questionType));
        });
        return $answers;
    }

    function createQuestion(question) {
        return $("<div/>")
            .hide()
            .addClass("wikids-test-question")
            .html('<b>' + question.name + '</b>')
            .attr("data-question-id", question.id);
    }

    function createQuestions(questions) {
        var $questions = $("<div/>").addClass("wikids-test-questions");
        questions.forEach(function(question) {
            var $question = createQuestion(question);
            var $answers = createAnswers(getAnswersData(question), question.type);
            $answers.appendTo($question);
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
        correctAnswersNumber = currentQuestionIndex = 0;
        $(".wikids-test-question:first-child", dom.questions)
            .show()
            .addClass("wikids-test-active-question");
        dom.results.hide();
        dom.nextButton.show();
        dom.restartButton.hide();
        dom.backToStoryButton.hide();
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
        correctAnswersNumber = currentQuestionIndex = 0;
        dom.nextButton.show();
        dom.restartButton.hide();
        dom.backToStoryButton.hide();
    }

    function backToStory() {

        dispatchEvent("backToStory", {});
    }

    function answerQuestion(element, answer) {
        var questionID = element.attr("data-question-id");
        var question = getQuestionsData().filter(function(elem) {
            return elem.id === questionID;
        });
        var correctAnswers = getAnswersData(question[0]).filter(function(elem) {
            return elem.is_correct === "1";
        });
        correctAnswers = correctAnswers.map(function(elem) {
            return elem.id;
        });
        if (answer.length === correctAnswers.length && answer.sort().every(function(value, index) { return value === correctAnswers.sort()[index];})) {
            correctAnswersNumber++;
        }
    }

    function getQuestionAnswers(element) {
        var answer = [];
        element.find(".wikids-test-answer input:checked").each(function(i, elem) {
            answer.push($(elem).val());
        });
        return answer;
    }

    function nextQuestion() {

        var $activeQuestion = $('.wikids-test-active-question');

        var answer = getQuestionAnswers($activeQuestion);
        if (answer.length === 0) {
            return;
        }

        answerQuestion($activeQuestion, answer);

        $activeQuestion
            .hide()
            .removeClass('wikids-test-active-question')
            .next('.wikids-test-question')
            .show()
            .addClass('wikids-test-active-question');

        if (++currentQuestionIndex === numQuestions) {
            dom.nextButton.hide();
            dom.finishButton.show();
            dom.results
                .html("<p>Тест завершен. Нажмите Закончить тест, что бы узнать результат.</p>")
                .show();
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
