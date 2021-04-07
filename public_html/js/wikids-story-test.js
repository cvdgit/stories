
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
        testQuestions = [];
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
    var linked;
    var missingWords,
        missingWordsRecognition,
        recordingAnswer;

    var speech;

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
        linked = new TestLinked(testData['stories']);

        questionsRepeat = new QuestionsRepeat(questions, 5);
        testProgress = new TestProgress(getProgressData());

        numPad = new AnswerTypeNumPad();

        speech = new TestSpeech();

        if (testConfig.answerTypeIsMissingWords()) {
            missingWordsRecognition = new MissingWordsRecognition(testConfig);
            missingWords = new MissingWords(missingWordsRecognition);
        }

        if (testConfig.answerTypeIsRecording()) {
            recordingAnswer = RecordingAnswer(new MissingWordsRecognition(testConfig))
        }

        makeTestQuestions();
        //console.log(testQuestions);

        setupDOM();
        addEventListeners();

        if (testConfig.answerTypeIsMissingWords()) {
            dom.nextButton.off("click").on("click", function() {
                var result = missingWords.getResult();
                missingWords.resetMatchElements();
                nextQuestion([result]);
            });
        }
        if (testConfig.answerTypeIsRecording()) {
            dom.nextButton.off("click").on("click", function() {
                var result = recordingAnswer.getResult();
                recordingAnswer.resetResult();
                nextQuestion([result]);
            });
        }

        start();

        if (for_slide === undefined) {
            for_slide = true;
        }
        if (for_slide && testConfig.sourceIsLocal()) {
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
            .addClass('btn correct-answer-page-next')
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
                        //if (elem) {
                        //    $(elem).parent()[0].click();
                        //}
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

        var originalImageExists = answer['original_image'] === undefined ? true : answer['original_image'];

        var $answer = $("<div/>").addClass("wikids-test-answer")
            .on("click", function(e) {
                var tagName = e.target.tagName;
                var tags = ['INPUT'];
                if (originalImageExists) {
                    tags.push('IMG');
                }
                if ($.inArray(tagName, tags) === -1) {
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
                .attr('height', 100);
            if (originalImageExists) {
                $image
                    .css('cursor', 'zoom-in')
                    .on('click', function () {
                        showOriginalImage($(this).attr('src'), this);
                    });
            }
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

    function generateAnswerList(answers, num) {

        var list = answers.filter(function(answer) {
            return answer.is_correct === 1;
        });

        function sample(population, k){
            if (!Array.isArray(population)) {
                throw new TypeError("Population must be an array.");
            }
            var n = population.length;
            if (k < 0 || k > n) {
                throw new RangeError("Sample larger than population or is negative");
            }
            var result = new Array(k);
            var setsize = 21;   // size of a small set minus size of an empty list
            if (k > 5) {
                setsize += Math.pow(4, Math.ceil(Math.log(k * 3) / Math.log(4)))
            }
            if (n <= setsize) {
                // An n-length list is smaller than a k-length set
                var pool = population.slice();
                for (var i = 0; i < k; i++) {          // invariant:  non-selected at [0,n-i)
                    var j = Math.random() * (n - i) | 0;
                    result[i] = pool[j];
                    pool[j] = pool[n - i - 1];       // move non-selected item into vacancy
                }
            } else {
                var selected = new Set();
                for (var i = 0; i < k; i++) {
                    var j = Math.random() * n | 0;
                    while (selected.has(j)) {
                        j = Math.random() * n | 0;
                    }
                    selected.add(j);
                    result[i] = population[j];
                }
            }
            return result;
        }

        var max = num - list.length;
        sample(answers.filter(function(answer) {
            return answer.is_correct !== 1;
        }), max).map(function(elem) {
            list.push(elem);
        });

        return shuffle(list);
    }

    function getQuestionAnswerNumber(question) {
        return parseInt(question.answer_number);
    }

    function getQuestionView(question) {
        return question.view;
    }

    function createAnswers(answers, question) {

        var num = getQuestionAnswerNumber(question);
        if (testConfig.sourceIsNeo() && num > 0) {
            answers = generateAnswerList(answers, num);
        }
        else {
            var mixAnswers = question.mix_answers || 0;
            if (parseInt(mixAnswers) === 1 || testConfig.sourceIsNeo()) {
                answers = shuffle(answers);
            }
        }

        var $answers = $("<div/>").addClass("wikids-test-answers");
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
        var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper"></div></div>');
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
        var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper text-center"></div></div>');
        $wrapper.find(".question-wrapper").append($answers);
        return $wrapper;
    }

    function createRecordingAnswer(question, answer) {
        return recordingAnswer.create(question, answer);
    }

    function createRegionAnswer(question, answers) {
        var regionQuestion = new RegionQuestion(question, answers);
        return regionQuestion.create();
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

    function createRegionAnswers(question, answers) {
        var $answers = $("<div/>").addClass("wikids-test-answers");
        $answers.append(createRegionAnswer(question, answers));
        var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper"></div></div>');
        $wrapper.find(".question-wrapper").append($answers);
        return $wrapper;
    }

    function createMissingWordsAnswer(question, answer) {
        return missingWords.init(question, answer);
    }

    function createMissingWordsAnswers(question, answers) {
        var $answers = $("<div/>").addClass("wikids-test-answers");
        answers.forEach(function(answer) {
            $answers.append(createMissingWordsAnswer(question, answer));
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
        appendStars($elem, 5, stars.current);
        return $elem;
    }

    function getCurrentProgressStateText() {
        return 'Вопрос ' + testProgress.getCurrent() + ' из ' + testProgress.getTotal();
    }

    function progressValue(value) {
        return ' ' + value + '% ';
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
                    .css('minWidth', '2em')
                    .text(progressValue(progress))
            )[0].outerHTML;
    }

    function updateProgress() {
        var progress = testProgress.calcPercent();
        $('.wikids-progress', dom.header).attr('title', getCurrentProgressStateText()).tooltip('fixTitle');
        $('.wikids-progress .progress-bar', dom.header)
            .css('width', progress + '%')
            .text(progressValue(progress));
    }

    function createQuestion(question) {

        var questionName = question.name;
        if (question['correct_number'] && question.correct_number > 1) {
            questionName += ' (верных ответов: ' + question.correct_number + ')';
        }

        if (testConfig.answerTypeIsMissingWords()) {
            questionName = 'Заполните пропущенные части';
        }

        var titleElement = $('<p/>')
            .addClass('question-title')
            .append(questionName);

        var stars = '';
        if (question['stars']) {
            stars = createStars(question.stars);
        }
        return $("<div/>")
            .hide()
            .addClass("wikids-test-question")
            .append(stars)
            .append(titleElement)
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
            if (testConfig.answerTypeIsMissingWords()) {
                view = 'missing_words';
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
                case 'region':
                    $answers = createRegionAnswers(question, getAnswersData(question));
                    break;
                case 'missing_words':
                    $answers = createMissingWordsAnswers(question, getAnswersData(question));
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
            .empty()
            .append("<h2>Тест успешно пройден</h2>")
            .append(linked.getHtml())
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

    function getCorrectAnswers(question) {
        return getAnswersData(question).filter(function(elem) {
            return parseInt(elem.is_correct) === 1;
        });
    }

    function createAnswerSteps(answers) {
        var steps = [];
        answers.map(function(item) {
            if (/(\d+)#([\wа-яА-ЯёЁ]+)/ui.test(item.name)) {
                var match;
                var re = /(\d+)#([\wа-яА-ЯёЁ]+)/uig;
                var parts = {};
                while ((match = re.exec(item.name)) !== null) {
                    parts[match[0]] = [];
                    parts[match[0]].push(match[1]);
                    parts[match[0]].push(match[2]);
                }
                for (var i = 0, str = ''; i < 2; i++) {
                    str = item.name;
                    for (var [key, value] of Object.entries(parts)) {
                        str = str.replace(key, value[i]);
                    }
                    steps.push(str);
                }
            }
        });
        return steps;
    }

    function correctAnswerSteps(steps, userAnswers) {
        return userAnswers.every(function(userAnswer) {
            return steps.some(function(stepAnswer) {
                return userAnswer === stepAnswer;
            });
        });
    }

    function checkAnswerCorrect(question, answer, correctAnswersCallback, convertAnswerToInt) {
        console.debug('WikidsStoryTest.checkAnswerCorrect');

        var correctAnswers = getAnswersData(question).filter(function(elem) {
            return parseInt(elem.is_correct) === 1;
        });

        var steps = createAnswerSteps(correctAnswers);
        var correct = false;
        if (steps.length > 0) {
            correct = correctAnswerSteps(steps, answer);
        }
        else {
            correctAnswers = correctAnswers.map(correctAnswersCallback);
            var answerCheckCallback = function (value, index) {
                if (convertAnswerToInt) {
                    value = parseInt(value)
                }
                return value === correctAnswers.sort()[index];
            };

            if (answer.length === correctAnswers.length && answer.sort().every(answerCheckCallback)) {
                correctAnswersNumber++;
                correct = true;
            }
        }

        return correct;
    }

    function answerQuestion(element, answer, correctAnswersCallback, convertAnswerToInt) {
        console.debug('WikidsStoryTest.answerQuestion');
        var questionID = element.attr("data-question-id");
        var question = getQuestionsData().filter(function(elem) {
            return parseInt(elem.id) === parseInt(questionID);
        });
        var correctAnswers = getAnswersData(question[0]).filter(function(elem) {
            return parseInt(elem.is_correct) === 1;
        });

        var steps = createAnswerSteps(correctAnswers);
        var correct = false;
        if (steps.length > 0) {
            correct = correctAnswerSteps(steps, answer);
        }
        else {
            correctAnswers = correctAnswers.map(correctAnswersCallback);
            var answerCheckCallback = function(value, index) {
                if (convertAnswerToInt) {
                    value = parseInt(value)
                }
                return value === correctAnswers.sort()[index];
            };
            if (answer.length === correctAnswers.length && answer.sort().every(answerCheckCallback)) {
                correctAnswersNumber++;
                correct = true;
            }
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
        if (testConfig.isStrictAnswer()) {
            return [val];
        }
        return [val.toLowerCase()];
    }

    /*function getRecognitionQuestionAnswers(element) {
        var val = answerTypeRecording.getResult();
        if (!val.length) {
            return [];
        }
        return [val.toLowerCase()];
    }*/

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
        var text = currentQuestion.name;
        if (testConfig.answerTypeIsInput()) {
            text = answer[0];
        }
        var $content = questionSuccess.create(action, text, currentQuestion.image);
        dom.wrapper.append($content)
        $content.fadeIn();
    }

    function getQuestionRememberAnswers(question) {
        return question['rememberAnswer'] || false;
    }

    function changeQuestionRememberAnswers(question, answer) {
        question.rememberAnswer = false;
        question.storyTestAnswers[0].name = answer[0];
    }

    /* Ответ на вопрос */
    function nextQuestion(preparedAnswers) {

        console.debug('WikidsStoryTest.nextQuestion');
        if (!Array.isArray(preparedAnswers)) {
            preparedAnswers = false;
        }
        preparedAnswers = preparedAnswers || false;

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
        if (!preparedAnswers) {
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
                /*case 'recognition':
                    answer = getRecognitionQuestionAnswers($activeQuestion);
                    answerTypeRecording.resetResult();
                    break;*/
                default:
                    answer = getQuestionAnswers($activeQuestion);
            }
        }
        else {
            answer = preparedAnswers;
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
        if (view === 'input' || view === 'recognition' || testConfig.answerTypeIsMissingWords()) {
            correctAnswersCallback = function(elem) {
                if (testConfig.isStrictAnswer()) {
                    return elem.name;
                }
                else {
                    return elem.name.toLowerCase();
                }
            };
            convertAnswerToInt = false;
        }

        var rememberAnswer = getQuestionRememberAnswers(currentQuestion);
        if (!rememberAnswer) {
            answerIsCorrect = answerQuestion($activeQuestion, answer, correctAnswersCallback, convertAnswerToInt);
        }
        else {
            changeQuestionRememberAnswers(currentQuestion, answer);
            answerIsCorrect = true;
        }

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
                    'test_id': testConfig.getTestID(),
                    'student_id': currentStudent.id,
                    'question_topic_id': currentQuestion.topic_id,
                    'question_topic_name': currentQuestion.name,
                    'entity_id': currentQuestion.id,
                    'entity_name': currentQuestion.entity_name,
                    'relation_id': currentQuestion.relation_id,
                    'relation_name': currentQuestion.relation_name,
                    'correct_answer': answerIsCorrect ? 1 : 0,
                    'answers': answerList,
                    'progress': testProgress.calcPercent(),
                    'stars': questionsRepeat.number(currentQuestion)
                };
                $.post('/question/answer', answerParams);
            }
            if (testConfig.sourceIsWord() && !testConfig.answerTypeIsInput()) {
                answerList = answer.map(function (answerText) {
                    return {
                        'answer_entity_id': currentQuestion.id,
                        'answer_entity_name': answerText
                    };
                });
                answerParams = {
                    'source': testConfig.getSource(),
                    'test_id': testConfig.getTestID(),
                    'student_id': currentStudent.id,
                    'entity_id': currentQuestion.id,
                    'entity_name': currentQuestion.name,
                    'correct_answer': answerIsCorrect ? 1 : 0,
                    'answers': answerList,
                    'progress': testProgress.calcPercent(),
                    'stars': questionsRepeat.number(currentQuestion)
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
                    'test_id': testConfig.getTestID(),
                    'student_id': currentStudent.id,
                    'entity_id': currentQuestion.id,
                    'entity_name': currentQuestion.name,
                    'correct_answer': answerIsCorrect ? 1 : 0,
                    'answers': answerList,
                    'progress': testProgress.calcPercent(),
                    'stars': questionsRepeat.number(currentQuestion)
                };
                $.post('/question/answer', answerParams);
            }
            if (testConfig.sourceIsLocal()) {
                answerList = answer.map(function (entity_id) {
                    var answer = answerByID(currentQuestion, entity_id);
                    return {
                        'answer_entity_id': entity_id,
                        'answer_entity_name': answer ? answer.name : 'no correct'
                    };
                });
                answerParams = {
                    'source': testConfig.getSource(),
                    'test_id': testConfig.getTestID(),
                    'student_id': currentStudent.id,
                    'entity_id': currentQuestion.id,
                    'entity_name': currentQuestion.name,
                    'correct_answer': answerIsCorrect ? 1 : 0,
                    'answers': answerList,
                    'progress': testProgress.calcPercent(),
                    'stars': questionsRepeat.number(currentQuestion)
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
            if (testConfig.sourceIsWord()
                && !testConfig.answerTypeIsNumPad()
                && !testConfig.answerTypeIsInput()
                && !testConfig.answerTypeIsMissingWords()) {
                continueTestAction(answer);
            }
            else {
                dom.results
                    .html("<p>Ответ не верный.</p>")
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
        //console.log('current:', currentQuestion);

        currentQuestionElement = $('.wikids-test-question[data-question-id=' + nextQuestion.id + ']', dom.questions);

        if (getQuestionView(currentQuestion) !== 'svg' && testConfig.sourceIsNeo()) {
            $('.wikids-test-answers', currentQuestionElement)
                .empty()
                .append(createAnswers(getAnswersData(currentQuestion), currentQuestion)
                    .find('.wikids-test-answers > div'));
        }

        currentQuestionElement
            .find('input[type=checkbox],input[type=radio]').prop('checked', false).end()
            .slideDown()
            .addClass('wikids-test-active-question');

        if (testConfig.sourceIsWord()) {
            dom.nextButton.hide();
        }

        if (testConfig.answerTypeIsInput()) {

            var text = getAnswersData(nextQuestion)[0].name;
            var q = $('.wikids-test-active-question .answer-input', dom.questions);
            setTimeout(function () {
                //testSpeech.ReadText(text);
                speech.readText(text, testConfig.getInputVoice());
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
                    //testSpeech.ReadText(text);
                    speech.readText(text, testConfig.getInputVoice());
                });
        }

        if (testConfig.answerTypeIsRecording()) {
            recordingAnswer.autoStart(new Event('autoStart'));
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

    function textDiff(a, b) {
        var diff = patienceDiff(a.split(''), b.split(''));
        var diffAnswer = '';
        diff.lines.forEach(function(line) {
            var char = '',
                color = 'red';
            if (line.aIndex >= 0) {
                char = line.line;
                if (line.bIndex === -1) {
                    color = 'red';
                }
                if (line.aIndex === line.bIndex) {
                    color = 'green';
                }
            }
            if (char.length) {
                diffAnswer += '<span style="color: ' + color + '">' + char + '</span>';
            }
        });
        return diffAnswer;
    }

    function showCorrectAnswerPage(question, answer) {
        console.debug('WikidsStoryTest.showCorrectAnswerPage');
        var $elements = $('<div/>');

        var text = incorrectAnswerText || 'Правильный ответ';
        text = text.replace('{1}', question.entity_name);
        $elements.append($('<h4/>').text(text + ':'));

        var $element;
        var answerText = '';
        var userAnswer = answer[0];
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
                        .append($('<span/>').text(answerText))
                        .append($('<a/>')
                            .attr('href', '#')
                            .attr('title', 'Прослушать')
                            .css('font-size', '3rem')
                            .on('click', function(e) {
                                e.preventDefault();
                                //testSpeech.ReadText(questionAnswer.name);
                                speech.readText(questionAnswer.name, testConfig.getInputVoice());
                            })
                            .html('<i class="glyphicon glyphicon-volume-up" style="left: 10px; top: 6px"></i>')
                        );
                }
                else {
                    if (testConfig.answerTypeIsInput()) {
                        $answerElement = $('<p/>').html(textDiff(answerText, userAnswer));
                    }
                    else {
                        $answerElement = $('<p/>').text(answerText);
                    }
                }
                $content.append($answerElement);

                if (testConfig.answerTypeIsInput()) {
                    $('<p/>').html('&nbsp;').appendTo($content);
                    $('<p/>')
                        .text('Ваш ответ:')
                        .appendTo($content);
                    $('<p/>')
                        .html(userAnswer)
                        .appendTo($content);
                }

                $elements.append($element.append($content));
            }
        });

        if (testConfig.answerTypeIsRecording()) {
            dom.correctAnswerPage.find('.correct-answer-page-next').hide();
        }

        dom.correctAnswerPage
            //.find('.wikids-test-correct-answer-page-header').text(question.name).end()
            .find('.wikids-test-correct-answer-answers').empty().html($elements[0].childNodes).end()
            .show();

        if (testConfig.answerTypeIsRecording()) {
            setTimeout(function() {
                //testSpeech.ReadText(answerText, correctAnswerPageNext);
                speech.readText(answerText, testConfig.getInputVoice(), correctAnswerPageNext);
            }, 600);
        }
    }

    function showNextButton() {
        //if (!testConfig.answerTypeIsNumPad() && !testConfig.answerTypeIsInput() && !testConfig.answerTypeIsRecording()) {
        if (!testConfig.sourceIsWord()) {
            dom.nextButton.show();
        }
    }

    function continueTestAction(answer) {
        console.debug('continueTestAction');

        dom.continueButton.hide();
        var isLastQuestion = (testQuestions.length === 0);
        // var actionRelated = incorrectAnswerActionRelated();
        var showCorrectAnswerPageCondition = testConfig.sourceIsWord()
            && !testConfig.answerTypeIsNumPad()
            && !testConfig.answerTypeIsRecording()
            && !testConfig.answerTypeIsInput()
            && !testConfig.answerTypeIsMissingWords();

        if (isLastQuestion) {

            if (!answerIsCorrect) {
                if (showCorrectAnswerPageCondition) {
                    showNextQuestion();
                    dom.results.hide();
                    showNextButton();
                }
                else {
                    showCorrectAnswerPage(currentQuestion, answer);
                }
            }
            else {
                if (testConfig.sourceIsLocal()) {
                    dispatchEvent("backToStory", {});
                }
                else {
                    finish();
                }
            }
        }
        else {
            if (!answerIsCorrect) {
                if (showCorrectAnswerPageCondition) {
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
        },
        "checkAnswerCorrect": checkAnswerCorrect,
        "getTestConfig": function() {
            return testConfig;
        },
        "getCorrectAnswer": function(question) {
            return getCorrectAnswers(question);
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

    var html = '<div class="keyboard-wrapper"><ul id="keyboard" class="clearfix">' +
        '<li class="letter">0</li>' +
        '<li class="letter-empty"></li>' +
        '<li class="letter">1</li>' +
        '<li class="letter">2</li>' +
        '<li class="letter">3</li>' +
        '<li class="letter">4</li>' +
        '<li class="letter">5</li>' +
        '<li class="letter">6</li>' +
        '<li class="letter">7</li>' +
        '<li class="letter">8</li>' +
        '<li class="letter">9</li>' +
        '<li class="letter">10</li>' +
        '<li class="letter-empty clearl"></li>' +
        '<li class="letter-empty"></li>' +
        '<li class="letter">11</li>' +
        '<li class="letter">12</li>' +
        '<li class="letter">13</li>' +
        '<li class="letter">14</li>' +
        '<li class="letter">15</li>' +
        '<li class="letter">16</li>' +
        '<li class="letter">17</li>' +
        '<li class="letter">18</li>' +
        '<li class="letter">19</li>' +
        '<li class="letter">20</li>' +
        '</ul>' +
        '<p></p></div>',
        $html = $(html);

    $html.find('li.letter').on('click', function() {
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
    //var $html = $('<input type="text" class="answer-input" style="width: 80%" />');
    var $html = $('<textarea class="answer-input" style="width: 80%" rows="5" />');
    $html.keypress(function(e) {
        if (e.which == 13) {
            action();
            return false;
        }
    });
    return $html;
};

/*
var testSpeech = {};
testSpeech.Synth = window.speechSynthesis;
testSpeech.Voices = [];
testSpeech.Voices = testSpeech.Synth.getVoices();
testSpeech.VoiceIndex = 0;
testSpeech.Rate = 1;
testSpeech.Pitch = 1;
testSpeech.ReadText = function(txt, afterSpeech) {
    var ttsSpeechChunk = new SpeechSynthesisUtterance(txt);
    var inputVoice = WikidsStoryTest.getTestConfig().getInputVoice() || 'Google русский';
    for (var i = 0; i < testSpeech.Synth.getVoices().length ; i++) {
        if (testSpeech.Synth.getVoices()[i].name === inputVoice) {
            ttsSpeechChunk.voice = testSpeech.Synth.getVoices()[i];
            break;
        }
    }
    ttsSpeechChunk.rate = testSpeech.Rate;
    ttsSpeechChunk.pitch = testSpeech.Pitch;
    if (afterSpeech) {
        ttsSpeechChunk.onend = afterSpeech;
    }
    testSpeech.Synth.speak(ttsSpeechChunk);
};
*/

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

var TestLinked = function(data) {

    var stories = [];

    function init() {
        if (!data || !data.length) {
            return;
        }
        stories = data;
    }

    init();

    function getHtml() {
        var $wrapper = $('<div/>')
            .addClass('test-linked-stories-wrapper');
        $wrapper.append($('<p/>').text('Посмотрите историю'))
        stories.forEach(function(story) {
            $('<a/>')
                .attr('href', story['url'])
                .css('display', 'block')
                .append(
                    $('<img/>').attr('src', story['image'])
                )
                .append($('<p/>').text(story['title']))
                .appendTo($wrapper);
        });
        return $wrapper;
    }

    var API = {};
    API.getHtml = getHtml;
    return API;
}

var RegionQuestion = function(question, questionAnswers) {

    this.question = question;
    this.questionAnswers = questionAnswers;

    var answers = [];
    this.addAnswer = function(answer) {
        if (answers.indexOf(answer) === -1) {
            answers.push(answer);
        }
    };
    this.getAnswers = function() {
        return answers;
    };
    this.resetAnswers = function() {
        answers = [];
    }

    this.getAnswerByRegion = function(region) {
        return questionAnswers.filter(function(answer) {
            return answer.region_id === region;
        });
    };
}
RegionQuestion.prototype.create = function() {

    var $img = $('<img/>')
        .attr('src', this.question.params.image)
        .css({'position': 'absolute', 'left': 0, 'top': 0, 'width': '100%'});

    function getRelativeCoordinates(event, target) {

        const position = {
            x: event.clientX,
            y: event.clientY
        };
        var container = $('.reveal .slides')[0]

        var scaleX = parseFloat(target.offsetWidth  / target.getBoundingClientRect().width).toFixed(2);
        var scaleY = parseFloat(target.offsetHeight  / target.getBoundingClientRect().height).toFixed(2);

        var offset = $(target).offset();
        var canvasOffsetLeft = offset.left;
        var canvasOffsetTop = offset.top;

        return {
            x: (position.x - canvasOffsetLeft + $(window).scrollLeft()) / Reveal.getScale(),
            y: (position.y - canvasOffsetTop + $(window).scrollTop()) / Reveal.getScale()
        };
    }

    var that = this;
    var $wrapper = $('<div/>')
        .addClass('question-region')
        .css({'width': '640px', 'height': '480px', 'position': 'relative'})
        .on('click', function(e) {

            var rect = getRelativeCoordinates(e, $wrapper[0]);

            $('<span/>')
                .addClass('answer-point')
                .css({
                    'position': 'absolute',
                    'left': rect.x,
                    'top': rect.y,
                    'shape-outside': 'circle()',
                    'clip-path': 'circle()',
                    'background': 'orangered',
                    'width': '3rem',
                    'height': '3rem'
                })
                .appendTo(this);

            var $target  = $(e.target);
            var isRect = $target[0].tagName === 'DIV' && $target.hasClass('answer-rect');

            setTimeout(function() {

                if (isRect) {
                    var regionID = $target.attr('data-answer-id');
                    var answer = that.getAnswerByRegion(regionID);
                    that.addAnswer(answer[0].id);
                }
                else {
                    that.addAnswer('no correct');
                }
                WikidsStoryTest.nextQuestion(that.getAnswers());
                that.resetAnswers();
                $wrapper.find('span.answer-point').remove();
            }, 500);
        })
        .append($img);

    this.question.params.regions.forEach(function(region) {
        $('<div/>')
            .addClass('answer-rect')
            .attr('data-answer-id', region.id)
            .css({
                'position': 'absolute',
                'left': parseInt(region.rect.left) + 'px',
                'top': parseInt(region.rect.top) + 'px',
                'width': parseInt(region.rect.width) + 'px',
                'height': parseInt(region.rect.height) + 'px'
            })
            .appendTo($wrapper);
    });
    return $wrapper;
};

var TestConfig = function(data) {

    function getSource() {
        return parseInt(data.source);
    }

    function getAnswerType() {
        return parseInt(data.answerType);
    }

    return {
        'getSource': getSource,
        'sourceIsLocal': function() {
            return getSource() === 1;
        },
        'sourceIsNeo': function() {
            return getSource() === 2;
        },
        'sourceIsWord': function() {
            return getSource() === 3;
        },
        'answerTypeIsDefault': function() {
            return getAnswerType() === 0;
        },
        'answerTypeIsNumPad': function() {
            return getAnswerType() === 1;
        },
        'answerTypeIsInput': function() {
            return getAnswerType() === 2;
        },
        'answerTypeIsRecording': function() {
            return getAnswerType() === 3;
        },
        'answerTypeIsMissingWords': function() {
            return getAnswerType() === 4;
        },
        'isStrictAnswer': function() {
            return parseInt(data.strictAnswer);
        },
        'getInputVoice': function() {
            return data.inputVoice;
        },
        'getRecordingLang': function() {
            return data.recordingLang;
        },
        'isRememberAnswers': function() {
            return data.rememberAnswers;
        },
        'getTestID': function() {
            return parseInt(data.id);
        }
    }
}

var Morphy = function() {

    var API = {};

    API.correctResult = function(match, result) {
        return $.post('/morphy/root', {
            match, result
        });
    }

    return API;
};

var MissingWords = function(recognition) {

    var elements = [];

    recognition.addEventListener('onStart', function() {
        WikidsStoryTest.hideNextButton();
        var element = getElement(WikidsStoryTest.getCurrentQuestion().id);
        setStatus(element, 'Идет запись с микрофона');
    });

    recognition.addEventListener('onResult', function(event) {
        var args = event.args;
        var elem = $(args.target);
        var match = elem.attr('data-match')
        var result = $.trim(args.result);
        elem.text(result);
        if (result.length >= match.length) {
            recognition.stop();
        }
    });

    function correctResult(match, result) {
        return $.post('/morphy/root', {
            match, result
        });
    }

    recognition.addEventListener('onEnd', function(event) {

        var element = getElement(WikidsStoryTest.getCurrentQuestion().id);
        hideLoader(element);
        hideStopButton(element);
        setStatus(element);

        var args = event.args,
            elem = $(args.target),
            match = elem.attr('data-match');

        var result = getMissingWordsText(element);
        if (checkResult(result)) {
            resetMatchElements();
            WikidsStoryTest.nextQuestion([result]);
        }
        else {
            correctResult(match, args.result).done(function(response) {
                elem.text(response.result);
                result = getMissingWordsText(element);
                if (checkResult(result)) {
                    resetMatchElements();
                    WikidsStoryTest.nextQuestion([result]);
                }
                else {
                    WikidsStoryTest.showNextButton();
                }
            });
        }
    });

    function createRepeatString(string) {
        return string.split(' ').map(function(word) {
            return '*'.repeat(word.length);
        }).join('_');
    }

    function createMaskedString(string) {
        var re = /\{([\wа-яА-ЯёЁ\s]+)\}/igm;
        var match;
        while ((match = re.exec(string)) !== null) {
            string = string.replace(match[0], '<span style="cursor:pointer" class="label label-primary" data-match="'+match[1]+'">' + createRepeatString(match[1]) + '</span>')
        }
        return string;
    }

    function resetMatchElements() {
        var element = getElement(WikidsStoryTest.getCurrentQuestion().id);
        $('.missing-words-text', element).find('span.label').each(function() {
            var match = $(this).attr('data-match');
            $(this).text(createRepeatString(match));
        });
    }

    function init(question, answer) {

        var element = $('<div/>', {
            'class': 'missing-words test-recognition'
        });

        element.data('correctAnswer', answer.name);

        element
            .append($('<p/>', {
                'class': 'missing-words-text',
                'html': createMaskedString(question.name)
            }));

        element.on('click', 'span.label', function (e) {
            start(e, question.id, $(this).attr('data-match'));
        });

        element
            .append($('<div/>')
                .addClass('recognition-result-wrapper')
                .append($('<span/>').addClass('recognition-result').css('background-color', 'inherit'))
                .append($('<span/>').addClass('recognition-result-interim'))
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
            .attr('title', 'Остановить')
            .addClass('recognition-stop')
            .on('click', function(e) {
                e.preventDefault();
                recognition.stop();
            })
            .append($('<i/>').addClass('glyphicon glyphicon-stop'))
            .hide()
            .appendTo(element);

        elements[question.id] = element;
        return element;
    }

    function getElement(id) {
        return elements[id];
    }

    function showLoader(element) {
        element.find('.wikids-test-loader').show();
    }

    function hideLoader(element) {
        element.find('.wikids-test-loader').hide();
    }

    function setStatus(element, status) {
        status = status || '';
        element.find('.recognition-status').text(status);
    }

    function showStopButton(element) {
        element.find('.recognition-stop').show();
    }

    function hideStopButton(element) {
        element.find('.recognition-stop').hide();
    }

    function checkResult(result) {
        return WikidsStoryTest.checkAnswerCorrect(
            WikidsStoryTest.getCurrentQuestion(),
            [result],
            function(elem) {
                return elem.name.toLowerCase();
            },
            false);
    }

    function getMissingWordsText(element) {
        return $.trim(element.find('.missing-words-text').text()).toLowerCase();
    }

    function start(event, questionID, match) {
        var element = getElement(questionID);
        setStatus(element);
        showLoader(element);
        showStopButton(element);
        recognition.start(event, match);
    }

    return {
        'init': init,
        'start': start,
        'getResult': function() {
            var element = getElement(WikidsStoryTest.getCurrentQuestion().id);
            return getMissingWordsText(element);
        },
        'resetMatchElements': resetMatchElements
    };
};

var RecognitionControl = function() {

    function getElement() {
        return WikidsStoryTest.getCurrentQuestionElement();
    }

    var API = {}

    API.showLoader = function() {
        getElement().find('.wikids-test-loader').show();
    };

    API.hideLoader = function() {
        getElement().find('.wikids-test-loader').hide();
    };

    API.setStatus = function(status) {
        status = status || '';
        getElement().find('.recognition-status').text(status);
    };

    API.showStopButton = function() {
        getElement().find('.recognition-stop').show();
    }

    API.hideStopButton = function() {
        getElement().find('.recognition-stop').hide();
    }

    API.getResult = function() {
        return getElement().find('.recognition-result').text();
    }

    API.setResult = function(text) {
        text = text || '';
        getElement().find('.recognition-result').text(text).trigger('input');
    }

    API.disableResult = function() {
        getElement().find('.recognition-result').prop('contenteditable', false);
    };

    API.enableResult = function() {
        getElement().find('.recognition-result').prop('contenteditable', true);
    };

    API.setFragmentResult = function(fragment, range) {
        var result = API.getResult();
        var match = result.substring(0, range.startOffset)
            + fragment
            + result.substring(range.endOffset);
        API.setResult(match);
    }

    API.showRepeatWord = function() {
        return getElement().find('.recognition-repeat-word').show();
    };

    API.hideRepeatWord = function() {
        return getElement().find('.recognition-repeat-word').hide();
    };

    API.getQuestionTitle = function() {
        return $.trim(getElement().find('.question-title').text());
    };

    API.repeatButtonShow = function() {
        getElement().find('.recognition-repeat').show();
    };

    API.repeatButtonHide = function() {
        getElement().find('.recognition-repeat').hide();
    };

    API.getCurrentCorrectAnswer = function() {
        return WikidsStoryTest.getCorrectAnswer(WikidsStoryTest.getCurrentQuestion())
            .map(function(elem) {
                return $.trim(elem.name);
            })
            .join('');
    }

    API.resultSetFocus = function() {
        getElement().find('.recognition-result').focus();
    };

    return API;
}

var RecordingAnswer = function(recognition) {

    var control = new RecognitionControl();

    function create(question, answer) {

        var element = $('<div/>');
        element.addClass('test-recognition');

        element
            .append($('<div/>')
                .addClass('recognition-result-wrapper')
                .append(
                    $('<div/>')
                        .prop('contenteditable', true)
                        .addClass('recognition-result')
                        .on('input', function(e) {
                            var value = $(this).text();
                            value.length > 0
                                ? WikidsStoryTest.showNextButton()
                                : WikidsStoryTest.hideNextButton();
                        })
                        .on('keydown', function(e) {
                            if (e.key === "Enter") {
                                e.preventDefault();
                                var value = $(this).text();
                                if (value.length > 0) {
                                    resetResult();
                                    WikidsStoryTest.nextQuestion([value]);
                                }
                            }
                        })
                )
                .append($('<span/>').addClass('recognition-result-interim'))
            );

        /*
        element.append(
            $('<div/>')
                .css('text-align', 'center')
                .append(
                    $('<a/>')
                        .attr('href', '#')
                        .attr('title', 'Повторить фрагмент')
                        .on('click', function(e) {
                            e.preventDefault();
                            var range = window.getSelection().getRangeAt(0);
                            if (!range.toString().length) {
                                return;
                            }
                            startFragment(range, e);
                        })
                        .hide()
                        .addClass('recognition-repeat-word')
                        .append($('<i/>').addClass('glyphicon glyphicon-refresh'))
                )
        );*/

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
                start(e);
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
                recognition.stop();
            })
            .append($('<i/>').addClass('glyphicon glyphicon-stop'))
            .hide()
            .appendTo(element);

        return element;
    }

    function start(event) {
        control.setStatus();
        control.setResult();
        control.repeatButtonHide();
        control.showLoader();
        recognition.start(event);
    }

    function autoStart(event) {
        control.setStatus();
        control.repeatButtonHide();
        setTimeout(function() {
            control.showLoader();
            recognition.start(event);
        }, 1000);
    }

    function startFragment(range, event) {
        control.showStopButton();
        recognition.start(event);
    }

    recognition.addEventListener('onStart', function() {
        WikidsStoryTest.hideNextButton();
        control.setStatus('Идет запись с микрофона');
        control.showStopButton();
        control.disableResult();
    });

    function checkResult(result) {
        return WikidsStoryTest.checkAnswerCorrect(
            WikidsStoryTest.getCurrentQuestion(),
            [result],
            function(elem) {
                return elem.name.toLowerCase();
            },
            false);
    }

    function getResult() {
        return control.getResult();
    }

    function resetResult() {
        control.setResult();
    }

    recognition.addEventListener('onEnd', function() {

        control.hideLoader();
        control.hideStopButton();
        control.setStatus();
        control.enableResult();
        var result = getResult();

        if (result.length === 0) {
            control.repeatButtonShow();
            control.resultSetFocus();
            return;
        }

        if (checkResult(result)) {
            resetResult();
            WikidsStoryTest.nextQuestion([result]);
        }
        else {
            var morphy = new Morphy();
            morphy.correctResult(control.getCurrentCorrectAnswer(), result).done(function(response) {
                result = response.result;
                control.setResult(result);
                if (checkResult(result)) {
                    resetResult();
                    WikidsStoryTest.nextQuestion([result]);
                }
                else {
                    WikidsStoryTest.showNextButton();
                }
            })
            control.repeatButtonShow();
            control.resultSetFocus();
        }
    });

    recognition.addEventListener('onError', function(event) {
        control.hideLoader();
        control.setStatus(event.args.error);
    });

    function checkResultLength(result, match) {
        return result.length >= match.replaceAll(/(\d+)#([\wа-яА-ЯёЁ]+)/uig, "$1").length;
    }

    recognition.addEventListener('onResult', function(event) {
        var args = event.args;
        var result = $.trim(args.result);
        control.setResult(result);
        var match = control.getCurrentCorrectAnswer();
        if (checkResultLength(result, match)) {
            recognition.stop();
        }
    });

    return {
        'create': create,
        'start': start,
        'autoStart': autoStart,
        'getResult': getResult,
        'resetResult': resetResult
    };
};

var MissingWordsRecognition = function(config) {

    var recorder = new webkitSpeechRecognition();
    recorder.continuous = true;
    recorder.interimResults = true;
    recorder.lang = config.getRecordingLang() || 'ru-RU';

    var recognizing = false;
    var startTimestamp = null;
    var finalTranscript = '';
    var targetElement;

    var eventListeners = [];

    recorder.onstart = function() {
        recognizing = true;
        dispatchEvent({type: 'onStart'});
    };

    recorder.onresult = function(event) {

        var interimTranscript = '';
        if (typeof(event.results) === 'undefined') {
            recorder.onend = null;
            recorder.stop();
            return;
        }

        for (var i = event.resultIndex; i < event.results.length; ++i) {
            if (event.results[i].isFinal) {
                finalTranscript = event.results[i][0].transcript;
            } else {
                interimTranscript += event.results[i][0].transcript;
            }
        }

        if (finalTranscript.length) {
            finalTranscript = lowerCase(finalTranscript);
            dispatchEvent({
                type: 'onResult',
                args: {
                    target: targetElement,
                    result: linebreak(finalTranscript),
                    interim: linebreak(interimTranscript)
                }
            });
        }
    };

    recorder.onend = function() {
        recognizing = false;
        dispatchEvent({
            type: 'onEnd',
            args: {
                target: targetElement,
                result: linebreak(finalTranscript)
            }
        });
    }

    function errorString(error) {
        var result = '';
        switch (error) {
            case 'no-speech': result = 'Речи не обнаружено'; break;
            case 'audio-capture': result = 'Не удалось захватить звук'; break;
            case 'not-allowed': result = 'Пользовательский агент запретил ввод речи из соображений безопасности, конфиденциальности или предпочтений пользователя'; break;
            default: result = error;
        }
        return result
    }

    recorder.onerror = function(event) {

        dispatchEvent({
            type: 'onError',
            args: {
                error: errorString(event.error)
            }
        });
    };

    function start(event, text) {
        if (recognizing) {
            recorder.stop();
            return;
        }
        finalTranscript = '';
        recorder.start();
        startTimestamp = event.timeStamp;
        targetElement = event.target;
    }

    function stop() {
        recorder.stop();
    }

    function dispatchEvent(event) {
        for (var i = 0; i < eventListeners.length; i++) {
            if (event.type === eventListeners[i].type) {
                eventListeners[i].eventHandler(event);
            }
        }
    }

    function linebreak(s) {
        var two_line = /\n\n/g;
        var one_line = /\n/g;
        return s.replace(two_line, '<p></p>').replace(one_line, '<br>');
    }

    function capitalize(s) {
        var first_char = /\S/;
        return s.replace(first_char, function(m) { return m.toUpperCase(); });
    }

    function lowerCase(s) {
        return s.toLowerCase();
    }

    return {
        'start': start,
        'stop': stop,
        'addEventListener': function(type, eventHandler) {
            var listener = {};
            listener.type = type;
            listener.eventHandler = eventHandler;
            eventListeners.push(listener);
        }
    }
}

var TestSpeech = function(options) {

    var defaultOptions = {
        pitch: 1,
        rate: 1
    };
    options = options || {};
    options = Object.assign(defaultOptions, options);

    var synthesis = window.speechSynthesis;

    function setSpeech() {
        return new Promise(function(resolve, reject) {
            var handle;
            handle = setInterval(function() {
                if (synthesis.getVoices().length > 0) {
                    resolve(synthesis.getVoices());
                    clearInterval(handle);
                }
            }, 50);
        });
    }

    var voices = [];
    setSpeech().then(function(speech) {
        voices = speech;
    });

    return {
        'readText': function(text, voice, onEnd) {

            var utterance = new SpeechSynthesisUtterance(text);

            voice = voice || 'Google русский';
            for (var i = 0; i < voices.length; i++) {
                if (voices[i].name === voice) {
                    utterance.voice = voices[i];
                    break;
                }
            }

            for (var [key, value] of Object.entries(options)) {
                utterance[key] = value;
            }

            if (typeof onEnd === 'function') {
                utterance.onend = onEnd;
            }

            synthesis.speak(utterance);
        }
    }
}