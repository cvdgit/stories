
var WikidsStoryTest = (function() {
    "use strict";

    var numQuestions,
        currentQuestionIndex = 0,
        correctAnswersNumber = 0,
        testData = {},
        dom = {},
        remoteTest = false,
        forSlide;

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

    var TestConfig = function(data) {
        this.source = parseInt(data.source);
        this.answerType = parseInt(data.answerType);
    }

    TestConfig.prototype.getSource = function() {
        return this.source;
    }

    TestConfig.prototype.sourceIsLocal = function() {
        return this.source === 1;
    }

    TestConfig.prototype.sourceIsNeo = function() {
        return this.source === 2;
    }

    TestConfig.prototype.sourceIsWord = function() {
        return this.source === 3;
    }

    TestConfig.prototype.getAnswerType = function() {
        return this.answerType;
    }

    TestConfig.prototype.answerTypeIsNumPad = function() {
        return this.answerType === 1;
    }

    TestConfig.prototype.answerTypeIsInput = function() {
        return this.answerType === 2;
    }

    TestConfig.prototype.answerTypeIsRecording = function() {
        return this.answerType === 3;
    }

    var testConfig;

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
        forSlide = for_slide;
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

    var incorrectAnswerText = '';
    var showAnswerImage = true,
        showAnswerText = true,
        showQuestionImage = true;

    var numPad;

    function load(data, for_slide) {
        console.debug('WikidsStoryTest.load');

        questionSuccess = new QuestionSuccess();

        testData = data[0];

        questions = getQuestionsData();
        //console.log(questions);

        numQuestions = questions.length;
        if (numQuestions === 0) {
            return;
        }

        //console.log(skipQuestion);

        if (testData['test']) {
            incorrectAnswerText = testData['test']['incorrectAnswerText'] || '';
        }

        if (testData['test']) {
            showAnswerImage = testData['test']['showAnswerImage'];
            showAnswerText = testData['test']['showAnswerText'];
            showQuestionImage = testData['test']['showQuestionImage'];
        }

        testConfig = new TestConfig(testData['test']);

        questionsRepeat = new QuestionsRepeat(questions, testConfig.sourceIsLocal() ? 1 : 5);
        testProgress = new TestProgress(getProgressData());

        numPad = new AnswerTypeNumPad();

        makeTestQuestions();
        //console.log(testQuestions);

        setupDOM();
        addEventListeners();

        start();

        if (for_slide === undefined) {
            for_slide = true;
        }
        if (for_slide && testConfig.sourceIsLocal()) { // !remoteTest
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
                load(response, forSlide);
                if (forSlide) {
                    Reveal.sync();
                    Reveal.slide(0);
                }
            })
            .fail(function(response) {
                container.html(createErrorPage());
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

    function createErrorPage() {
        return $('<div/>')
            .addClass('wikids-test-error-page')
            .append($('<h3/>').text('При загрузке теста произошла ошибка'));
    }

    function correctAnswerPageNext() {
        dom.correctAnswerPage.hide();
        showNextQuestion();
        dom.results.hide();
        showNextButton();
    }

    function createCorrectAnswerPage() {
        var $action = $('<button/>')
            .addClass('btn')
            .text('Продолжить')
            .on('click', function() {
                correctAnswerPageNext();
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

    function showOriginalImage(url, elem) {
        $('<div/>')
            .addClass('wikids-test-image-original')
            .append(
                $('<div/>')
                    .addClass('wikids-test-image-original-inner image-loader')
                    .on('click', function() {
                        $(this).parent().remove();
                        if (elem) {
                            $(elem).parent()[0].click();
                        }
                    })
                    .append(
                        $('<img/>')
                            .attr('src', url + '/original')
                            .on('load', function() {
                                $(this).parent().removeClass('image-loader');
                                $(this).show();
                            })
                    )
            )
            .appendTo(dom.wrapper);
    }

    function createAnswer(answer, question) {

        var questionType = question.type;
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

                var key = 'q' + question.id;
                questionAnswers[key] = getQuestionAnswers($(this).parent());
                if (questionAnswers[key].length === parseInt(question.correct_number)) {
                    nextQuestion();
                }
            })
            .append($element);

        if (showAnswerImage && answer.image) {
            var $image = $("<img/>")
                .attr("src", answer.image)
                .attr('height', 100)
                .css('cursor', 'zoom-in')
                .on('click', function() {
                    showOriginalImage($(this).attr('src'), this);
                });
            $answer.append($image);
        }

        if (showAnswerText) {
            var $label = $("<label/>")
                .attr("for", "answer" + answer.id)
                .text(answer.name);
            $answer.append($label);
        }

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

    function createAnswers(answers, question) {

        var mixAnswers = question.mix_answers;

        var $answers = $("<div/>").addClass("wikids-test-answers");
        mixAnswers = mixAnswers || 0;
        if (parseInt(mixAnswers) === 1) {
            answers = shuffle(answers);
        }
        answers.forEach(function(answer) {
            $answers.append(createAnswer(answer, question));
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
                var key = 'q' + question.id;
                questionAnswers[key] = getAnswersIDs(svgDOM);
                if (questionAnswers[key].length === parseInt(question.correct_number)) {
                    nextQuestion();
                }
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

    function createNumPadAnswer(question, answer) {
        return numPad.create(function(text) {
            nextQuestion();
        });
    }

    function createNumPadAnswers(question, answers) {
        var $answers = $("<div/>").addClass("wikids-test-answers");
        answers.forEach(function(answer) {
            $answers.append(createNumPadAnswer(question, answer));
        });
        var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-4 question-image"></div><div class="col-md-8 question-wrapper"></div></div>');
        $wrapper.find(".question-wrapper").append($answers);
        return $wrapper;
    }

    function createInputAnswer(question, answer) {
        var $html = '<a href="#" title="Повторить слово" class="glyphicon glyphicon-repeat synthesis-question" style="top: 5px; right: 10px"><i></i></a>';
        return $('<div/>').append($html).append(answerTypeInput.create(nextQuestion));
    }

    function createInputAnswers(question, answers) {
        var $answers = $("<div/>").addClass("wikids-test-answers");
        answers.forEach(function(answer) {
            $answers.append(createInputAnswer(question, answer));
        });
        var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-4 question-image"></div><div class="col-md-8 question-wrapper"></div></div>');
        $wrapper.find(".question-wrapper").append($answers);
        return $wrapper;
    }

    function createRecordingAnswer(question, answer) {
        return answerTypeRecording.create(question, answer);
    }

    function createRecordingAnswers(question, answers) {
        var $answers = $("<div/>").addClass("wikids-test-answers");
        answers.forEach(function(answer) {
            $answers.append(createRecordingAnswer(question, answer));
        });
        var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper"></div></div>');
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
        appendStars($elem, testConfig.sourceIsLocal() ? 1 : 5, stars.current);
        return $elem[0].outerHTML;
    }

    function getCurrentProgressStateText() {
        return 'Вопрос ' + testProgress.getCurrent() + ' из ' + testProgress.getTotal();
    }

    function createProgress() {
        var progress = testProgress.calcPercent();
        return $('<div/>')
            .addClass('wikids-progress')
            .attr('title', getCurrentProgressStateText())
            .attr('data-toggle', 'tooltip')
            .append(
                $('<div/>')
                    .addClass('progress-bar progress-bar-info')
                    .css('width', progress + '%')
                    .append($('<span/>').addClass('sr-only'))
            )[0].outerHTML;
    }

    function updateProgress() {
        var progress = testProgress.calcPercent();
        $('.wikids-progress', dom.header).attr('title', getCurrentProgressStateText()).tooltip('fixTitle');
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
            if (testConfig.answerTypeIsNumPad()) {
                view = 'numpad';
            }
            if (testConfig.answerTypeIsInput()) {
                view = 'input';
            }
            if (testConfig.answerTypeIsRecording()) {
                view = 'recording';
            }

            var $answers;
            switch (view) {
                case 'svg':
                    $answers = createSvgAnswers(question, getAnswersData(question));
                    break;
                case 'numpad':
                    $answers = createNumPadAnswers(question, getAnswersData(question));
                    break;
                case 'input':
                    $answers = createInputAnswers(question, getAnswersData(question));
                    break;
                case 'recording':
                    $answers = createRecordingAnswers(question, getAnswersData(question));
                    break;
                default:
                    $answers = createAnswers(getAnswersData(question), question);
            }

            $answers.appendTo($question);

            if (showQuestionImage && question.image) {
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

        $('[data-toggle="tooltip"]', $header).tooltip();

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
        showNextButton();
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

    function answerQuestion(element, answer, correctAnswersCallback, convertAnswerToInt) {

        var questionID = element.attr("data-question-id");
        var question = getQuestionsData().filter(function(elem) {
            return parseInt(elem.id) === parseInt(questionID);
        });
        var correctAnswers = getAnswersData(question[0]).filter(function(elem) {
            return parseInt(elem.is_correct) === 1;
        });
        correctAnswers = correctAnswers.map(correctAnswersCallback);

        var answerCheckCallback = function(value, index) {
            if (convertAnswerToInt) {
                value = parseInt(value)
            }
            return value === correctAnswers.sort()[index];
        };
        var correct = false;
        if (answer.length === correctAnswers.length && answer.sort().every(answerCheckCallback)) {
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
        if (Object.keys(questionAnswers).length === 0) {
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

    function getNumPadQuestionAnswers(element) {
        var answer = [parseInt(element.find('#keyboard + p').text())];
        return answer;
    }

    function getInputQuestionAnswers(element) {
        var val = element.find('.answer-input').val();
        if (!val.length) {
            return [];
        }
        return [val.toLowerCase()];
    }

    function getRecognitionQuestionAnswers(element) {
        var val = answerTypeRecording.getResult();
        if (!val.length) {
            return [];
        }
        return [val.toLowerCase()];
    }

    var answerIsCorrect,
        currentQuestion,
        currentQuestionElement;

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

        console.debug('WikidsStoryTest.nextQuestion');

        var $activeQuestion = $('.wikids-test-active-question');
        currentQuestion = $activeQuestion.data('question');

        var view = currentQuestion['view'] ? currentQuestion.view : '';
        if (testConfig.answerTypeIsNumPad()) {
            view = 'numpad';
        }
        if (testConfig.answerTypeIsInput()) {
            view = 'input';
        }
        if (testConfig.answerTypeIsRecording()) {
            view = 'recognition';
        }

        var answer = [];
        switch (view) {
            case 'svg':
                answer = getSvgQuestionAnswers(currentQuestion);
                questionIsVisible($activeQuestion);
                break;
            case 'numpad':
                answer = getNumPadQuestionAnswers($activeQuestion);
                numPad.reset($activeQuestion);
                break;
            case 'input':
                answer = getInputQuestionAnswers($activeQuestion);
                break;
            case 'recognition':
                answer = getRecognitionQuestionAnswers($activeQuestion);
                answerTypeRecording.resetResult();
                break;
            default:
                answer = getQuestionAnswers($activeQuestion);
        }

        if (answer.length === 0) {
            return;
        }

        var convertAnswerToInt = true;
        var correctAnswersCallback = function(elem) {
            return parseInt(elem.id);
        };
        if (view === 'numpad') {
            correctAnswersCallback = function(elem) {
                return parseInt(elem.name);
            };
        }
        if (view === 'input' || view === 'recognition') {
            correctAnswersCallback = function(elem) {
                return elem.name.toLowerCase();
            };
            convertAnswerToInt = false;
        }
        answerIsCorrect = answerQuestion($activeQuestion, answer, correctAnswersCallback, convertAnswerToInt);
        // console.debug(answerIsCorrect);

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
            if (testConfig.sourceIsLocal()) {
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
        if (!testConfig.sourceIsLocal()) {
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

        if (!App.userIsGuest()) {
            var answerParams = {};
            var answerList = [];
            if (testConfig.sourceIsNeo()) {
                answerList = answer.map(function (entity_id) {
                    return {
                        'answer_entity_id': entity_id,
                        'answer_entity_name': answerByID(currentQuestion, entity_id).name
                    };
                });
                answerParams = {
                    'source': testConfig.getSource(),
                    'test_id': currentQuestion.test_id,
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
            if (testConfig.sourceIsWord() && !testConfig.answerTypeIsInput()) {
/*                answerList = answer.map(function (entity_id) {
                    var answer = answerByID(currentQuestion, entity_id);
                    return {
                        'answer_entity_id': entity_id,
                        'answer_entity_name': answer ? answer.name : entity_id
                    };
                });*/
                answerList = answer.map(function (answerText) {
                    return {
                        'answer_entity_id': currentQuestion.id,
                        'answer_entity_name': answerText
                    };
                });
                answerParams = {
                    'source': testConfig.getSource(),
                    'test_id': currentQuestion.test_id,
                    'student_id': currentStudent.id,
                    'entity_id': currentQuestion.id,
                    'entity_name': currentQuestion.name,
                    'correct_answer': answerIsCorrect ? 1 : 0,
                    'answers': answerList,
                    'progress': testProgress.calcPercent()
                };
                $.post('/question/answer', answerParams);
            }
            if (testConfig.sourceIsWord() && testConfig.answerTypeIsInput()) {
                answerList = answer.map(function (answerText) {
                    return {
                        'answer_entity_id': currentQuestion.id,
                        'answer_entity_name': answerText
                    };
                });
                answerParams = {
                    'source': testConfig.getSource(),
                    'test_id': currentQuestion.test_id,
                    'student_id': currentStudent.id,
                    'entity_id': currentQuestion.id,
                    'entity_name': currentQuestion.name,
                    'correct_answer': answerIsCorrect ? 1 : 0,
                    'answers': answerList,
                    'progress': testProgress.calcPercent()
                };
                $.post('/question/answer', answerParams);
            }
        }

        $activeQuestion
            .slideUp()
            .hide()
            .removeClass('wikids-test-active-question');

        dom.nextButton.hide();
        if (!answerIsCorrect) {
            if (testConfig.sourceIsWord() && !testConfig.answerTypeIsNumPad() && !testConfig.answerTypeIsInput()) {
                continueTestAction(answer);
            }
            else {
                dom.results
                    .html("<p>Ответ " + (answerIsCorrect ? "" : "не ") + "верный.</p>")
                    .show()
                    .delay(1000)
                    .fadeOut('slow', function () {
                        continueTestAction(answer);
                    });
            }
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
        currentQuestion = nextQuestion;

        currentQuestionElement = $('.wikids-test-question[data-question-id=' + nextQuestion.id + ']', dom.questions);

        currentQuestionElement
            .find('input[type=checkbox],input[type=radio]').prop('checked', false).end()
            .slideDown()
            .addClass('wikids-test-active-question');

        if (testConfig.answerTypeIsNumPad() || testConfig.answerTypeIsInput() || testConfig.answerTypeIsRecording()) {
            dom.nextButton.hide();
        }

        if (testConfig.answerTypeIsInput()) {

            var text = getAnswersData(nextQuestion)[0].name;
            var q = $('.wikids-test-active-question .answer-input', dom.questions);
            setTimeout(function () {
                testSpeech.ReadText(text);
                q.focus();
            }, 500);

            setTimeout(function() {
                q
                    .val('')
                    .focus();
            }, 100);

            $('.wikids-test-active-question .synthesis-question', dom.questions)
                .off('click')
                .on('click', function (e) {
                    e.preventDefault();
                    testSpeech.ReadText(text);
                });
        }

        if (testConfig.answerTypeIsRecording()) {

            answerTypeRecording.autoStart(new Event('autoStart'));
        }
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

        var text = incorrectAnswerText || 'Правильные ответы';
        text = text.replace('{1}', question.entity_name);
        $elements.append($('<h4/>').text(text + ':'));

        var $element;
        var answerText = '';
        getAnswersData(question).forEach(function(questionAnswer) {
            $element = $('<div/>').addClass('row');
            var $content = $('<div/>').addClass('col-md-offset-3 col-md-9');
            if (parseInt(questionAnswer.is_correct) === 1) {

                answerText = questionAnswer.name;

                if (questionAnswer.image) {
                    var $image = $('<img/>')
                        .attr("src", questionAnswer.image)
                        .attr("width", 180)
                        .css('cursor', 'zoom-in')
                        .on('click', function() {
                            showOriginalImage($(this).attr('src'));
                        });
                    $content.append($image);
                }

                var $answerElement;
                if (testConfig.answerTypeIsRecording()) {
                    $answerElement = $('<p/>')
                        .append($('<span/>').text(questionAnswer.name))
                        .append($('<a/>')
                            .attr('href', '#')
                            .attr('title', 'Прослушать')
                            .css('font-size', '3rem')
                            .on('click', function(e) {
                                e.preventDefault();
                                testSpeech.ReadText(questionAnswer.name);
                            })
                            .html('<i class="glyphicon glyphicon-volume-up" style="left: 10px; top: 6px"></i>')
                        );
                }
                else {
                    $answerElement = $('<p/>').text(questionAnswer.name);
                }
                $content.append($answerElement);

                $elements.append($element.append($content));
            }
        });

        dom.correctAnswerPage
            //.find('.wikids-test-correct-answer-page-header').text(question.name).end()
            .find('.wikids-test-correct-answer-answers').empty().html($elements[0].childNodes).end()
            .show();

        if (testConfig.answerTypeIsRecording()) {
            setTimeout(function() {
                testSpeech.ReadText(answerText, correctAnswerPageNext);
            }, 1000);
        }
    }

    function showNextButton() {
        if (!testConfig.answerTypeIsNumPad() && !testConfig.answerTypeIsInput() && !testConfig.answerTypeIsRecording()) {
            dom.nextButton.show();
        }
    }

    function continueTestAction(answer) {
        console.debug('continueTestAction');

        dom.continueButton.hide();

        var isLastQuestion = (testQuestions.length === 0);
        var actionRelated = incorrectAnswerActionRelated();
        if (isLastQuestion) {
            if (!testConfig.sourceIsLocal()) {
                if (!answerIsCorrect) {
                    if (testConfig.sourceIsWord() && !testConfig.answerTypeIsRecording()) {
                        showNextQuestion();
                        dom.results.hide();
                        showNextButton();
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
            if (!answerIsCorrect && !testConfig.sourceIsLocal()) {
                    if (testConfig.sourceIsWord() && !testConfig.answerTypeIsRecording()) {
                        showNextQuestion();
                        dom.results.hide();
                        showNextButton();
                    }
                    else {
                        showCorrectAnswerPage(currentQuestion, answer);
                    }
            }
            else {
                showNextQuestion();
                dom.results.hide();
                showNextButton();
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
        },
        "getCurrentQuestion": getCurrentQuestion,
        "nextQuestion": nextQuestion,
        "getCurrentQuestionElement": function() {
            return currentQuestionElement;
        },
        "showNextButton": function() {
            dom.nextButton.show();
        },
        "hideNextButton": function() {
            dom.nextButton.hide();
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

var AnswerTypeNumPad = function() {

};

AnswerTypeNumPad.prototype.create = function(callback) {

    var html = '<div><ul id="keyboard" class="clearfix">' +
        '<li class="letter">1</li>' +
        '<li class="letter">2</li>' +
        '<li class="letter">3</li>' +
        '<li class="letter clearl">4</li>' +
        '<li class="letter">5</li>' +
        '<li class="letter">6</li>' +
        '<li class="letter clearl">7</li>' +
        '<li class="letter ">8</li>' +
        '<li class="letter">9</li>' +
        '<li class="letter">10</li>' +
        '</ul>' +
        '<p></p></div>',
        $html = $(html);

    $html.find('li').on('click', function() {

        $(this).parent().parent().find('p').text($(this).text());
        callback($(this).text());
    });

    return $html;
}

AnswerTypeNumPad.prototype.reset = function(element) {
    element.find('#keyboard + p').text('');
}

var answerTypeInput = {};
answerTypeInput.create = function(action) {
    var $html = $('<input type="text" class="answer-input" />');
    $html.keypress(function(e) {
        if (e.which == 13) {
            action();
            return false;
        }
    });
    return $html;
};

var testSpeech = {};
testSpeech.Synth = window.speechSynthesis;
testSpeech.Voices = [];
testSpeech.Voices = testSpeech.Synth.getVoices();
testSpeech.VoiceIndex = 0;
testSpeech.Rate = 0.85;
testSpeech.ReadText = function(txt, afterSpeech) {
    var ttsSpeechChunk = new SpeechSynthesisUtterance(txt);
    ttsSpeechChunk.voice = testSpeech.Voices[testSpeech.VoiceIndex];
    ttsSpeechChunk.rate = testSpeech.Rate;
    if (afterSpeech) {
        ttsSpeechChunk.onend = afterSpeech;
    }
    testSpeech.Synth.speak(ttsSpeechChunk);
};


var answerTypeRecording = {};
answerTypeRecording.elements = [];

answerTypeRecording.create = function(question, answer) {

    var element = $('<div/>');
    element.addClass('test-recognition');

    element
        .append($('<div/>')
            .addClass('recognition-result-wrapper')
            .append($('<span/>').prop('contenteditable', true).addClass('recognition-result'))
            .append($('<span/>').addClass('recognition-result-interim'))
            .append($('<a/>')
                .attr('href', '#')
                .attr('title', 'Повторить фрагмент')
                .on('click', function(e) {
                    e.preventDefault();
                    var range = window.getSelection().getRangeAt(0);
                    if (!range.toString().length) {
                        return;
                    }
                    answerTypeRecording.startFragment(range, e);
                })
                .hide()
                .addClass('recognition-repeat-word')
                .append($('<i/>').addClass('glyphicon glyphicon-refresh'))
            )
        );

    $('<div/>')
        .addClass('wikids-test-loader')
        .append($('<img/>')
            .attr('src', '/img/loading.gif')
            .attr('width', '60px')
        )
        .hide()
        .appendTo(element);

    element
        .append($('<p/>').addClass('recognition-status'));

    $('<a/>')
        .attr('href', '#')
        .attr('title', 'Повторить ввод с микрофона')
        .addClass('recognition-repeat')
        .on('click', function(e) {
            e.preventDefault();
            answerTypeRecording.autoStart(e);
        })
        .append($('<i/>').addClass('glyphicon glyphicon-refresh'))
        .hide()
        .appendTo(element);

    $('<a/>')
        .attr('href', '#')
        .attr('title', 'Остановить')
        .addClass('recognition-stop')
        .on('click', function(e) {
            e.preventDefault();
            testRecognition.Stop();
        })
        .append($('<i/>').addClass('glyphicon glyphicon-stop'))
        .hide()
        .appendTo(element);

    answerTypeRecording.elements[question.id] = element;

    return element;
};

answerTypeRecording.autoStart = function(e) {

    answerTypeRecording.setStatus('');
    answerTypeRecording.repeatButtonHide();

    setTimeout(function() {
        answerTypeRecording.showLoader();
        testRecognition.Start(e);
    }, 1000);
};

answerTypeRecording.startFragment = function(range, e) {
    answerTypeRecording.setStatus('');
    answerTypeRecording.repeatButtonHide();
    answerTypeRecording.showLoader();
    testRecognition.StartFragment(range, e);
}

answerTypeRecording.repeatButtonShow = function() {
    answerTypeRecording.getElement()
        .find('.recognition-repeat').show();
};

answerTypeRecording.repeatButtonHide = function() {
    answerTypeRecording.getElement()
        .find('.recognition-repeat').hide();
};

answerTypeRecording.showLoader = function() {
    answerTypeRecording.getElement()
        .find('.wikids-test-loader').show();
};

answerTypeRecording.hideLoader = function() {
    answerTypeRecording.getElement()
        .find('.wikids-test-loader').hide();
};

answerTypeRecording.getElement = function() {
    var currentQuestion = WikidsStoryTest.getCurrentQuestion();
    return answerTypeRecording.elements[currentQuestion.id];
}
answerTypeRecording.setStatus = function(statusText) {
    answerTypeRecording.getElement()
        .find('.recognition-status').text(statusText);
};
answerTypeRecording.setResult = function(text) {
    var element = answerTypeRecording.getElement();
    if (text.length) {
        element.find('.recognition-repeat-word').show();
    }
    else {
        element.find('.recognition-repeat-word').hide();
    }
    element.find('.recognition-result').text(text);
}
answerTypeRecording.setResultInterim = function(text) {
    answerTypeRecording.getElement()
        .find('.recognition-result-interim').text(text);
}
answerTypeRecording.getResult = function() {
    return answerTypeRecording.getElement()
        .find('.recognition-result').text();
}
answerTypeRecording.resetResult = function() {
    answerTypeRecording.setResult('');
}

answerTypeRecording.showStopButton = function() {
    answerTypeRecording.getElement()
        .find('.recognition-stop').show();
};

answerTypeRecording.hideStopButton = function() {
    answerTypeRecording.getElement()
        .find('.recognition-stop').hide();
};

var testRecognition = {};

testRecognition.recognizingFragment = false;
testRecognition.recognizing = false;
testRecognition.start_timestamp = null;

testRecognition.recorder = new webkitSpeechRecognition();
testRecognition.recorder.continuous = true;
testRecognition.recorder.interimResults = true;

testRecognition.Stop = function() {
    testRecognition.recorder.stop();
}

testRecognition.Start = function(event) {

    testRecognition.recognizingFragment = false;
    answerTypeRecording.showStopButton();

    if (testRecognition.recognizing) {
        testRecognition.recorder.stop();
        return;
    }

    testRecognition.final_transcript = '';
    testRecognition.recorder.lang = 'ru-RU';
    testRecognition.recorder.start();
    testRecognition.start_timestamp = event.timeStamp;
};

testRecognition.StartFragment = function(range, event) {

    testRecognition.recognizingFragment = true;
    answerTypeRecording.showStopButton();

    if (testRecognition.recognizing) {
        testRecognition.recorder.stop();
        return;
    }

    testRecognition.final_transcript = '';
    testRecognition.recorder.lang = 'ru-RU';
    testRecognition.recorder.start();
    testRecognition.start_timestamp = event.timeStamp;
    testRecognition.selectionRange = range;
}

testRecognition.recorder.onstart = function() {
    testRecognition.recognizing = true;
    answerTypeRecording.setStatus('Идет запись с микрофона');
};

testRecognition.recorder.onerror = function(event) {

    answerTypeRecording.hideLoader();

    var ignore_onend = false;
    if (event.error === 'no-speech') {
        answerTypeRecording.setStatus('Речи не обнаружено');
    }
    else if (event.error === 'audio-capture') {
        answerTypeRecording.setStatus('Не удалось захватить звук');
    }
    else if (event.error === 'not-allowed') {
        answerTypeRecording.setStatus('Пользовательский агент запретил ввод речи из соображений безопасности, конфиденциальности или предпочтений пользователя.');
    }
    else {
        answerTypeRecording.setStatus(event.error);
    }
};

testRecognition.endSpeech = function() {
    answerTypeRecording.hideLoader();
    answerTypeRecording.hideStopButton();
    testRecognition.recognizing = false;
    answerTypeRecording.setStatus('');
    if (answerTypeRecording.getResult() !== '') {
        WikidsStoryTest.showNextButton();
    }
}

testRecognition.recorder.onend = function() {

    testRecognition.endSpeech();

    if (testRecognition.recognizingFragment) {
        var match = answerTypeRecording.getResult().substring(0, testRecognition.selectionRange.startOffset)
            + testRecognition.speechFragment
            + answerTypeRecording.getResult().substring(testRecognition.selectionRange.endOffset);
        answerTypeRecording.setResult(match);
    }
    else {
        if (window.getSelection) {
            window.getSelection().removeAllRanges();
            var range = document.createRange();
            range.selectNode(answerTypeRecording.getElement().find('.recognition-result')[0]);
            window.getSelection().addRange(range);
        }

        var result = answerTypeRecording.getResult();
        if (result.length) {
            //WikidsStoryTest.nextQuestion();
        }
        else {
            answerTypeRecording.setStatus('Речи не обнаружено');
            answerTypeRecording.repeatButtonShow();
        }
    }
};

testRecognition.final_transcript = '';

testRecognition.selectionRange = null;
testRecognition.speechFragment = '';

//testRecognition.resultTimeout = 0;

testRecognition.recorder.onresult = function(event) {

    //clearTimeout(testRecognition.resultTimeout);
    answerTypeRecording.getElement()
        .find('.recognition-result').blur();

    var interim_transcript = '';
    if (typeof(event.results) == 'undefined') {
        testRecognition.recorder.onend = null;
        testRecognition.recorder.stop();
        return;
    }

    for (var i = event.resultIndex; i < event.results.length; ++i) {
        if (event.results[i].isFinal) {
            testRecognition.final_transcript += event.results[i][0].transcript;
        } else {
            interim_transcript += event.results[i][0].transcript;
        }
    }

    if (testRecognition.recognizingFragment) {
        testRecognition.speechFragment = testRecognition.lowerCase(testRecognition.final_transcript);
    }
    else {
        testRecognition.final_transcript = testRecognition.lowerCase(testRecognition.final_transcript);
        answerTypeRecording.setResult(testRecognition.linebreak(testRecognition.final_transcript));
        answerTypeRecording.setResultInterim(testRecognition.linebreak(interim_transcript));
    }

    //if (testRecognition.final_transcript.length) {
/*        testRecognition.resultTimeout = setTimeout(function() {
            testRecognition.endSpeech();
            testRecognition.recorder.onend = null;
            testRecognition.recorder.stop();
        }, 1500);*/
    //}
};

testRecognition.recorder.onspeechend = function() {
    console.log('onspeechend');
}

testRecognition.linebreak = function(s) {
    var two_line = /\n\n/g;
    var one_line = /\n/g;
    return s.replace(two_line, '<p></p>').replace(one_line, '<br>');
};

testRecognition.capitalize = function(s) {
    var first_char = /\S/;
    return s.replace(first_char, function(m) { return m.toUpperCase(); });
}

testRecognition.lowerCase = function(s) {
    return s.toLowerCase();
}


var SlideLoader = (function() {

    var $element = $('<div/>')
        .addClass('wikids-test-loader')
        .append($('<p/>').text('Загрузка вопросов'))
        .append($('<img/>').attr('src', '/img/loading.gif'));

    function show() {
        WikidsStoryTest.getCurrentQuestionElement().append($element);
    }

    function hide() {

    }

    return {
        'show': show,
        'hide': hide
    };
})();