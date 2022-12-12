import '../css/wikids-reveal.css';
import {
  extend,
  shuffle,
  combineArraysRecursively,
  textDiff,
  _extends
} from './common';
import QuestionSuccess from './components/QuestionSuccess';
import AnswerTypeNumPad from "./answers/AnswerTypeNumPad";
import answerTypeInput from "./answers/AnswerTypeInput";
import TestLinked from "./components/TestLinked";
import RegionQuestion from "./questions/RegionQuestion";
import SequenceQuestion from "./questions/SequenceQuestion";
import TestConfig from "./components/TestConfig";
import MissingWords from "./questions/MissingWords";
import RecordingAnswer from "./questions/RecordingAnswer";
import MissingWordsRecognition from "./components/MissingWordsRecognition";
import VoiceResponse from "./questions/VoiceResponse";
import VoiceResponseInfo from "./components/VoiceResponseInfo";
import TestSpeech from "./components/TestSpeech";
import createSettings from "./components/Settings";
import {createStar, createStarFill} from "./components/stars";
import createDescription from "./components/description";
import PassTest from "./questions/PassTest";
import DragWords from "./questions/DragWords";
import {createBeginPage} from "./components/BeginPage";
import createHome from "./components/header/Home";
import createPlayBackdrop from "./components/questionAudio";
import Poetry from "./questions/Poetry";


var plugins = [];
var defaults = {
  initializeByDefault: true
};
var PluginManager = {
  mount: function mount(plugin) {
    // Set default static properties
    for (var option in defaults) {
      if (defaults.hasOwnProperty(option) && !(option in plugin)) {
        plugin[option] = defaults[option];
      }
    }
    plugins.forEach(function (p) {
      if (p.pluginName === plugin.pluginName) {
        throw "WikidsStoryTest: Cannot mount plugin ".concat(plugin.pluginName, " more than once");
      }
    });
    plugins.push(plugin);
  },
  initializePlugins: function initializePlugins(test, el, defaults, options) {
    plugins.forEach(function (plugin) {
      var pluginName = plugin.pluginName;
      //if (!test.options[pluginName] && !plugin.initializeByDefault) return;
      if (!plugin.initializeByDefault) return;
      var initialized = new plugin(test, el, defaults);
      initialized.test = test;
      //initialized.options = defaults;
      test[pluginName] = initialized; // Add default options from plugin
      _extends(defaults, initialized.defaults);
    });
  }
};

var tests = [];

