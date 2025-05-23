import Morphy from "../components/Morphy";
import {_extends} from "../common";
import RecognitionControl from "../components/RecognitionControl";
import sendMessage from "../../../mental_map_quiz/lib/sendMessage";

function processOutputAsJson(output) {
  let json = null
  try {
    json = JSON.parse(output.replace(/```json\n?|```/g, ''))
  } catch (ex) {

  }
  return json
}

const RecordingAnswer = function (test) {

  var control;
  this.control = control = new RecognitionControl(test);
  this.recognition = null;
  this.test = test;

  function checkResultLength(result, match) {
    return result.length >= match.replaceAll(/(\d+)#([\wа-яА-ЯёЁ]+)/uig, "$1").length;
  }

  this.addRecognitionListeners = function () {
    if (this.recognition === null) {
      return;
    }
    this.recognition.addEventListener('onStart', () => {
      test.hideNextButton();
      this.control.setStatus('Идет запись с микрофона');
      this.control.showStopButton();
      this.control.disableResult();
      this.control.addVoiceAnimation()
    });

    var that = this;
    this.recognition.addEventListener('onEnd', async function () {
      control.hideLoader();
      control.hideStopButton();
      control.setStatus();
      control.enableResult();

      that.control.removeVoiceAnimation()

      const result = that.getResult();
      if (result.length === 0) {
        that.control.setStatus('Речи не обнаружено')
        that.control.repeatButtonShow()
        return
      }

      if (that.checkResult(result)) {
        that.resetResult()
        test.nextQuestion([result])
        return
      }

      that.control.requestLoaderShow('Обработка ответа...')

      console.log('rewrite')
      await sendMessage(
        `/admin/index.php?r=gpt/stream/retelling-rewrite`,
        {
          userResponse: result,
          slideTexts: control.getCurrentCorrectAnswer()
        },
        (message) => control.setResult(message),
        (error) => console.log('error', error),
        () => {
        })

      if (that.checkResult(result.toLowerCase())) {
        that.resetResult()
        test.nextQuestion([result.toLowerCase()])
        return
      }

      console.log('retelling')
      await sendMessage(`/admin/index.php?r=gpt/stream/retelling`, {
          userResponse: result,
          slideTexts: that.control.getCurrentCorrectAnswer()
        },
        (message) => that.control.setRetellingResponse(message),
        (error) => console.log('error', error),
        () => {
        }
      )

      console.log('after', that.control.getRetellingResponse())
      const json = processOutputAsJson(that.control.getRetellingResponse())
      if (json === null) {
        console.log('no json')
        return
      }
      const val = Number(json?.overall_similarity)

      if (val > 85) {
        that.resetResult();
        test.nextQuestion([that.control.getCurrentCorrectAnswer()]);
      }

      /*var morphy = new Morphy();
      morphy.correctResult(control.getCurrentCorrectAnswer(), result).done(function(response) {
        result = response.result;
        control.setResult(result);
        if (that.checkResult(result)) {
          that.resetResult();
          test.nextQuestion([result]);
        }
        else {
          test.showNextButton();
        }
      })*/

      that.control.requestLoaderHide()
      control.repeatButtonShow();
      control.resultSetFocus();
    });

    this.recognition.addEventListener('onError', function (event) {
      control.hideLoader();
      control.setStatus(event.args.error);
    });

    let timeout
    this.recognition.addEventListener('onInterimResult', (e) => {
      this.control.voiceAnimOn()
      if (timeout) {
        clearTimeout(timeout)
        this.control.voiceAnimOn()
      }
      timeout = setTimeout(() => this.control.voiceAnimOff(), 500)
    });

    this.recognition.addEventListener('onResult', (event) => {

      const args = event.args
      const result = $.trim(args.result)
      this.control.setResult(result)

      /*
      var match = control.getCurrentCorrectAnswer();
      if (checkResultLength(result, match)) {
        that.recognition.stop();
      }*/
    });
  };
}

RecordingAnswer.prototype = {
  create: function (question, answer) {
    var that = this;
    var element = $('<div/>');
    element.addClass('test-recognition');
    element
      .append($('<div/>')
        .addClass('recognition-result-wrapper')
        .append(
          $('<div/>')
            .attr('contenteditable', 'plaintext-only')
            .addClass('recognition-result')
            .on('input', function (e) {
              /*var value = $(this).text();
              value.length > 0
                ? that.test.showNextButton()
                : that.test.hideNextButton();*/
            })
            .on('keydown', function (e) {
              if (e.key === "Enter") {
                e.preventDefault();
                var value = $(this).text();
                if (value.length > 0) {
                  that.resetResult();
                  that.test.nextQuestion([value]);
                }
              }
            })
        )
        .append($('<span/>').addClass('recognition-result-interim'))
        .append($('<span/>').addClass('retelling-response'))
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
      .attr('title', 'Повторить ввод с микрофона')
      .addClass('recognition-repeat')
      .on('click', function (e) {
        e.preventDefault();
        that.start(e);
      })
      .append($('<i/>').addClass('glyphicon glyphicon-refresh'))
      .hide()
      .appendTo(element);
    $('<a/>')
      .attr('href', '')
      .attr('title', 'Остановить')
      .addClass('recognition-stop')
      .on('click', function (e) {
        e.preventDefault();
        that.recognition.stop();
      })
      .append($('<i/>').addClass('glyphicon glyphicon-stop'))
      .hide()
      .appendTo(element);
    return element;
  },
  autoStart: function (event, timeout) {
    this.control.setStatus();
    this.control.repeatButtonHide();
    timeout = timeout || 1000;
    var that = this;
    setTimeout(function () {
      that.control.showLoader();
      that.recognition.start(event);
    }, timeout);
  },
  setRecognition: function (recognition) {
    this.recognition = recognition;
    this.addRecognitionListeners();
  },
  getResult: function () {
    return this.control.getResult();
  },
  resetResult: function () {
    this.control.setResult();
  },
  start: function (event) {
    this.control.setStatus();
    this.control.setResult();
    this.control.repeatButtonHide();
    this.control.showLoader();
    this.recognition.start(event);
  },
  checkResult: function (result) {
    return this.test.checkAnswerCorrect(
      this.test.getCurrentQuestion(),
      [result],
      function (elem) {
        return elem.name.toLowerCase();
      },
      false);
  }
};

_extends(RecordingAnswer, {
  pluginName: 'recordingAnswer'
});

export default RecordingAnswer;
