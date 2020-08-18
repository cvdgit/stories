
var WikidsStoryTest = (function() {
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
    var questionAnswers = {};

    function reset()
    {
        numQuestions = 0;
        currentQuestionIndex = 0;
        correctAnswersNumber = 0;
        testData = {};
        dom = {};
        questionHistory = [];
        skipQuestion = [];
        questions = [];
        questionsRepeat = [];
        questionAnswers = {};
        container = null;
    }

    var dataUrl,
        dataParams;

    var container;

    var questionSuccess;

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

    function getProgressData() {
        var progress = testData['test'] || {};
        progress = progress['progress'] || {};
        return progress;
    }

    function getStudentsData() {
        return testData['students'] || [];
    }

    var QuestionsRepeat = function(questions, starsTotal) {
        this.starsTotal = starsTotal;
        questions.map(function(question) {
            if (question['stars']) {
                question.stars.number = parseInt(question.stars.total) - parseInt(question.stars.current);
            }
        });
    };

    QuestionsRepeat.prototype.inc = function(question) {
        question.stars.number++;
        var increased = true;
        if (question.stars.number > this.starsTotal) {
            question.stars.number = this.starsTotal;
            increased = false;
        }
        return increased;
    };

    QuestionsRepeat.prototype.dec = function(question) {
        question.stars.number--;
    };

    QuestionsRepeat.prototype.done = function(question) {
        return parseInt(question.stars.number) <= 0;
    };

    QuestionsRepeat.prototype.number = function(question) {
        var number = this.starsTotal - question.stars.number;
        return number < 0 ? 0 : number;
    };

    QuestionsRepeat.prototype.stars = function(question) {
        return question.stars.number;
    };

    var testProgress;
    var TestProgress = function(progress) {
        this.progress = progress;
    };

    TestProgress.prototype.getProgress = function() {
        return this.progress;
    };

    TestProgress.prototype.getCurrent = function() {
        return this.progress.current;
    };

    TestProgress.prototype.getTotal = function() {
        return this.progress.total;
    };

    TestProgress.prototype.calcPercent = function() {
        return Math.round(this.getCurrent() * 100 / this.getTotal());
    }

    TestProgress.prototype.inc = function() {
        this.progress.current++;
    }

    TestProgress.prototype.dec = function() {
        this.progress.current--;
        if (this.progress.current < 0) {
            this.progress.current = 0;
        }
    }

    function init(remote, for_slide, testResponse, element) {
        console.debug('WikidsStoryTest.init');

        reset();

        container = element;
        remoteTest = remote || false;

        dom.wrapper = $("<div/>").addClass("wikids-test");
        dom.beginPage = createBeginPage(testResponse);
        dom.wrapper.append(dom.beginPage);

        if (for_slide === undefined) {
            for_slide = true;
        }
        if (for_slide) {
            container.html($("<section/>")
                .attr("data-background-color", "#ffffff")
                .append(dom.wrapper));
        }
        else {
            container.html(dom.wrapper);
        }
    }

    function incorrectAnswerAction() {
        return testData['incorrectAnswerAction'] || '';
    }

    function incorrectAnswerActionRelated() {
        return incorrectAnswerAction() === 'related';
    }

    var testQuestions = [];

    function makeTestQuestions() {
        var end = false;
        while (!end && testQuestions.length < 5) {
            end = questions.length === 0;
            if (!end) {
                testQuestions.push(questions.shift());
            }
        }
    }

    function load(data, for_slide) {
        console.debug('WikidsStoryTest.load');

        questionSuccess = new QuestionSuccess();

        //dom.wrapper.empty();
        //dom.wrapper = $("<div/>").addClass("wikids-test");

        testData = data[0];

        questions = getQuestionsData();
        //console.log(questions);

        numQuestions = questions.length;
        if (numQuestions === 0) {
            return;
        }

        //console.log(skipQuestion);

        questionsRepeat = new QuestionsRepeat(questions, remoteTest ? 5 : 1);
        testProgress = new TestProgress(getProgressData());

        makeTestQuestions();
        //console.log(testQuestions);

        setupDOM();
        addEventListeners();

        start();

        if (for_slide === undefined) {
            for_slide = true;
        }
        if (for_slide && !remoteTest) {
            container.html($("<section/>")
                .attr("data-background-color", "#ffffff")
                .append(dom.wrapper));
        }
        else {
            container.html(dom.wrapper);
        }
    }

    function createLoader() {
        return $('<div/>')
            .addClass('wikids-test-loader')
            .append($('<p/>').text('Загрузка вопросов'))
            .append($('<img/>').attr('src', '/img/loading.gif'));
    }

    function loadData() {
        console.debug('WikidsStoryTest.loadData');
        dataParams.studentId = currentStudent.id;
        container.html(createLoader());
        $.getJSON(dataUrl, dataParams)
            .done(function(response) {
                load(response);
                if (!remoteTest) {
                    Reveal.sync();
                    Reveal.slide(0);
                }
            });
    }

    var currentStudent;

    function setActiveStudentElement(element) {
        element.siblings().removeClass('active');
        element.addClass('active');
        currentStudent = element.data('student');
        $('.wikids-test-student-info', dom.header).text(currentStudent.name);
    }

    function createBeginPage(testResponse) {
        var $listGroup = $('<div/>').addClass('list-group');
        $listGroup.on('click', 'a', function(e) {
            e.preventDefault();
            setActiveStudentElement($(this));
        });
        testResponse.students.forEach(function(student) {
            var $item = $('<a/>')
                .attr('href', '#')
                .addClass('list-group-item')
                .data('student', student)
                .append($('<h4/>').addClass('list-group-item-heading').text(student.name));
            if (student['progress'] && student.progress > 0) {
                $item.append(
                    $('<p/>').addClass('list-group-item-text').text(student.progress + '% завершено')
                );
            }
            $item.appendTo($listGroup);
        });
        setActiveStudentElement($listGroup.find('a:eq(0)'));
        var $beginButton = $('<button/>')
            .addClass('btn wikids-test-begin')
            .text('Начать тест')
            .on('click', function() {
                loadData();
            });
        return $('<div/>')
            .addClass('wikids-test-begin-page row')
            .append($('<h3/>').text(testResponse.test.header))
            .append($('<div/>').addClass('col-md-6')
                .append($('<h3/>').text('Выберите ученика:'))
                .append($listGroup)
                .append($beginButton))
            .append($('<div/>').addClass('col-md-6')
                .append($('<p/>').addClass('wikids-test-description').html(testResponse.test.description)));
    }

    function createCorrectAnswerPage() {
        var $action = $('<button/>')
            .addClass('btn')
            .text('Продолжить')
            .on('click', function() {
                dom.correctAnswerPage.hide();
                showNextQuestion();
                dom.results.hide();
                dom.nextButton.show();
            });
        return $('<div/>')
            .addClass('wikids-test-correct-answer-page')
            .hide()
            //.append($('<p/>').addClass('wikids-test-correct-answer-page-header'))
            .append($('<div/>').addClass('wikids-test-correct-answer-answers'))
            .append($('<div/>').addClass('wikids-test-correct-answer-page-action').append($action));
    }

    function setupDOM() {
        console.debug('WikidsStoryTest.setupDOM');
        questionSuccess.create();
        dom.header = createHeader(getTestData());
        dom.questions = createQuestions(getQuestionsData());
        dom.controls = createControls();
        dom.nextButton = $("<button/>")
            .addClass("btn btn-small btn-test wikids-test-next")
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
        dom.correctAnswerPage = createCorrectAnswerPage();
        dom.wrapper
            .append(dom.header)
            .append(dom.questions)
            .append(dom.results)
            .append(dom.controls)
            .append(dom.correctAnswerPage);
    }

    function createStudentInfo() {
        return $('<div/>')
            .addClass('wikids-test-student-info')
            .text(currentStudent.name);
    }

    function addEventListeners() {
        dom.nextButton.off("click").on("click", nextQuestion);
        dom.finishButton.off("click").on("click", finish);
        dom.restartButton.off("click").on("click", restart);
        dom.backToStoryButton.off("click").on("click", backToStory);
        dom.continueButton.off("click").on("click", continueTestAction);
    }

    function showOriginalImage(url) {
        $('<div/>')
            .addClass('wikids-test-image-original')
            .append(
                $('<div/>')
                    .addClass('wikids-test-image-original-inner image-loader')
                    .append(
                        $('<img/>')
                            .attr('src', url + '/original')
                            .on('load', function() {
                                $(this).parent().removeClass('image-loader');
                                $(this).show();
                            })
                            .on('click', function() {
                                $(this).parent().parent().remove();
                            })
                    )
            )
            .appendTo(dom.wrapper);
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
            .on("click", function(e) {
                var tagName = e.target.tagName;
                if (tagName !== 'INPUT' && tagName !== 'IMG') {
                    var $input = $(this).find("input");
                    $input.prop("checked", !$input.prop("checked"));
                }
            })
            .append($element);

        if (answer.image) {
            var $image = $("<img/>")
                .attr("src", answer.image)
                .attr('height', 100)
                .css('cursor', 'zoom-in')
                .on('click', function() {
                    showOriginalImage($(this).attr('src'));
                });
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

    function createSvgAnswer(question, answers) {
        var $object = $('<object/>')
            .attr({
                data: '/upload/Continents.svg?t=' + (new Date().getMilliseconds()),
                type: 'image/svg+xml',
                id: 'svg' + question.id
            })
            .css('width', '100%');
        var getAnswersIDs = function(dom) {
            var answers = [];
            $('.continent.selected', dom).each(function() {
                var id = $(this).attr('id');
                answers.push(id);
            });
            return answers;
        }
        $object[0].addEventListener('load', function() {
            var svgDOM = $object[0].contentDocument;
            $('.continent', svgDOM).on('click', function() {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                }
                else {
                    if (parseInt(question.correct_number) === 1) {
                        $('.selected', svgDOM).removeClass('selected');
                    }
                    $(this).addClass('selected');
                }
                questionAnswers['q' + question.id] = getAnswersIDs(svgDOM);
            });
        }, true);
        return $object;
    }

    function createSvgAnswers(question, answers) {
        var $answers = $("<div/>").addClass("wikids-test-answers");
        $answers.append(createSvgAnswer(question, answers));
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

    function createStars(stars) {
        var $elem = $('<p/>');
        $elem.addClass('question-stars');
        $elem.css('textAlign', 'right');
        appendStars($elem, remoteTest ? 5 : 1, stars.current);
        return $elem[0].outerHTML;
    }

    function createProgress() {
        var progress = testProgress.calcPercent();
        return $('<div/>')
            .addClass('wikids-progress')
            .append(
                $('<div/>')
                    .addClass('progress-bar progress-bar-info')
                    .css('width', progress + '%')
                    .append($('<span/>').addClass('sr-only'))
            )[0].outerHTML;
    }

    function updateProgress() {
        var progress = testProgress.calcPercent();
        $('.wikids-progress .progress-bar', dom.header).css('width', progress + '%');
    }

    function createQuestion(question) {
        var questionName = question.name;
        if (question['correct_number'] && question.correct_number > 1) {
            questionName += ' (верных ответов: ' + question.correct_number + ')';
        }
        var stars = '';
        if (question['stars']) {
            stars = createStars(question.stars);
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

            var view = question['view'] ? question.view : '';
            var $answers;

            if (view === 'svg') {
                $answers = createSvgAnswers(question, getAnswersData(question));
            }
            else {
                $answers = createAnswers(getAnswersData(question), question.type, question.mix_answers);
            }

            $answers.appendTo($question);

            if (question.image) {
                $('<img/>')
                    .attr("src", question.image)
                    .css('cursor', 'zoom-in')
                    .on('click', function() {
                        showOriginalImage($(this).attr('src'));
                    })
                    .appendTo($(".question-image", $question));
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
        var $header = $("<div/>")
            .addClass("wikids-test-header");

        if (test.title) {
            $header.append($("<h3/>").text(test.title));
        }
        if (test.description) {
            $header.append($("<p/>").text(test.description));
        }
        $header
            .append(createStudentInfo())
            .append(createProgress());
        return $header;
    }

    function questionIsVisible(questionElement) {
        var object = $('object', questionElement);
        if (object.length) {
            var domSVG = object.contents();
            $('.continent', domSVG).removeClass('selected');
        }
    }

    function start() {

        correctAnswersNumber = 0;
        currentQuestionIndex = 0;

        showNextQuestion();

        dom.beginPage.hide();
        dom.header.show();
        dom.nextButton.show();
    }

    function finish() {
        $('.wikids-test-active-question').hide().removeClass('wikids-test-active-question');
        dom.finishButton.hide();
        dom.results
            .html("<p>Тест успешно пройден</p>")
            .show();
        dispatchEvent("finish", {
            "testID": getTestData().id,
            "correctAnswers": correctAnswersNumber
        });
    }

    function restart() {

        var nextQuestion = testQuestions.shift();
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
            return parseInt(elem.id);
        });
        var correct = false;
        if (answer.length === correctAnswers.length && answer.sort().every(function(value, index) { return parseInt(value) === correctAnswers.sort()[index];})) {
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

    function getSvgQuestionAnswers(question) {
        if (questionAnswers.length === 0) {
            return [];
        }
        var questionAnswerNames = [];
        $.each(questionAnswers, function(key, value) {
            var questionID = key.replace(/\D+/, '');
            if (parseInt(questionID) === parseInt(question.id)) {
                questionAnswerNames = value;
                return;
            }
        });
        var answers = [];
        if (questionAnswerNames.length > 0) {
            var questionParams = question.svg.params;
            questionAnswerNames.forEach(function (answerName) {
                var param = questionParams.filter(function (value) {
                    return (value.param_name === answerName);
                });
                if (param.length > 0) {
                    answers.push(param[0].entity_id);
                }
            });
        }
        return answers;
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

    function answerByID(question, id) {
        return getAnswersData(question).filter(function(answer) {
            return parseInt(answer.id) === parseInt(id);
        })[0];
    }

    function showQuestionSuccessPage(answer) {

        var action = function() {
            $(this).parent().parent().remove();
            continueTestAction(answer);
        };
        var $content = questionSuccess.create(action, currentQuestion.name, currentQuestion.image);
        dom.wrapper.append($content)
        $content.fadeIn();
    }

    /* Ответ на вопрос */
    function nextQuestion() {

        var $activeQuestion = $('.wikids-test-active-question');
        currentQuestion = $activeQuestion.data('question');

        var answer = [];
        var view = currentQuestion['view'] ? currentQuestion.view : '';
        if (view === 'svg') {
            answer = getSvgQuestionAnswers(currentQuestion);
            questionIsVisible($activeQuestion);
        }
        else {
            answer = getQuestionAnswers($activeQuestion);
        }

        if (answer.length === 0) {
            return;
        }

        answerIsCorrect = answerQuestion($activeQuestion, answer);
        //console.log(answerIsCorrect);

        if (answerIsCorrect) {
            if (currentQuestion['stars']) {
                if (currentQuestion.lastAnswerIsCorrect) {
                    questionsRepeat.dec(currentQuestion);
                    testProgress.inc();
                }
                else {
                    currentQuestion.lastAnswerIsCorrect = true;
                }
            }
            else {
                skipQuestion.push(currentQuestion.id);
            }
            if (!remoteTest) {
                testProgress.inc();
            }
        }
        else {
            currentQuestion.lastAnswerIsCorrect = false;
            if (currentQuestion['stars']) {
                var increased = questionsRepeat.inc(currentQuestion);
                if (increased) {
                    testProgress.dec();
                }
            }
        }

        if (currentQuestion['stars']) {
            updateStars($activeQuestion, questionsRepeat.number(currentQuestion));
        }
        updateProgress();

        //console.log(answerIsCorrect, questionsRepeat.done(currentQuestion));
        var done = false;
        if (remoteTest) {
            if (!answerIsCorrect) {
                testQuestions.unshift(currentQuestion);
            }
            else {
                done = questionsRepeat.done(currentQuestion);
                if (done) {
                    makeTestQuestions();
                }
                else {
                    testQuestions.push(currentQuestion);
                }
            }
        }
        else {
            if (!answerIsCorrect) {
                testQuestions.unshift(currentQuestion);
            }
            else {
                if (!testQuestions.length) {
                    makeTestQuestions();
                }
            }
        }

        //console.log(questions);
        //console.log(currentQuestion);

        if (remoteTest && !App.userIsGuest()) {
            var answerList = answer.map(function(entity_id) {
                return {
                    'answer_entity_id': entity_id,
                    'answer_entity_name': answerByID(currentQuestion, entity_id).name
                };
            });
            var answerParams = {
                'slide_id': WikidsPlayer.getCurrentSlideID(),
                'student_id': currentStudent.id,
                'question_topic_id': currentQuestion.topic_id,
                'question_topic_name': currentQuestion.name,
                'entity_id': currentQuestion.entity_id,
                'entity_name': currentQuestion.entity_name,
                'relation_id': currentQuestion.relation_id,
                'relation_name': currentQuestion.relation_name,
                'correct_answer': answerIsCorrect ? 1 : 0,
                'answers': answerList,
                'progress': testProgress.calcPercent()
            };
            $.post('/question/answer', answerParams);
        }

        $activeQuestion
            .slideUp()
            .hide()
            .removeClass('wikids-test-active-question');

        dom.nextButton.hide();
        if (!answerIsCorrect) {
            dom.results
                .html("<p>Ответ " + (answerIsCorrect ? "" : "не ") + "верный.</p>")
                .show()
                .delay(1000)
                .fadeOut('slow', function() {continueTestAction(answer);});
        }
        else {
            if (done) {
                showQuestionSuccessPage(answer);
            }
            else {
                continueTestAction(answer);
            }
        }
    }

    function showNextQuestion() {
        console.debug('WikidsStoryTest.showNextQuestion');
        var nextQuestion = testQuestions.shift();
        $('.wikids-test-question[data-question-id=' + nextQuestion.id + ']', dom.questions)
            .find('input[type=checkbox],input[type=radio]').prop('checked', false).end()
            .slideDown()
            .addClass('wikids-test-active-question');
    }

    function goToRelatedSlide(goToSlideCallback, otherCallback) {
        var params = {
            'entity_id': getCurrentQuestion().entity_id,
            'relation_id': getCurrentQuestion().relation_id,
        };
        $.getJSON('/question/get-related-slide', params).done(function (data) {
            if (data && data['slide_id'] && data['story_id']) {
                goToSlideCallback(data);
            } else {
                otherCallback();
            }
        });
    }

    function showCorrectAnswerPage(question, answer) {

        var $elements = $('<div/>');
        $elements.append($('<h4/>').text('На континенте ' + question.entity_name + ' обитают:'));
        var $element;
        getAnswersData(question).forEach(function(questionAnswer) {

            $element = $('<div/>').addClass('row');
            var $content = $('<div/>').addClass('col-md-offset-3 col-md-9');
            if (parseInt(questionAnswer.is_correct) === 1) {
                if (questionAnswer.image) {
                    var $image = $('<img/>')
                        .attr("src", questionAnswer.image)
                        .attr("width", 180)
                        .css('cursor', 'zoom-in')
                        .on('click', function() {
                            showOriginalImage($(this).attr('src'));
                        });
/*                    if (questionAnswer['description']) {
                        $image.attr('title', function() {
                            var title = questionAnswer.name + ' обитает на континент' + (questionAnswer.description.split(',').length > 1 ? 'ах' : 'е');
                            return title + ' ' + questionAnswer.description;
                        });
                    }*/
                    $content.append($image);
                }
                $content.append($('<p/>').text(questionAnswer.name));
                $elements.append($element.append($content));
            }
        });

        dom.correctAnswerPage
            //.find('.wikids-test-correct-answer-page-header').text(question.name).end()
            .find('.wikids-test-correct-answer-answers').empty().html($elements[0].childNodes).end()
            .show();
    }

    function continueTestAction(answer) {
        console.debug('continueTestAction');

        dom.continueButton.hide();

        var isLastQuestion = (testQuestions.length === 0);
        var actionRelated = incorrectAnswerActionRelated();
        if (isLastQuestion) {
            if (remoteTest) {
                if (!answerIsCorrect) {
                    if (actionRelated) {
                        goToRelatedSlide(
                            function (data) {
                                TransitionSlide.goToSlide(data.story_id, data.slide_id, true);
                            },
                            function () {
                                WikidsPlayer.right();
                            }
                        );
                    }
                    else {
                        showCorrectAnswerPage(currentQuestion, answer);
                    }
                }
                else {
                    finish();
                }
            }
            else {
                dispatchEvent("backToStory", {});
            }
        }
        else {
            if (!answerIsCorrect && remoteTest) {
                if (actionRelated) {
                    goToRelatedSlide(
                        function (data) {
                            TransitionSlide.goToSlide(data.story_id, data.slide_id, true);
                        },
                        function () {
                            showNextQuestion();
                            dom.results.hide();
                            dom.nextButton.show();
                        }
                    );
                }
                else {
                    showCorrectAnswerPage(currentQuestion, answer);
                }
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

        //init(true);
        dom.wrapper = $("<div/>").addClass("wikids-test");

        setupDOM();
        addEventListeners();
        start();

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
        "setDataParams": function(url, params) {
            dataUrl = url;
            params = params || {};
            dataParams = params;
        }
    };
})();

var QuestionSuccess = function() {

    function create(action, title, image) {
        var $action = $('<button/>')
            .addClass('btn')
            .text('Продолжить')
            .on('click', action);
        return $('<div/>')
            .addClass('wikids-test-success-question-page')
            .hide()
            .append(
                $('<div/>').addClass('wikids-test-success-question-page-content')
                    .append('<i class="glyphicon glyphicon-star"></i>')
                    .append('<i class="glyphicon glyphicon-star"></i>')
                    .append('<i class="glyphicon glyphicon-star"></i>')
                    .append('<i class="glyphicon glyphicon-star"></i>')
                    .append(
                        $('<h4/>').text('Вы заработали 5 звезд!')
                    )
                    .append($('<p/>').text(title))
                    .append($('<img/>').attr('src', image))
            )
            .append($('<div/>').addClass('wikids-test-success-question-page-action').append($action));
    }

    return {
        'create': create
    }
}