function WikidsStoryTest(el, options) {

  if (!(el && el.nodeType && el.nodeType === 1)) {
    throw "Element must be an HTMLElement, not ".concat({}.toString.call(el));
  }

  options = options || {};
  this.options = Object.assign({}, options);

  if (!options['init'] || typeof options['init'] !== 'function') {
    throw 'Необходимо определить свойство init';
  }

  const testingId = options.dataParams.testId;

  var that = this;

  el['_wikids_test'] = this;

  this.recordingAnswer = null;
  this.recognition = null;
  this.sequenceQuestion = null;
  this.missingWords = null;
  this.regionQuestion = null;
  this.voiceResponse = null;
  this.passTestQuestion = null;
  this.dragWordsQuestion = null;
  this.poetryQuestion = null;

  setElementHtml(createLoader('Инициализация'));

  var numQuestions,
    currentQuestionIndex = 0,
    correctAnswersNumber = 0,
    testData = {},
    dom = {};

  var questionHistory = [],
    skipQuestion = [],
    questions = [],
    questionsRepeat = [],
    testParams = {};

  var questionAnswers = {};

  let questionList = [];

  function reset() {
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
    testQuestions = [];
    currentQuestion = null;
  }

  var questionSuccess;

  function getTestData() {
    return testData;
  }

  function getQuestionsData() {
    return testData.storyTestQuestions.filter(function (el) {
      return (skipQuestion.indexOf(el.id) === -1);
    });
  }

  function getAnswersData(question) {
    return question.storyTestAnswers.filter(function (answer) {
      return !answer['hidden'];
    });
  }

  function getHiddenAnswersData(question) {
    return question.storyTestAnswers.filter(function (answer) {
      return answer['hidden'];
    });
  }

  function addQuestionAnswer(question, answer) {
    question.storyTestAnswers.push(answer);
  }

  function getProgressData() {
    var progress = testData['test'] || {};
    progress = progress['progress'] || {};
    return progress;
  }

  function getStudentsData() {
    return testData['students'] || [];
  }

  var QuestionsRepeat = function (questions, starsTotal) {
    this.starsTotal = starsTotal;
    questions.map(function (question) {
      if (question['stars']) {
        question.stars.number = parseInt(question.stars.total) - parseInt(question.stars.current);
      }
    });
  };

  QuestionsRepeat.prototype.inc = function (question) {
    question.stars.number++;
    var increased = true;
    if (question.stars.number > this.starsTotal) {
      question.stars.number = this.starsTotal;
      increased = false;
    }
    return increased;
  };

  QuestionsRepeat.prototype.dec = function (question) {
    question.stars.number--;
  };

  QuestionsRepeat.prototype.done = function (question) {
    return parseInt(question.stars.number) <= 0;
  };

  QuestionsRepeat.prototype.number = function (question) {
    var number = this.starsTotal - question.stars.number;
    return number < 0 ? 0 : number;
  };

  QuestionsRepeat.prototype.stars = function (question) {
    return question.stars.number;
  };

  var testProgress;
  var TestProgress = function (progress) {
    this.progress = progress;
  };

  TestProgress.prototype.getProgress = function () {
    return this.progress;
  };

  TestProgress.prototype.getCurrent = function () {
    return this.progress.current;
  };

  TestProgress.prototype.getTotal = function () {
    return this.progress.total;
  };

  TestProgress.prototype.calcPercent = function () {
    return Math.round(this.getCurrent() * 100 / this.getTotal());
  }

  TestProgress.prototype.inc = function () {
    this.progress.current++;
  }

  TestProgress.prototype.dec = function () {
    this.progress.current--;
    if (this.progress.current < 0) {
      this.progress.current = 0;
    }
  }

  var testConfig;

  function setElementHtml(html) {
    $(el).html(html);
  }

  function testIsRequired() {
    return parseInt(that.options.required) === 1;
  }

  function createContainer(content) {
    if (that.options.forSlide) {
      setElementHtml($("<section/>")
        .attr("data-background-color", "#ffffff")
        .append(content));
    } else {
      setElementHtml(content);
    }
  }

  function run() {
    options.init()
      .done((response) => {
        init(response);
        if (typeof options['onInitialized'] === 'function') {
          options.onInitialized();
        }
      });
  }

  function init(testResponse) {
    console.debug('WikidsStoryTest.init');

    reset();

    dom.wrapper = $("<div/>").addClass("wikids-test");

    if (App.userIsGuest()) {
      dom.beginPage = createGuestBeginPage(testResponse);
    } else {
      dom.beginPage = createBeginPage(testResponse, {
        canModerate: App.userIsModerator(),
        onActive: (student) => {
          currentStudent = student;
          activeStudent.set(student);
          $('.wikids-test-student-info', dom.header).text(currentStudent.name);
        },
        onStart: (fastMode) => {
          loadData({fastMode});
        },
        onRestart: (studentId) => {
          return $.getJSON('/question/quiz-restart', {quiz_id: testResponse.test.id, student_id: studentId})
            .fail(function(response) {
              toastr.error((response['responseJSON'] && response.responseJSON.message) || 'Произошла ошибка');
            });
        }
      });
    }

    dom.wrapper.append(dom.beginPage);
    createContainer(dom.wrapper);
  }

  function incorrectAnswerAction() {
    return testData['incorrectAnswerAction'] || '';
  }

  function incorrectAnswerActionRelated() {
    return incorrectAnswerAction() === 'related';
  }

  var testQuestions = [];

  function makeTestQuestions() {
    console.log('makeTestQuestions');
    var end = false;
    var max = getQuestionRepeat();
    while (!end && testQuestions.length < max) {
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
  var speech;

  var repeatQuestions = 5;

  function getQuestionRepeat() {
    return that.options.fastMode ? 1 : repeatQuestions;
  }

  var questionCode;

  function neoQuestionWithAnimalSigns() {
    return questionCode === 'select_signs' || questionCode === 'common_signs';
  }

  function load(data) {
    console.debug('WikidsStoryTest.load');

    testData = data[0];
    questions = getQuestionsData();
    numQuestions = questions.length;

    testParams = testData['params'] || {};
    questionCode = testData['code'];

    if (testData['test']) {
      incorrectAnswerText = testData['test']['incorrectAnswerText'] || '';
    }

    if (testData['test']) {
      showAnswerImage = testData['test']['showAnswerImage'];
      showAnswerText = testData['test']['showAnswerText'];
      showQuestionImage = testData['test']['showQuestionImage'];

      if (testData['test']['repeatQuestions']) {
        repeatQuestions = testData['test']['repeatQuestions'];
      }
    }

    questionSuccess = new QuestionSuccess(getQuestionRepeat());

    testConfig = new TestConfig(testData['test']);
    linked = new TestLinked(testData['stories']);
    questionsRepeat = new QuestionsRepeat(questions, getQuestionRepeat());
    testProgress = new TestProgress(getProgressData());
    numPad = new AnswerTypeNumPad();
    speech = new TestSpeech();

    if (testConfig.answerTypeIsMissingWords(questions[0])) {
      that.missingWords.setRecognition(new MissingWordsRecognition(testConfig));
    }

    if (testConfig.answerTypeIsRecording(questions[0])) {
      that.recordingAnswer.setRecognition(new MissingWordsRecognition(testConfig));
    }

    if (testConfig.isVoiceResponse()) {
      that.voiceResponse.setRecognition(new MissingWordsRecognition(testConfig));
    }

    makeTestQuestions();
    setupDOM();
    addEventListeners();

    start();
    createContainer(dom.wrapper);

    if (currentQuestion && questionViewPoetry(currentQuestion)) {
      that.poetryQuestion.scroll($(currentQuestionElement).find('.drag-words-question'));
    }
  }

  function createLoader(text) {
    text = text || 'Загрузка вопросов';
    return $('<div/>')
      .addClass('wikids-test-loader')
      .append($('<p/>').text(text))
      .append($('<img/>').attr('src', '/img/loading.gif'));
  }

  function loadData(options) {
    console.debug('WikidsStoryTest.loadData');

    setElementHtml(createLoader());

    that.options.fastMode = options.fastMode;

    var dataParams = Object.assign(that.options.dataParams || {}, {
      studentId: activeStudent.getID(),
      fastMode: options.fastMode
    });
    $.getJSON(that.options.dataUrl, dataParams)
      .done(function (response) {

        PluginManager.initializePlugins(that, el, {
          'historyValues': response[0].historyValues
        });

        load(response);
        if (that.options.forSlide) {
          that.options.deck.sync();
          that.options.deck.slide(0);
        }
      })
      .fail(function (response) {
        setElementHtml(createErrorPage());
      });
  }

  var currentStudent;
  var activeStudent = (function () {
    var stud = {};
    return {
      'set': function (student) {
        stud = student;
      },
      'getID': function () {
        return stud['id'] || null;
      },
      'getName': function () {
        return stud['name'] || '';
      },
      'getProgress': function () {
        return stud['progress'] || 0;
      },
      'setFinish': function (finish) {
        stud['finish'] = finish;
      },
      'getFinish': function () {
        return stud['finish'] || false;
      }
    }
  })();

  function createGuestBeginPage(testResponse) {

    var $beginButton = $('<button/>')
      .addClass('btn wikids-test-begin')
      .text('Начать тест')
      .on('click', function () {
        loadData({fastMode: true});
      });

    var $rowWrapper = $('<div/>', {'class': 'row-wrapper'});

    if (testResponse.test.description.length) {
      const description = $('<div/>', {class: 'question-description__wrap'}).append(
        createDescription(testResponse.test.description)
      );
      $rowWrapper.append(description);
    }

    return $('<div/>')
      .addClass('wikids-test-begin-page')
      .append($('<h3/>').text(testResponse.test.header))
      .append($rowWrapper)
      .append($beginButton);
  }

  function createErrorPage() {
    return $('<div/>')
      .addClass('wikids-test-error-page')
      .append($('<h3/>').text('При загрузке теста произошла ошибка'));
  }

  function correctAnswerPageNext() {
    dom.correctAnswerPage.hide();
    dom.results.hide();
    showNextQuestion();
    showNextButton();
  }

  function createCorrectAnswerPage() {
    var $action = $('<button/>')
      .addClass('btn correct-answer-page-next')
      .text('Продолжить')
      .on('click', function () {
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
      .hide()
      .text('Следующий вопрос')
      .appendTo($(".wikids-test-buttons", dom.controls));
    dom.finishButton = $("<button/>")
      .addClass("wikids-test-finish")
      .hide()
      .text('Закончить тест')
      .appendTo($(".wikids-test-buttons", dom.controls));
    dom.restartButton = $("<button/>")
      .addClass("btn wikids-test-reset")
      .hide()
      .text('Пройти еще раз')
      .appendTo($(".wikids-test-buttons", dom.controls));
    dom.backToStoryButton = $("<button/>")
      .addClass("btn wikids-test-back")
      .hide()
      .text('Вернуться к истории')
      .appendTo($(".wikids-test-buttons", dom.controls));
    dom.continueButton = $("<button/>")
      .addClass("wikids-test-continue")
      .hide()
      .text('Продолжить')
      .appendTo($(".wikids-test-buttons", dom.controls));
    dom.nextSlideButton = $("<button/>")
      .addClass("btn wikids-test-next-slide")
      .hide()
      .html('Продолжить <i class="icomoon-chevron-right"></i>')
      .appendTo($(".wikids-test-buttons", dom.controls));
    dom.results = createResults();
    dom.correctAnswerPage = createCorrectAnswerPage();
    dom.wrapper
      .empty()
      .append(dom.header)
      .append(dom.questions)
      .append(dom.results)
      .append(dom.controls)
      .append(dom.correctAnswerPage);

    $('[data-toggle="tooltip"]', dom.wrapper).tooltip();
  }

  function createStudentInfo() {
    return $('<div/>')
      .addClass('wikids-test-student-info')
      .text(currentStudent.name);
  }

  function addEventListeners() {
    //dom.nextButton.off("click").on("click", nextQuestion);
    dom.finishButton.off("click").on("click", finish);
    dom.restartButton.off("click").on("click", restart);
    dom.backToStoryButton.off("click").on("click", backToStory);
    dom.continueButton.off("click").on("click", continueTestAction);
    dom.nextSlideButton.off("click").on("click", nextSlideAction);
  }

  function showOriginalImage(url, elem) {
    url = url.indexOf('neo.wikids.ru') === -1 ? url : url + '/original';
    $('<div/>')
      .addClass('wikids-test-image-original')
      .append(
        $('<div/>')
          .addClass('wikids-test-image-original-inner image-loader')
          .on('click', function () {
            $(this).parent().remove();
            //if (elem) {
            //    $(elem).parent()[0].click();
            //}
          })
          .append(
            $('<img/>')
              .attr('src', url)
              .on('load', function () {
                $(this).parent().removeClass('image-loader');
                $(this).show();
              })
          )
      )
      .appendTo(dom.wrapper);
  }

  function findAnimalSignItem(id) {
    var signs = testParams['signs'] || [];
    return signs.filter(function (sign) {
      return parseInt(sign.id) === parseInt(id);
    })[0];
  }

  function getBoundSlideId(question, id) {
    var sign = findAnimalSignItem(id) || [];
    return sign['slide_id'] || null;
  }

  function getAnimalSignTitle(question) {
    var questionNeoParams = question['params'] || [];
    if (questionNeoParams.length === 0) {
      return '';
    }
    return questionNeoParams[0]['animal'] || '';
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
      .on("click", function (e) {
        var tagName = e.target.tagName;
        var tags = ['INPUT', 'I'];
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

    if (answer.description) {
      $answer
        .attr('title', answer.description)
        .attr('data-toggle', 'tooltip')
        .attr('data-placement', 'auto');
    }

    if (showAnswerImage && answer.image) {
      var $image = $("<img/>")
        .attr("src", answer.image)
        .attr('height', 100);
      if (originalImageExists || answer['orig_image']) {
        $image
          .css('cursor', 'zoom-in')
          .on('click', function () {
            showOriginalImage(answer['orig_image'] || $(this).attr('src'), this);
          });
      }
      $answer.append($image);
    }

    if (showAnswerText) {
      var answerName = answer.name;
      var slideId = getBoundSlideId(question, answer.id);
      if (slideId) {
        answerName = '<i data-bound-slide-id="' + slideId + '" class="glyphicon glyphicon-question-sign"></i> ' + answerName;
      }
      var $label = $("<label/>")
        .attr("for", "answer" + answer.id)
        .html(answerName);
      $label.find('[data-bound-slide-id]').on('click', function (e) {
        e.preventDefault();
        var id = $(this).data('boundSlideId');
        createSlideInnerDialog(id);
      });
      $answer.append($label);
    }

    return $answer;
  }

  function generateAnswerList(answers, num) {

    if (answers.length <= num) {
      return shuffle(answers);
    }

    var list = answers.filter(function (answer) {
      return answer.is_correct === 1;
    });

    function sample(population, k) {
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
    sample(answers.filter(function (answer) {
      return answer.is_correct !== 1;
    }), max).map(function (elem) {
      list.push(elem);
    });

    return shuffle(list);
  }

  function getQuestionAnswerNumber(question) {
    return parseInt(question.answer_number);
  }

  function createAnswers(answers, question) {

    var num = getQuestionAnswerNumber(question);
    if (testConfig.sourceIsNeo() && num > 0) {
      answers = generateAnswerList(answers, num);
    } else {
      var mixAnswers = question.mix_answers || 0;
      if (parseInt(mixAnswers) === 1 || testConfig.sourceIsNeo() || testConfig.sourceIsLocal()) {
        answers = shuffle(answers);
      }
    }

    var $answers = $("<div/>").addClass("wikids-test-answers");
    answers.forEach(function (answer) {
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
    var getAnswersIDs = function (dom) {
      var answers = [];
      $('.continent.selected', dom).each(function () {
        var id = $(this).attr('id');
        answers.push(id);
      });
      return answers;
    }
    $object[0].addEventListener('load', function () {
      var svgDOM = $object[0].contentDocument;
      $('.continent', svgDOM).on('click', function () {
        if ($(this).hasClass('selected')) {
          $(this).removeClass('selected');
        } else {
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
    return numPad.create(function (text) {
      nextQuestion();
    });
  }

  function createNumPadAnswers(question, answers) {
    var $answers = $("<div/>").addClass("wikids-test-answers");
    answers.forEach(function (answer) {
      $answers.append(createNumPadAnswer(question, answer));
    });
    var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper"></div></div>');
    $wrapper.find(".question-wrapper").append($answers);
    return $wrapper;
  }

  function createInputAnswer(question, answer) {
    var $elem = $('<div/>');
    if (testConfig.isSayCorrectAnswer() || testConfig.isAskQuestion()) {
      var $repeat = '<a href="" title="Повторить слово" class="glyphicon glyphicon-repeat synthesis-question" style="top: 5px; right: 10px"><i></i></a>';
      $elem.append($repeat);
    }
    $elem.append(answerTypeInput.create(nextQuestion));
    return $elem;
  }

  function createInputAnswers(question, answers) {
    var $answers = $("<div/>").addClass("wikids-test-answers");
    answers.forEach(function (answer) {
      $answers.append(createInputAnswer(question, answer));
    });
    var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper text-center"></div></div>');
    $wrapper.find(".question-wrapper").append($answers);
    return $wrapper;
  }

  function createRecordingAnswer(question, answer) {
    return that.recordingAnswer.create(question, answer);
  }

  function createRegionAnswer(question, answers) {
    return that.regionQuestion.create(question, answers, {scale: (that.options.deck && that.options.deck.getScale()) || null});
  }

  function createRecordingAnswers(question, answers) {
    var $answers = $("<div/>").addClass("wikids-test-answers");
    getCorrectAnswers(question).forEach(function (answer) {
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
    return that.missingWords.init(question, answer);
  }

  function createMissingWordsAnswers(question, answers) {
    var $answers = $("<div/>").addClass("wikids-test-answers");
    answers.forEach(function (answer) {
      $answers.append(createMissingWordsAnswer(question, answer));
    });
    var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper"></div></div>');
    $wrapper.find(".question-wrapper").append($answers);
    return $wrapper;
  }

  function appendStars($elem, total, current) {
    $elem.empty();
    for (let i = 0, star; i < total; i++) {
      if (i + 1 <= current) {
        star = createStarFill();
      } else {
        star = createStar();
      }
      $elem.append(star);
    }
  }

  function appendHints($elem, questionID) {
    $elem.empty();
    $('<a/>', {
      'href': '',
      'text': 'Подсказка'
    })
      .on('click', function (e) {
        e.preventDefault();

        var $hintWrapper = $('<div/>', {'class': 'slide-hints-wrapper'});
        var $hintBackground = $('<div/>', {'class': 'slide-hints-background'});
        var $hintInner = $('<div/>', {'class': 'slide-hints-inner'});
        var $hint = $('<div/>', {'class': 'slide-hints'});

        $hintBackground.appendTo($hintWrapper);
        $hintInner.appendTo($hintWrapper);

        $('<header/>', {'class': 'slide-hints-header'})
          .append(
            $('<div/>', {'class': 'header-actions'})
              .append(
                $('<button/>', {
                  'class': 'hints-close',
                  'html': '&times;'
                })
                  .on('click', function () {
                    $hintWrapper.hide();
                    $(this).remove();
                    $('.reveal .story-controls').show();
                  })
              )
          )
          .appendTo($hintInner);

        $('<iframe>', {
          'src': '/question-hints/view?question_id=' + questionID,
          'frameborder': 0,
          'scrolling': 'no',
          'width': '100%',
          'height': '100%'
        }).appendTo($hint);

        $hint.appendTo($hintInner);

        $('.reveal .story-controls').hide();
        $('.reveal .slides section.present').append($hintWrapper);
      })
      .appendTo($elem);
  }

  function createStars(questionID, stars, haveHints) {
    var $elem = $('<div/>', {
      'class': 'row row-no-gutters question-stars'
    })
      .append(
        $('<div/>', {'class': 'col-md-6 hints'})
      )
      .append(
        $('<div/>', {'class': 'col-md-6 stars'})
      );
    appendStars($elem.find('.stars'), getQuestionRepeat(), stars.current);
    haveHints = haveHints || false;
    if (haveHints) {
      appendHints($elem.find('.hints'), questionID);
    }
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
      .attr('data-placement', 'bottom')
      .append(
        $('<div/>')
          .addClass('progress-bar progress-bar-info')
          .css('width', progress + '%')
        //.css('minWidth', '2em')
        //.text(progressValue(progress))
      )[0].outerHTML;
  }

  function updateProgress() {
    var progress = testProgress.calcPercent();
    $('.wikids-progress', dom.header).attr('title', getCurrentProgressStateText()).tooltip('fixTitle');
    $('.wikids-progress .progress-bar', dom.header)
      .css('width', progress + '%');
    //.text(progressValue(progress));
  }

  function createQuestionName(question) {

    var questionName = question.name;

    var createQuestionHint = function (name, hint) {
      hint = hint || '';
      if (!hint.length) {
        return name;
      }
      if (name.length) {
        return name + ' (подсказка: ' + hint + ')';
      }
      return 'Подсказка: ' + hint;
    };
    questionName = createQuestionHint(questionName, question['hint']);

    var correctNum = parseInt(question.correct_number);
    if (question['correct_number'] && correctNum > 1) {
      questionName += ' (верных: ' + correctNum + ')';
    }

    if (testConfig.answerTypeIsMissingWords(question)) {
      questionName = 'Заполните пропущенные части';
    }

    if (questionViewSequence(question)) {
      questionName = question.name;
    }

    if (testConfig.hideQuestionName()) {
      questionName = '';
      return createQuestionHint(questionName, question['hint']);
    }

    return questionName;
  }

  function createQuestion(question) {

    const titleElement = $('<p/>', {class: 'question-title'});

    if (testConfig.answerTypeIsDefault() && testConfig.isAskQuestion()) {

      let questionName = createQuestionName(question);
      if (questionName.length) {
        titleElement.append($('<div/>', {text: questionName}));
      }

      const playContent = $(createPlayBackdrop());
      $('<span/>', {
        'css': {'line-height': '3.5rem', 'margin-left': '10px', 'color': '#000', 'cursor': 'pointer'},
        'title': 'Прослушать'
      })
        .on('click', function() {
          var $this = $(this);
          if ($this.data('process')) {
            return false;
          }
          $this.data('process', true);
          var text = question.name;
          var i = $(this).find('i');
          i.removeClass('glyphicon-volume-up').addClass('glyphicon-option-horizontal');

          questionWrap.append(playContent);
          sayQuestionName(question, () => {
            i.removeClass('glyphicon-option-horizontal').addClass('glyphicon-volume-up');
            $this.data('process', false);
            playContent.remove();
          });
        })
        .append($('<i/>', {'class': 'glyphicon glyphicon-volume-up'}))
        .appendTo(titleElement);
    } else {
      let questionName = createQuestionName(question);
      if (testConfig.answerTypeIsInput(question) && questionName === '') {
        questionName = 'Введите текст';
      }
      if (questionName.length === 0) {
        titleElement.hide();
      }
      titleElement.append(questionName);
    }

    var stars = '';
    if (question['stars']) {
      stars = createStars(question.id, question.stars, question['haveSlides']);
    }

    const questionWrap = $('<div/>')
      .addClass("wikids-test-question")
      .hide()
      .attr("data-question-id", question.id)
      .data("question", question);

    questionWrap.append(stars);

    if (!questionViewPoetry(question)) {
      questionWrap.append(titleElement);
    }

    if (testConfig.showDescriptionInQuestions() && testConfig.getDescription().length) {
      questionWrap.append(
        $('<div/>', {class: 'question-description__wrap'}).append(
          createDescription(testConfig.getDescription())
        )
      );
    }

    return questionWrap;
  }

  function createQuestions(questions) {
    const $questions = $("<div/>").addClass("wikids-test-questions");
    questions.forEach(function (question) {

      const $question = createQuestion(question);

      var view = question['view'] ? question.view : '';
      if (testConfig.answerTypeIsNumPad(question)) {
        view = 'numpad';
      }
      if (testConfig.answerTypeIsInput(question)) {
        view = 'input';
      }
      if (testConfig.answerTypeIsRecording(question)) {
        view = 'recording';
      }
      if (testConfig.answerTypeIsMissingWords(question)) {
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
          $answers = createInputAnswers(question, getCorrectAnswers(question));
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
        case 'sequence':
          $answers = that.sequenceQuestion.createWrapper();
          break;
        case 'pass-test':
          $answers = that.passTestQuestion.createWrapper();
          break;
        case 'drag-words':
          $answers = that.dragWordsQuestion.createWrapper();
          break;
        case 'poetry':
          $answers = that.poetryQuestion.createWrapper();
          break;
        default:
          $answers = createAnswers(getAnswersData(question), question);
      }
      $answers.appendTo($question);

      var multipleImages = question['images'] && question.images.length > 0;
      if (multipleImages) {
        var $imageWrapper = $('<div/>', {'class': 'row'});
        var $image;
        question.images.forEach(function (imageItem) {
          $image = $('<img/>')
            .attr("src", imageItem.url)
            .attr('title', imageItem.title)
            .attr('data-toggle', 'tooltip')
            .css('width', '100%')
            .css('padding', '10px')
            .css('cursor', 'zoom-in')
            .on('click', function () {
              showOriginalImage($(this).attr('src'));
            });
          $image.wrap('<div class="col-md-6"></div>').parent().appendTo($imageWrapper);
        });
        $imageWrapper.appendTo($(".question-image", $question));
      } else {
        if (showQuestionImage && question.image) {
          var $image = $('<img/>')
            .attr("src", question.image)
            .css('max-width', '330px');

          var title = getAnimalSignTitle(question);
          if (title) {
            $image.attr({
              'title': title,
              'data-toggle': 'tooltip'
            });
          }

          var originalImageExists = question['original_image'] === undefined ? true : question['original_image'];
          if (originalImageExists || question['orig_image']) {
            $image
              .css('cursor', 'zoom-in')
              .on('click', function () {
                showOriginalImage(question['orig_image'] || $(this).attr('src'));
              });
          }
          $image.appendTo($(".question-image", $question));
        }
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

    const $row = $('<div/>', {class: 'quiz-header-row'});

    if (!App.userIsGuest()) {
      $('<div/>', {class: 'quiz-header-col student-col'})
        .append(createHome(() => {
          run();
        }))
        .append(createStudentInfo())
        .appendTo($row);
    }

    if (App.userIsModerator()) {

      const items = [];

      if (testConfig.sourceIsLocal() || testConfig.sourceIsTests()) {
        const item = {
          title: 'Перейти к вопросу',
          callback: () => {
            window.open(`/admin/index.php?r=test/update-question&question_id=${currentQuestion.id}`, '_blank');
          }
        };
        items.push(item);
      }

      items.push({
        title: 'Перейти к тесту',
        callback: () => {
          window.open(`/admin/index.php?r=test/update&id=${testConfig.getTestID()}`, '_blank');
        }
      });

      $('<div/>', {class: 'quiz-header-col'})
        .append(createSettings(items))
        .appendTo($row);
    }

    $row.appendTo($header);

    $header.append(createProgress());
    return $header;
  }

  function questionIsVisible(questionElement) {
    var object = $('object', questionElement);
    if (object.length) {
      var domSVG = object.contents();
      $('.continent', domSVG).removeClass('selected');
    }
  }

  function setTestResults(title) {
    title = title || 'Тест пройден';
    dom.results
      .empty()
      .append("<h2>" + title + "</h2>");
    var linkedStories = linked.getHtml();
    if (linkedStories.length) {
      dom.results.append(linkedStories);
    }
    return dom.results.show();
  }

  function nextSlideAction() {
    dispatchEvent("nextSlide", {
      "testID": getTestData().id
    });
  }

  function start() {
    console.debug('WikidsStoryTest.start');

    correctAnswersNumber = 0;
    currentQuestionIndex = 0;

    if (numQuestions === 0) {
      if (currentStudent.progress === 100) {
        setTestResults();
        if (that.options.forSlide) {
          dom.backToStoryButton.show();
        }
        dom.nextSlideButton.show();
      } else {
        setTestResults('В тесте нет вопросов');
      }
      return;
    }

    showNextQuestion();
    dom.beginPage.hide();
    dom.header.show();
    showNextButton();
  }

  function finish() {
    $('.wikids-test-active-question', el).hide().removeClass('wikids-test-active-question');
    dom.finishButton.hide();
    setTestResults();
    if (currentStudent) {
      currentStudent['finish'] = true;
    }
    activeStudent.setFinish(true);
    dispatchEvent("finish", {
      "testID": getTestData().id,
      "correctAnswers": correctAnswersNumber
    });
  }

  function restart() {
    /*            var nextQuestion = testQuestions.shift();
            $('.wikids-test-question[data-question-id=' + nextQuestion.id + ']', dom.questions)
                .find('input[type=checkbox],input[type=radio]').prop('checked', false).end()
                .addClass('wikids-test-active-question');

            dom.results.hide();
            correctAnswersNumber = 0;
            currentQuestionIndex = 0;
            dom.nextButton.show();
            dom.restartButton.hide();
            dom.backToStoryButton.hide();
            dom.continueButton.hide();*/
  }

  function backToStory() {
    dispatchEvent("backToStory", {});
  }

  function getCorrectAnswers(question) {
    return getAnswersData(question).filter(function (elem) {
      return parseInt(elem.is_correct) === 1;
    });
  }

  function createAnswerSteps(answers) {
    var steps = [];
    answers.map(function (item) {
      if (/(\d+)#([\wа-яА-ЯёЁ]+)/ui.test(item.name)) {
        var match;
        var re = /(\d+)#([\wа-яА-ЯёЁ]+)/uig;
        var parts = [];
        var key = 0;
        var line = item.name;
        while ((match = re.exec(item.name)) !== null) {
          parts.push({match, values: [match[1], match[2]], key: '{' + key + '}'});
          line = line.replace(match[0], '{' + key + '}');
          key++;
        }

        var variants = [];
        if (parts.length === 1) {
          variants.push([parts[0].values[0]]);
          variants.push([parts[0].values[1]]);
        } else {
          variants = combineArraysRecursively(parts.map(function (item) {
            return item.values;
          }));
        }

        var str = '';
        var j = 0;
        variants.forEach(function (value) {
          str = line;
          j = 0
          parts.forEach(function (partValue) {
            str = str.replace(partValue.key, value[j]);
            j++;
          });
          steps.push(str);
        });
      }
    });
    return steps;
  }

  function correctAnswerSteps(steps, userAnswers) {
    return userAnswers.every(function (userAnswer) {
      return steps.some(function (stepAnswer) {
        return userAnswer === stepAnswer;
      });
    });
  }

  function checkAnswerCorrect(question, answer, correctAnswersCallback, convertAnswerToInt) {
    console.debug('WikidsStoryTest.checkAnswerCorrect');

    var correctAnswers = getAnswersData(question).filter(function (elem) {
      return parseInt(elem.is_correct) === 1;
    });

    var correctHiddenAnswers = getHiddenAnswersData(question).filter(function (elem) {
      return parseInt(elem.is_correct) === 1;
    });

    var steps = createAnswerSteps(correctAnswers);
    var correct = false;
    if (steps.length > 0) {
      correct = correctAnswerSteps(steps, answer);
    } else {
      if (questionViewSequence(question)) {
        correct = checkAnswersCorrect(question, answer);
      } else {

        correctAnswers = correctAnswers.map(correctAnswersCallback);

        var checker = function (values) {
          return function (value, index) {
            if (convertAnswerToInt) {
              value = parseInt(value)
            }
            return value === values.sort()[index];
          }
        }

        if (answer.length === correctAnswers.length && answer.sort().every(checker(correctAnswers))) {
          correctAnswersNumber++;
          correct = true;
        }

        if (!correct && correctHiddenAnswers.length > 0) {
          correctHiddenAnswers = correctHiddenAnswers.map(correctAnswersCallback);
          if ($.inArray(answer[0], correctHiddenAnswers) > -1) {
            correctAnswersNumber++;
            correct = true;
          }
        }
      }
    }
    return correct;
  }

  function getQuestionView(question) {
    return question['view'] ? question.view : '';
  }

  function questionViewSequence(question) {
    return getQuestionView(question) === 'sequence';
  }

  function questionViewPassTest(question) {
    return getQuestionView(question) === 'pass-test';
  }

  function questionViewDragWords(question) {
    return getQuestionView(question) === 'drag-words';
  }

  function questionViewPoetry(question) {
    return getQuestionView(question) === 'poetry';
  }

  function questionViewDefault(question) {
    return getQuestionView(question) === 'default';
  }

  function questionViewSvg(question) {
    return getQuestionView(question) === 'svg';
  }

  function questionViewRegion(question) {
    return getQuestionView(question) === 'region';
  }

  function checkAnswersCorrect(question, userAnswers) {
    var correctAnswers = getCorrectAnswers(question);
    correctAnswers.sort(function (a, b) {
      return a.order - b.order;
    });
    correctAnswers = correctAnswers.map(function (answer) {
      return parseInt(answer.id);
    });
    return (JSON.stringify(correctAnswers) === JSON.stringify(userAnswers));
  }

  function getQuestionAnswers(element) {
    var answer = [];
    element.find(".wikids-test-answer input:checked").each(function (i, elem) {
      answer.push($(elem).val());
    });
    return answer;
  }

  function getSvgQuestionAnswers(question) {
    if (Object.keys(questionAnswers).length === 0) {
      return [];
    }
    var questionAnswerNames = [];
    $.each(questionAnswers, function (key, value) {
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

  var answerIsCorrect,
    currentQuestion,
    currentQuestionElement;

  function getCurrentQuestion() {
    return currentQuestion;
  }

  function updateStars($question, current) {
    var $stars = $('.question-stars .stars', $question);
    appendStars($stars, getQuestionRepeat(), current);
  }

  function answerByID(question, id) {
    return getAnswersData(question).filter(function (answer) {
      return parseInt(answer.id) === parseInt(id);
    })[0];
  }

  function answerByName(question, name) {
    return $.merge(getAnswersData(question), getHiddenAnswersData(question))
      .filter(function (answer) {
        return answer.name.toLowerCase() === name.toLowerCase();
      })[0];
  }

  function showQuestionSuccessPage(answer) {

    dom.questions.hide();
    dom.controls.hide();
    if (!dom.wrapper.hasClass('wikids-test--no-controls')) {
      dom.wrapper.addClass('wikids-test--no-controls');
    }

    var text = currentQuestion.name;
    if (testConfig.answerTypeIsInput(currentQuestion)) {
      text = answer[0];
    }
    var image = currentQuestion.image;
    if (!image) {
      getCorrectAnswers(currentQuestion).forEach(function (answer) {
        if (!image && answer.image) {
          image = answer.image;
        }
      });
    }
    var $content = questionSuccess.create(text, image);
    dom.wrapper.append($content)
    $content.fadeIn();

    var action = function () {
      $content.remove();
      continueTestAction(answer);
    };
    setTimeout(action, 2000);
  }

  function getQuestionRememberAnswers(question) {
    return question['rememberAnswer'] || false;
  }

  function changeQuestionRememberAnswers(question, answer) {
    question.rememberAnswer = false;
    question.storyTestAnswers[0].name = answer[0];
  }

  // Ответ на вопрос
  function nextQuestion(preparedAnswers, checkAnswersCallback) {

    console.debug('WikidsStoryTest.nextQuestion');
    if (!Array.isArray(preparedAnswers)) {
      preparedAnswers = false;
    }
    preparedAnswers = preparedAnswers || false;

    var $activeQuestion = $('.wikids-test-active-question', el);
    currentQuestion = $activeQuestion.data('question');

    var view = currentQuestion['view'] ? currentQuestion.view : '';
    if (testConfig.answerTypeIsNumPad(currentQuestion)) {
      view = 'numpad';
    }
    if (testConfig.answerTypeIsInput(currentQuestion)) {
      view = 'input';
    }
    if (testConfig.answerTypeIsRecording(currentQuestion)) {
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
        default:
          answer = getQuestionAnswers($activeQuestion);
      }
    } else {
      answer = preparedAnswers;
    }

    if (answer.length === 0) {
      return;
    }

    if (typeof checkAnswersCallback === 'function') {
      answerIsCorrect = checkAnswersCallback(currentQuestion, answer);
    }
    else {

      var convertAnswerToInt = true;
      var correctAnswersCallback = function (elem) {
        return parseInt(elem.id);
      };
      if (view === 'numpad') {
        correctAnswersCallback = function (elem) {
          return parseInt(elem.name);
        };
      }
      if (view === 'input' || view === 'recognition' || questionViewPassTest(currentQuestion) || testConfig.answerTypeIsMissingWords(currentQuestion)) {
        correctAnswersCallback = function (elem) {
          if (testConfig.isStrictAnswer()) {
            return elem.name;
          } else {
            return elem.name.toLowerCase();
          }
        };
        convertAnswerToInt = false;
      }

      var rememberAnswer = getQuestionRememberAnswers(currentQuestion);
      if (!rememberAnswer) {
        answerIsCorrect = checkAnswerCorrect(currentQuestion, answer, correctAnswersCallback, convertAnswerToInt);
      } else {
        changeQuestionRememberAnswers(currentQuestion, answer);
        answerIsCorrect = true;
      }
    }

    cancelSpeech();

    if (testConfig.isVoiceResponse()) {
      VoiceResponse.remove(dom.wrapper);
    }

    if (answerIsCorrect) {
      if (currentQuestion['stars']) {
        if (currentQuestion.lastAnswerIsCorrect) {
          questionsRepeat.dec(currentQuestion);
          testProgress.inc();
        } else {
          currentQuestion.lastAnswerIsCorrect = true;
        }
      } else {
        skipQuestion.push(currentQuestion.id);
      }
    } else {
      currentQuestion.lastAnswerIsCorrect = questionViewPoetry(currentQuestion);
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

    var done = false;
    if (!answerIsCorrect) {

      testQuestions.unshift(currentQuestion);

      if (questionViewPoetry(currentQuestion)) {
        const max = 3;
        let i = 0;
        while (questionList.length && i < max) {

          let backQuestion = questionList.pop();
          if (questionsRepeat.inc(backQuestion)) {
            testProgress.dec();
          }
          testQuestions.unshift(backQuestion);
          updateStars(dom.questions.find(`[data-question-id=${backQuestion.id}]`), questionsRepeat.number(backQuestion));

          i++;
        }

        if (i > 0) {
          updateProgress();
        }
      }
    } else {
      done = questionsRepeat.done(currentQuestion);
      if (done) {
        makeTestQuestions();
      } else {
        testQuestions.push(currentQuestion);
      }
    }

    if (!App.userIsGuest() && !that.options.fastMode) {
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
      if (testConfig.sourceIsWord() && !testConfig.answerTypeIsInput(currentQuestion)) {
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
      if (testConfig.sourceIsWord() && testConfig.answerTypeIsInput(currentQuestion)) {
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
      if (testConfig.sourceIsLocal() || testConfig.sourceIsTests()) {

        if (testConfig.answerTypeIsInput(currentQuestion)) {
          answerList = answer.map(function (answerText) {
            return {
              'answer_entity_id': currentQuestion.id,
              'answer_entity_name': answerText
            };
          });
        }
        else {
          answerList = answer.map(function (entity_id) {
            var answer = answerByID(currentQuestion, entity_id);
            return {
              'answer_entity_id': entity_id,
              'answer_entity_name': answer ? answer.name : 'no correct'
            };
          });
        }

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

    if (!answerIsCorrect) {

      if (questionViewPoetry(currentQuestion)) {
        that.poetryQuestion.showCorrectOverlay(answer, getCorrectAnswers(currentQuestion));

        $activeQuestion.append(that.poetryQuestion.createOverlay(() => {

          $activeQuestion
            .hide()
            .removeClass('wikids-test-active-question');

          that.poetryQuestion.removeOverlay();
          showNextQuestion();
          dom.results.hide();
          showNextButton();
        }));

      } else {

        $activeQuestion
          .hide()
          .removeClass('wikids-test-active-question');

        dom.questions.hide();
        dom.controls.hide();
        dom.wrapper.addClass('wikids-test--no-controls');
        if (testConfig.sourceIsWord()
          && !testConfig.answerTypeIsNumPad(currentQuestion)
          && !testConfig.answerTypeIsInput(currentQuestion)
          && !testConfig.answerTypeIsMissingWords(currentQuestion)) {
          continueTestAction(answer);
        } else {
          dom.results
            .html("<p>Ответ неверный.</p>")
            .show()
            .delay(1000)
            .fadeOut('slow', function () {
              continueTestAction(answer);
            });
        }
      }
    } else {

      $activeQuestion
        .hide()
        .removeClass('wikids-test-active-question');

      if (done && !that.options.fastMode && getQuestionRepeat() > 1) {
        showQuestionSuccessPage(answer);
      } else {
        continueTestAction(answer);
      }
    }
  }

  function isShuffleAnswers(q) {
    return testConfig.sourceIsNeo() && getQuestionView(currentQuestion) !== 'svg' || (
      testConfig.sourceIsLocal()
      && parseInt(q.mix_answers) === 1
      && !testConfig.answerTypeIsNumPad(q)
      && !testConfig.answerTypeIsMissingWords(q)
      && !testConfig.answerTypeIsRecording(q)
      && !testConfig.answerTypeIsInput(q)
    );
  }

  function getAudioFile(question) {
    return question.audio_file;
  }

  function haveAudioFile(question) {
    return getAudioFile(question) !== null;
  }

  var audio;

  function playAudio(url, onEndCallback) {
    audio = new Audio(url);
    audio.play();
    if (onEndCallback) {
      audio.addEventListener('ended', onEndCallback);
    }
  }

  function showNextQuestion() {

    console.debug('WikidsStoryTest.showNextQuestion');

    var nextQuestionObj = testQuestions.shift();
    currentQuestion = nextQuestionObj;

    if (nextQuestionObj === undefined) {
      return;
    }

    cancelSpeech();

    const exists = questionList.find(q => parseInt(q.id) === parseInt(currentQuestion.id));
    if (exists) {
      questionList = questionList.filter(q => parseInt(q.id) !== parseInt(currentQuestion.id));
    }

    dom.nextButton.off("click").on("click", nextQuestion);

    currentQuestionElement = $('.wikids-test-question[data-question-id=' + nextQuestionObj.id + ']', dom.questions);

    currentQuestionElement
      .find('input[type=checkbox],input[type=radio]')
      .prop('checked', false);

    dom.questions.show();

    const playContent = $(createPlayBackdrop());

    currentQuestionElement
      .addClass('wikids-test-active-question')
      .show();

    if (
      isShuffleAnswers(currentQuestion)
      && !questionViewRegion(currentQuestion)
      && !questionViewSequence(currentQuestion)
      && !questionViewPassTest(currentQuestion)
      && !questionViewDragWords(currentQuestion)
      && !questionViewPoetry(currentQuestion)
    ) {
      $('.wikids-test-answers', currentQuestionElement)
        .empty()
        .append(createAnswers(getAnswersData(currentQuestion), currentQuestion)
          .find('.wikids-test-answers > div'));
    }

    if (questionViewSequence(currentQuestion)) {

      $('.seq-question', currentQuestionElement)
        .empty()
        .append(that.sequenceQuestion.create(currentQuestion, getAnswersData(currentQuestion)));

      dom.nextButton.off("click").on("click", function () {
        var result = that.sequenceQuestion.getAnswerIDs(currentQuestion.id);
        nextQuestion(result);
      });
    }

    if (questionViewPassTest(currentQuestion)) {

      $('.seq-question', currentQuestionElement)
        .html(that.passTestQuestion.create(currentQuestion, getAnswersData(currentQuestion)));

      dom.nextButton.off("click").on("click", function () {
        var answer = that.passTestQuestion.getUserAnswers();
        nextQuestion(answer);
      });
    }

    if (questionViewDragWords(currentQuestion)) {
      $('.drag-words-question', currentQuestionElement).html(that.dragWordsQuestion.create(currentQuestion));
      dom.nextButton.off("click").on("click", function () {
        const answer = that.dragWordsQuestion.getUserAnswers();
        nextQuestion(answer, function (question, userAnswers) {
          return that.dragWordsQuestion.checkAnswers(question, userAnswers);
        });
      });
    }

    if (questionViewPoetry(currentQuestion)) {
      $('.drag-words-question', currentQuestionElement)
        .html(that.poetryQuestion.create(currentQuestion));
      that.poetryQuestion.scroll($(currentQuestionElement).find('.drag-words-question'));
    }

    if (testConfig.answerTypeIsMissingWords(currentQuestion)) {
      dom.nextButton.off("click").on("click", function () {
        var result = that.missingWords.getResult();
        that.missingWords.resetMatchElements();
        nextQuestion([result]);
      });
    }

    if (testConfig.answerTypeIsRecording(currentQuestion)) {
      dom.nextButton.off("click").on("click", function () {
        var result = that.recordingAnswer.getResult();
        that.recordingAnswer.resetResult();
        nextQuestion([result]);
      });
    }

    if (testConfig.answerTypeIsInput(currentQuestion)) {

      const q = $('.wikids-test-active-question .answer-input', dom.questions);
      q.val('');
      q.trigger('focus');

      if (testConfig.isSayCorrectAnswer()) {

        //currentQuestionElement.append(playContent);

        const correctText = getCorrectAnswers(nextQuestionObj)[0].name;
        sayCorrectAnswerName(correctText, () => {
          //playContent.remove();
          q.trigger('focus');
        });

        $('.wikids-test-active-question .synthesis-question', dom.questions)
          .off('click')
          .on('click',(e) => {
            e.preventDefault();

            //currentQuestionElement.append(playContent)
            sayCorrectAnswerName(correctText, () => {
              //playContent.remove();
              q.trigger('focus');
            });
          });
      }
      else if (testConfig.isAskQuestion()) {

        //currentQuestionElement.append(playContent);
        sayQuestionName(currentQuestion, () => {
          //playContent.remove();
          q.trigger('focus');
        });

        $('.wikids-test-active-question .synthesis-question', dom.questions)
          .off('click')
          .on('click',(e) => {
            e.preventDefault();

            //currentQuestionElement.append(playContent)
            sayQuestionName(currentQuestion, () => {
              //playContent.remove();
              q.trigger('focus');
            });
          });
      }
    }

    if (testConfig.answerTypeIsRecording(currentQuestion)) {
      if (testConfig.isAskQuestion()) {
        var speechText = currentQuestion.name;
        setTimeout(function () {
          speech.readText(speechText, testConfig.getAskQuestionLang(), function () {
            that.recordingAnswer.autoStart(new Event('autoStart'), 500);
          });
        }, 500);
      } else {
        that.recordingAnswer.autoStart(new Event('autoStart'));
      }
    }

    if (testConfig.isVoiceResponse()) {
      VoiceResponse.remove(dom.wrapper);
    }

    if (testConfig.answerTypeIsDefault()) {
      if (testConfig.isAskQuestion()) {
        currentQuestionElement.append(playContent);
        sayQuestionName(currentQuestion, () => {
          playContent.remove();
        });
      }

      if (testConfig.isVoiceResponse()) {

        var elem = VoiceResponse.create(function () {

          var state = $(this).data('state');
          var $that = $(this);
          if (!state) {
            setTimeout(function () {
              that.voiceResponse.start(new Event('voiceResponseStart'), function () {
                $that.data('state', 'recording');
                $that.addClass('recording');
                $that.before('<div class="pulse-ring"></div>');
              });
            }, 500);
          } else {

            that.voiceResponse.stop(function (args) {

              $that.siblings('.pulse-ring').remove();
              $that.removeClass('recording');
              $that.removeData('state');

              var result = args.result;
              if (result.length > 0) {

                var correct = checkAnswerCorrect(currentQuestion, [result], function (elem) {
                  return elem.name;
                }, false);

                if (correct) {
                  var answerId = answerByName(currentQuestion, result).id;
                  nextQuestion([answerId]);
                } else {
                  $.post('/answer/check', {
                    question_id: currentQuestion.id,
                    answer: result
                  }).done(function (response) {
                    if (response && response.success) {

                      var output = response.output;

                      var correct = checkAnswerCorrect(currentQuestion, [output], function (elem) {
                        return elem.name;
                      }, false);

                      if (correct) {
                        var answer = answerByName(currentQuestion, output);
                        if (answer) {
                          nextQuestion([answer.id]);
                        }
                      } else {

                        var $content = $(`<div class="voice-content">
                                     <div>
                                       <div class="voice-content-row">
                                         <h4 class="voice-content-row__title">Input:</h4>
                                         <p class="voice-input">${response.input}</p>
                                       </div>
                                       <div class="voice-content-row">
                                         <h4 class="voice-content-row__title">Output:</h4>
                                         <p class="voice-output">${response.output || 'Пусто'}</p>
                                       </div>
                                       <div class="voice-content-row">
                                         <h4 class="voice-content-row__title">Расстояние:</h4>
                                         <p class="voice-lev">${response.lev}</p>
                                       </div>
                                     </div>
                                     <div class="voice-content-action">
                                       <button class="voice-add" type="button">Добавить как верный ответ</button>
                                     </div>
                                   </div>`);

                        $content.find('.voice-add').on('click', function () {
                          VoiceResponseInfo.setContent(dom.wrapper, '<div class="voice-result"><p>...</p></div>');
                          VoiceResponseInfo.send(currentQuestion.id, response.input, function (data) {

                            addQuestionAnswer(currentQuestion, data.answer);

                            VoiceResponseInfo.setContent(dom.wrapper, '<div class="voice-result"><p>Успешно</p></div>');
                            setTimeout(function () {
                              VoiceResponseInfo.remove(dom.wrapper);
                            }, 1000);
                          })
                        });

                        var info = VoiceResponseInfo.create($content);
                        dom.wrapper.append(info);
                      }

                    }
                  });
                }
              }
            });
          }
        });
        dom.wrapper.append(elem);
      }
    }
  }

  function sayQuestionName(question, onEndCallback) {
    cancelSpeech();
    if (haveAudioFile(question)) {
      playAudio(getAudioFile(question), onEndCallback);
    } else {
      speech.readText(question.name, testConfig.getAskQuestionLang(), onEndCallback);
    }
  }

  function sayCorrectAnswerName(text, onEndCallback) {
    cancelSpeech();
    speech.readText(text, testConfig.getInputVoice(), onEndCallback);
  }

  function cancelSpeech() {
    if (testConfig.isAskQuestion()) {
      speech.cancel();
    }
    if (audio) {
      audio.pause();
      audio.currentTime = 0;
      audio = null;
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
    console.debug('WikidsStoryTest.showCorrectAnswerPage');

    var $elements = $('<div/>');
    var text = incorrectAnswerText || 'Правильный ответ';
    text = text.replace('{1}', question.entity_name);
    $elements.append($('<h4/>').text(text + ':'));

    if (question.incorrect_description) {
      $elements.append(
        $('<div/>', {class: 'question-description__wrap'}).append(
          createDescription(question.incorrect_description)
        )
      );
    }

    var questionNeoParams = question['params'] || [];

    if (questionViewRegion(question)) {
      $elements.append(that.regionQuestion.createSuccess(question));
    }
    else if (questionViewPassTest(question)) {
      $elements.append(that.passTestQuestion.getContent(question.payload));
    }
    else if (questionViewDragWords(question)) {
      $elements.append(that.dragWordsQuestion.getContent(question.payload));
    }
    else if (testConfig.sourceIsNeo() && neoQuestionWithAnimalSigns()) {

      var $elementRow = $('<div/>', {'class': 'row row-no-gutters'});
      var $elementCol = $('<div/>', {'class': 'col-md-8 col-md-offset-2'});
      $elementCol.appendTo($elementRow);

      var $table = $('<table class="table table-responsive animal-sign-table"><tbody></tbody></table>');
      $table.appendTo($elementCol);

      var correctAnswers = getCorrectAnswers(question);
      var answerIsCorrect = function (id) {
        var correct = false;
        correctAnswers.forEach(function (answer) {
          if (parseInt(answer.id) === parseInt(id)) {
            correct = true;
            return;
          }
        });
        return correct;
      }

      $('.wikids-test-question[data-question-id=' + question.id + ']', dom.questions)
        .find('.wikids-test-answer').each(function () {

        var $span = $('<span/>', {'class': 'label wikids-animal-sign'})
          .html($(this).find('label').text());

        var id = $(this).find('input').val();
        var correct = ($.inArray(id.toString(), answer) !== -1);

        if (answerIsCorrect(id)) {
          $span.addClass('label-success');
        } else {
          $span.addClass('label-default');
        }

        var userPick = '';
        if (correct) {
          userPick = '<i class="glyphicon glyphicon-check"></i> ';
        }

        $('<tr/>')
          .append(
            $('<td/>').html(userPick)
          )
          .append(
            $('<td/>').html($span)
          )
          .appendTo(
            $table.find('tbody')
          );
      });

      if (questionNeoParams.length > 0) {

        questionNeoParams.forEach(function (param) {

          $('<span/>', {
            'text': 'Все признаки ' + param.animal,
            'class': 'label label-primary',
            'css': {'cursor': 'pointer', 'display': 'inline-block'}
          })
            .on('click', function (e) {

              var $elementRow = $('<div/>', {
                'class': 'row row-no-gutters',
                'css': {
                  'padding-top': '10px',
                  'background-color': 'white',
                  'height': '100%',
                  'overflow-y': 'auto'
                }
              });

              $elementRow.on('click', '[data-slide-id]', function () {
                var slideId = $(this).data('slideId');
                createSlideInnerDialog(slideId);
              });

              var signsByGroup = [];
              param.signs.forEach(function (signId) {
                var sign = findAnimalSignItem(signId['id']);
                if (!signsByGroup[sign.group_name]) {
                  signsByGroup[sign.group_name] = [sign];
                } else {
                  signsByGroup[sign.group_name].push(sign);
                }
              });

              for (var signName in signsByGroup) {

                var $elementCol = $('<div/>', {'class': 'col-md-6', 'css': {'padding': '10px'}});

                var $dl = $('<dl/>', {'class': 'dl-horizontal'});
                $('<dt/>', {'text': signName, 'title': signName, 'css': {'width': '200px'}})
                  .appendTo($dl);

                signsByGroup[signName].forEach(function (sign) {

                  var signItem = sign.name;
                  if (sign['slide_id']) {
                    signItem = $('<span/>', {
                      'data-slide-id': sign['slide_id'],
                      'text': sign.name,
                      'css': {
                        'cursor': 'pointer',
                        'user-select': 'none',
                        'text-decoration': 'underline'
                      }
                    });
                  }

                  var $dd = $('<dd/>', {'css': {'margin-left': '220px'}})
                    .html(signItem);
                  $dd.appendTo($dl);

                  $dl.appendTo($elementCol);
                });

                $elementCol.appendTo($elementRow);
              }

              createInnerDialog('Признаки - ' + param.animal, $elementRow);
            })
            .wrap($('<div/>', {'class': 'text-center'}))
            .parent()
            .appendTo($elementCol);
        });
      }

      $elementRow.appendTo($elements);
    }
    else {
      var $element;
      var answerText = '';
      var userAnswer = answer[0];

      var allAnswers = getAnswersData(question);
      if (questionViewSequence(question)) {
        $elements.append(that.sequenceQuestion.createCorrectPage(question, allAnswers, showOriginalImage));
      }
      else {
        allAnswers.forEach(function (questionAnswer) {
          $element = $('<div/>').addClass('row');
          var $content = $('<div/>').addClass('col-md-offset-3 col-md-9');
          if (parseInt(questionAnswer.is_correct) === 1) {

            answerText = questionAnswer.name;

            if (questionAnswer.image) {
              var $image = $('<img/>')
                .attr("src", questionAnswer.image)
                .attr("width", 110)
                .css('cursor', 'zoom-in')
                .on('click', function () {
                  showOriginalImage(questionAnswer['orig_image'] || $(this).attr('src'));
                });
              $content.append($image);
            }

            var $answerElement;
            if (testConfig.answerTypeIsRecording()) {
              $answerElement = $('<p/>')
                .append($('<span/>').text(answerText))
                .append($('<a/>')
                  .attr('href', '')
                  .attr('title', 'Прослушать')
                  .css('font-size', '3rem')
                  .on('click', function (e) {
                    e.preventDefault();
                    if (haveAudioFile(question)) {
                      playAudio(getAudioFile(question));
                    } else {
                      speech.readText(questionAnswer.name, testConfig.getInputVoice());
                    }
                  })
                  .html('<i class="glyphicon glyphicon-volume-up" style="left: 10px; top: 6px"></i>')
                );
            } else {
              if (testConfig.answerTypeIsInput(question) && testConfig.isStrictAnswer()) {
                $answerElement = $('<p/>').html(textDiff(answerText, userAnswer));
              } else {
                $answerElement = $('<p/>').text(answerText);
              }
            }
            $content.append($answerElement);

            if (testConfig.answerTypeIsInput(question)) {
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
      }
    }

    if (testConfig.answerTypeIsRecording()) {
      dom.correctAnswerPage.find('.correct-answer-page-next').hide();
    }

    //dom.questions.hide();
    //dom.controls.hide();
    //dom.wrapper.addClass('wikids-test--no-controls');
    dom.correctAnswerPage
      .find('.wikids-test-correct-answer-answers')
      .empty()
      .html($elements[0].childNodes)
      .end()
      .show();

    if (testConfig.answerTypeIsRecording()) {
      setTimeout(function () {
        speech.readText(answerText, testConfig.getInputVoice(), correctAnswerPageNext);
      }, 600);
    }
  }

  function showNextButton() {
    console.debug('showNextButton');

    dom.wrapper.addClass('wikids-test--no-controls');
    dom.controls.hide();

    if (!testConfig.sourceIsWord()
      && !questionViewDefault(currentQuestion)
      && !questionViewSvg(currentQuestion)
      && !questionViewRegion(currentQuestion)
      && !questionViewPoetry(currentQuestion)
      && !testConfig.sourceIsNeo()) {
      dom.wrapper.removeClass('wikids-test--no-controls');
      dom.controls.show();
      dom.nextButton.show();
    }
  }

  function hideNextButton() {
    dom.nextButton.hdie();
  }

  function continueTestAction(answer) {
    console.debug('continueTestAction');

    dom.continueButton.hide();

    var isLastQuestion = (testQuestions.length === 0);
    // var actionRelated = incorrectAnswerActionRelated();
    var showCorrectAnswerPageCondition = testConfig.sourceIsWord()
      && !testConfig.answerTypeIsNumPad(currentQuestion)
      && !testConfig.answerTypeIsRecording(currentQuestion)
      && !testConfig.answerTypeIsInput(currentQuestion)
      && !testConfig.answerTypeIsMissingWords(currentQuestion);

    if (isLastQuestion) {

      if (!answerIsCorrect) {
        if (showCorrectAnswerPageCondition) {
          showNextQuestion();
          dom.results.hide();
          showNextButton();
        } else {
          showCorrectAnswerPage(currentQuestion, answer);
        }
      } else {
        if (that.options.forSlide) {
          dispatchEvent("backToStory", {});
        } else {
          finish();
        }
      }
    } else {
      if (!answerIsCorrect) {
        if (showCorrectAnswerPageCondition) {
          showNextQuestion();
          dom.results.hide();
          showNextButton();
        } else {
          showCorrectAnswerPage(currentQuestion, answer);
        }
      } else {

        questionList.push(currentQuestion);

        showNextQuestion();
        dom.results.hide();
        showNextButton();
      }
    }
  }

  this.getQuestionList = () => questionList;

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

  function createSlideInnerDialog(slideId, title) {
    title = title || '';
    var content = $('<iframe>', {
      'src': '/question-hints/view-slide-by-id?id=' + slideId,
      'frameborder': 0,
      'scrolling': 'no',
      'width': '100%',
      'height': '100%'
    });
    createInnerDialog(title, content);
  }

  function createInnerDialog(title, content) {

    var defIndex = 400;
    $(Reveal.getCurrentSlide()).find('.slide-hints-wrapper').each(function () {
      defIndex++;
    });
    var $hintWrapper = $('<div/>', {'class': 'slide-hints-wrapper', 'css': {'z-index': defIndex}});
    var $hintBackground = $('<div/>', {'class': 'slide-hints-background'});
    var $hintInner = $('<div/>', {'class': 'slide-hints-inner'});
    var $hint = $('<div/>', {'class': 'slide-hints'});

    $hintBackground.appendTo($hintWrapper);
    $hintInner.appendTo($hintWrapper);

    $('<header/>', {'class': 'slide-hints-header'})
      .append(
        $('<h3/>', {class: 'slide-hints-header__title'}).text(title)
      )
      .append(
        $('<div/>', {'class': 'header-actions'})
          .append(
            $('<button/>', {
              'class': 'hints-close',
              'html': '&times;'
            })
              .on('click', function () {
                $hintWrapper.hide();
                $(this).parents('.slide-hints-wrapper:eq(0)').remove();
                if (!$(Reveal.getCurrentSlide()).find('.slide-hints-wrapper').length) {
                  $('.reveal .story-controls').show();
                }
              })
          )
      )
      .appendTo($hintInner);

    $hint.append(content);
    $hint.appendTo($hintInner);

    $('.reveal .story-controls').hide();
    $('.reveal .slides section.present').append($hintWrapper);
  }

  //PluginManager.initializePlugins(this, el, {});

  this.getCurrentQuestionElement = function () {
    return currentQuestionElement;
  }

  this.getCorrectAnswer = function (question) {
    return getCorrectAnswers(question);
  };

  this.getCurrentQuestion = getCurrentQuestion;

  this.hideNextButton = function () {
    dom.nextButton.hide();
  };

  this.showNextButton = function () {
    dom.nextButton.show();
  };

  this.nextQuestion = nextQuestion;

  this.checkAnswerCorrect = checkAnswerCorrect;

  tests.push(el);

  this.canNext = function () {
    //var canNext = currentStudent && (currentStudent.progress === 100 || currentStudent['finish']);
    var canNext = activeStudent.getProgress() === 100 || activeStudent.getFinish();
    return (testIsRequired() && canNext) || (!testIsRequired());
  };

  this.showOrigImage = showOriginalImage;

  this.getTestingId = () => {
    return testingId;
  };

  this.getCurrentQuestionId = () => {
    return currentQuestion && currentQuestion.id;
  };

  return {
    run,
    "load": load,
    "restore": restore,
    "addEventListener": function (type, listener, useCapture) {
      if ('addEventListener' in window) {
        dom.wrapper[0].addEventListener(type, listener, useCapture);
      }
    },
    "getTestConfig": function () {
      return testConfig;
    },
    "isTestSlide": function () {
      return ($('[data-test-id]', Reveal.getCurrentSlide()).length > 0);
    }
  };
}

WikidsStoryTest.create = function (el, options) {
  return new WikidsStoryTest(el, options);
};

WikidsStoryTest.mount = function () {
  for (var _len = arguments.length, plugins = new Array(_len), _key = 0; _key < _len; _key++) {
    plugins[_key] = arguments[_key];
  }
  if (plugins[0].constructor === Array) plugins = plugins[0];
  plugins.forEach(function (plugin) {
    if (!plugin.prototype || !plugin.prototype.constructor) {
      throw "WikidsStoryTest: Mounted plugin must be a constructor function, not ".concat({}.toString.call(plugin));
    }
    PluginManager.mount(plugin);
  });
};

WikidsStoryTest.mount(RecordingAnswer);
WikidsStoryTest.mount(SequenceQuestion);
WikidsStoryTest.mount(MissingWords);
WikidsStoryTest.mount(RegionQuestion);
WikidsStoryTest.mount(VoiceResponse);
WikidsStoryTest.mount(PassTest);
WikidsStoryTest.mount(DragWords);
WikidsStoryTest.mount(Poetry);

WikidsStoryTest.getTests = function () {
  return tests;
}

window.WikidsStoryTest = WikidsStoryTest;
