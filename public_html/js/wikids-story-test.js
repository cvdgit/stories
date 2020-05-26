
var WikidsStoryTest = function() {
    "use strict";

    var numQuestions,
        currentQuestionIndex = 0,
        correctAnswersNumber = 0,
        testData = {},
        dom = {},
        remoteTest = false;

    var questionHistory = [],
        skipQuestion = [],
        questions = [],
        questionsRepeat = [];

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

    var QuestionsRepeat = function(questions) {
        this.items = questions.map(function(currentValue) {
            return {"entity_id": currentValue.entity_id, "number": 5};
        });
    };

    QuestionsRepeat.prototype.getItems = function() {
        return this.items;
    };

    QuestionsRepeat.prototype.findItem = function(id) {
        var currentItem;
        this.items.forEach(function(item) {
            if (parseInt(item.entity_id) === parseInt(id)) {
                currentItem = item;
                return;
            }
        });
        return currentItem;
    };

    QuestionsRepeat.prototype.inc = function(id) {
        var item = this.findItem(id);
        item.number++;
    };

    QuestionsRepeat.prototype.dec = function(id) {
        var item = this.findItem(id);
        item.number--;
    };

    QuestionsRepeat.prototype.done = function(id) {
        var item = this.findItem(id);
        return parseInt(item.number) <= 0;
    };

    QuestionsRepeat.prototype.number = function(id) {
        var number = 5 - this.findItem(id).number;
        return number < 0 ? 0 : number;
    };

    QuestionsRepeat.prototype.stars = function(id) {
        var number = this.number(id);
        return number;
    };

    function load(data, for_slide) {

        testData = data[0];

        numQuestions = getQuestionsData().length;
        if (numQuestions === 0) {
            return;
        }

        questions = getQuestionsData();
        questionsRepeat = new QuestionsRepeat(questions);

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
            .attr("value", answer.id)
            .data("answer", answer);

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

    function appendStars($elem, total, current) {
        $elem.empty();
        for (var i = 0, $star, className; i < total; i++) {
            $star = $('<i/>');
            className = 'star-empty';
            if (i + 1 <= current) {
                className = 'star';
            }
            $star.addClass('glyphicon glyphicon-' + className);
            $star.appendTo($elem);
        }
    }

    function createStars(id) {
        var $elem = $('<p/>');
        $elem.addClass('question-stars');
        $elem.css('textAlign', 'right');
        appendStars($elem, 5, questionsRepeat.stars(id));
        return $elem[0].outerHTML;
    }

    function createQuestion(question) {
        var questionName = question.name;
        if (question['correct_number'] && question.correct_number > 1) {
            questionName += ' (верных ответов: ' + question.correct_number + ')';
        }
        var stars = '';
        if (question['stars']) {
            stars = createStars(question.entity_id);
        }
        return $("<div/>")
            .hide()
            .addClass("wikids-test-question")
            .html('<p class="question-title">' + questionName + '</p>' + stars)
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

        var nextQuestion = questions.shift();
        var $question = $('.wikids-test-question[data-question-id=' + nextQuestion.id + ']', dom.questions);
        $question
            .find('input[type=checkbox]').prop('checked', false).end()
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
            .html("<p>Тест успешно пройден</p>")
            .show();
        //dom.restartButton.show();
        //dom.backToStoryButton.show();
        dispatchEvent("finish", {
            "testID": getTestData().id,
            "correctAnswers": correctAnswersNumber
        });
    }

    function restart() {

        /*
        $(".wikids-test-question:first-child", dom.questions)
            .show()
            .addClass("wikids-test-active-question");
        $(".wikids-test-question input").prop("checked", false);
        */

        var nextQuestion = questions.shift();
        $('.wikids-test-question[data-question-id=' + nextQuestion.id + ']', dom.questions)
            .find('input[type=checkbox],input[type=radio]').prop('checked', false).end()
            .addClass('wikids-test-active-question');

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

    function updateStars($question, current) {
        var $stars = $('.question-stars', $question);
        appendStars($stars, 5, current);
    }

    /* Ответ на вопрос */
    function nextQuestion() {

        var $activeQuestion = $('.wikids-test-active-question');
        var answer = getQuestionAnswers($activeQuestion);
        if (answer.length === 0) {
            return;
        }

        answerIsCorrect = answerQuestion($activeQuestion, answer);
        currentQuestion = $activeQuestion.data('question');

        if (answerIsCorrect) {
            //skipQuestion.push(currentQuestion.id);
            questionsRepeat.dec(currentQuestion.entity_id);
        }
        else {
        }

        if (currentQuestion['stars']) {
            updateStars($activeQuestion, questionsRepeat.number(currentQuestion.entity_id));
        }

        //console.log(answerIsCorrect, questionsRepeat.done(currentQuestion.entity_id));
        if (remoteTest) {
            if (!answerIsCorrect || !questionsRepeat.done(currentQuestion.entity_id)) {
                questions.push(currentQuestion);
            }
        }
        else {
            if (!answerIsCorrect) {
                questions.push(currentQuestion);
            }
        }

        //console.log(questions);
        //console.log(currentQuestion.name, answerIsCorrect);

        $activeQuestion
            .hide()
            .removeClass('wikids-test-active-question');

        dom.results
            .html("<p>Ответ " + (answerIsCorrect ? "" : "не ") + "верный.</p>")
            .show();
        dom.nextButton.hide();
        dom.continueButton.show();

        if (remoteTest && !App.userIsGuest()) {
            var answerParams = {
                'slide_id': WikidsPlayer.getCurrentSlideID(),
                'question_topic_id': currentQuestion.topic_id,
                'question_topic_name': currentQuestion.topic_name,
                'entity_id': currentQuestion.entity_id,
                'entity_name': currentQuestion.entity_name,
                'relation_id': currentQuestion.relation_id,
                'relation_name': currentQuestion.relation_name,
                'correct_answer': answerIsCorrect ? 1 : 0
            };
            $.post('/question/answer', answerParams);
        }
    }

    function showNextQuestion() {
        var nextQuestion = questions.shift();
        $('.wikids-test-question[data-question-id=' + nextQuestion.id + ']', dom.questions)
            .find('input[type=checkbox],input[type=radio]').prop('checked', false).end()
            .addClass('wikids-test-active-question')
            .show();
    }

    function continueTestAction() {

        dom.continueButton.hide();

        var isLastQuestion = (questions.length === 0);
        if (isLastQuestion) {
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
                    finish();
                    //WikidsPlayer.right();
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
                    else {
                        showNextQuestion();
                        dom.results.hide();
                        dom.nextButton.show();
                    }
                });
            }
            else {
                showNextQuestion();
                dom.results.hide();
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

    function restore() {

        init(true);
        setupDOM();
        addEventListeners();
        start();
console.log(stars);
        var elem = $("div.new-questions", WikidsPlayer.getCurrentSlide());
        elem.html(dom.wrapper);
    }

    return {
        "init": init,
        "load": load,
        "restore": restore,
        "addEventListener": function(type, listener, useCapture) {
            if ('addEventListener' in window) {
                dom.wrapper[0].addEventListener(type, listener, useCapture);
            }
        },
    };
}();
