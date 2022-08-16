import {_extends} from "../common";
import RecognitionControl from "../components/RecognitionControl";

const MissingWords = function(test) {

  var control;
  this.control = control = new RecognitionControl(test);
  this.recognition = null;
  this.test = test;

  this.init = function(question, answer) {
    var element = $('<div/>', {
      'class': 'missing-words test-recognition'
    });
    var that = this;
    element.data('correctAnswer', answer.name);
    element
      .append($('<p/>', {
        'class': 'missing-words-text',
        'html': createMaskedString(question.name)
      }));
    element.on('click', 'span.label', function (e) {
      that.start(e, question.id, $(this).attr('data-match'));
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
      .attr('href', '')
      .attr('title', 'Остановить')
      .addClass('recognition-stop')
      .on('click', function(e) {
        e.preventDefault();
        that.recognition.stop();
      })
      .append($('<i/>').addClass('glyphicon glyphicon-stop'))
      .hide()
      .appendTo(element);
    return element;
  };

  this.addRecognitionListeners = function() {
    if (this.recognition === null) {
      return;
    }
    var that = this;
    this.recognition.addEventListener('onStart', function() {
      test.hideNextButton();
      control.setStatus('Идет запись с микрофона');
    });
    this.recognition.addEventListener('onResult', function(event) {
      var args = event.args;
      var elem = $(args.target);
      var match = elem.attr('data-match')
      var result = $.trim(args.result);
      elem.text(result);
      if (result.length >= match.length) {
        that.recognition.stop();
      }
    });
    this.recognition.addEventListener('onEnd', function(event) {
      control.hideLoader();
      control.hideStopButton();
      control.setStatus();
      var args = event.args,
        elem = $(args.target),
        match = elem.attr('data-match');
      var result = control.getMissingWordsText();
      if (checkResult(result)) {
        that.resetMatchElements();
        test.nextQuestion([result]);
      }
      else {
        correctResult(match, args.result).done(function(response) {
          elem.text(response.result);
          result = control.getMissingWordsText();
          if (checkResult(result)) {
            that.resetMatchElements();
            test.nextQuestion([result]);
          }
          else {
            test.showNextButton();
          }
        });
      }
    });
  }

  function correctResult(match, result) {
    return $.post('/morphy/root', {
      match, result
    });
  }

  function createMaskedString(string) {
    var re = /\{([\wа-яА-ЯёЁ\s]+)\}/igm;
    var match;
    while ((match = re.exec(string)) !== null) {
      string = string.replace(match[0], '<span style="cursor:pointer" class="label label-primary" data-match="'+match[1]+'">' + createRepeatString(match[1]) + '</span>')
    }
    return string;
  }

  function createRepeatString(string) {
    return string.split(' ').map(function(word) {
      return '*'.repeat(word.length);
    }).join('_');
  }

  this.resetMatchElements = function() {
    control.getMissingWordsElement().find('span.label').each(function() {
      var match = $(this).attr('data-match');
      $(this).text(createRepeatString(match));
    });
  };

  function checkResult(result) {
    return test.checkAnswerCorrect(
      test.getCurrentQuestion(),
      [result],
      function(elem) {
        return elem.name.toLowerCase();
      },
      false);
  }
}

MissingWords.prototype = {
  start: function(event, questionID, match) {
    this.control.setStatus();
    this.control.showLoader();
    this.control.showStopButton();
    this.recognition.start(event, match);
  },
  getResult: function() {
    return this.control.getMissingWordsText();
  },
  setRecognition: function(recognition) {
    this.recognition = recognition;
    this.addRecognitionListeners();
  }
};

_extends(MissingWords, {
  pluginName: 'missingWords'
});

export default MissingWords;
