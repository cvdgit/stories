const RetellingPlugin = window.RetellingPlugin || (function () {
  const {retelling} = Reveal.getConfig()

  const MissingWordsRecognition = function (config) {

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
    const recorder = new SpeechRecognition()

    recorder.continuous = true;
    recorder.interimResults = true;
    recorder.lang = config.getRecordingLang?.() || 'ru-RU';

    var recognizing = false;
    var startTimestamp = null;
    var finalTranscript = '';
    var targetElement;

    var eventListeners = [];
    var callbacks = {};

    recorder.onstart = function () {
      recognizing = true;
      dispatchEvent({type: 'onStart'});
      callCallback('onStart');
    };

    function callCallback(type, args) {
      var callback = callbacks[type];
      args = args || {};
      if (typeof callback === 'function') {
        callback(args);
      }
    }

    recorder.onresult = function (event) {

      var interimTranscript = '';
      if (typeof (event.results) === 'undefined') {
        recorder.onend = null;
        recorder.stop();
        return;
      }

      for (var i = event.resultIndex; i < event.results.length; ++i) {
        if (event.results[i].isFinal) {
          finalTranscript += event.results[i][0].transcript;
        } else {
          interimTranscript += event.results[i][0].transcript;
        }
      }

      //if (finalTranscript.length) {
        finalTranscript = capitalize(finalTranscript);
        dispatchEvent({
          type: 'onResult',
          args: {
            target: targetElement,
            result: linebreak(finalTranscript),
            interim: linebreak(interimTranscript)
          }
        });
      //}
    };

    recorder.onend = function () {
      recognizing = false;
      dispatchEvent({
        type: 'onEnd',
        args: {
          target: targetElement,
          result: linebreak(finalTranscript)
        }
      });
      callCallback('onEnd', {
        target: targetElement,
        result: linebreak(finalTranscript)
      });
    }

    function errorString(error) {
      var result = '';
      switch (error) {
        case 'no-speech':
          result = 'Речи не обнаружено';
          break;
        case 'audio-capture':
          result = 'Не удалось захватить звук';
          break;
        case 'not-allowed':
          result = 'Пользовательский агент запретил ввод речи из соображений безопасности, конфиденциальности или предпочтений пользователя';
          break;
        default:
          result = error;
      }
      return result
    }

    recorder.onerror = function (event) {

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
      return s.replace(first_char, function (m) {
        return m.toUpperCase();
      });
    }

    function lowerCase(s) {
      return s.toLowerCase();
    }

    function onStartCallback(callback) {
      if (typeof callback === 'function') {
        return callback;
      }
    }

    function onEndCallback(callback) {
      if (typeof callback === 'function') {
        return callback;
      }
    }

    return {
      start,
      stop,
      addEventListener: function (type, eventHandler) {
        var listener = {};
        listener.type = type;
        listener.eventHandler = eventHandler;
        eventListeners.push(listener);
      },
      setCallback: function (type, callback) {
        callbacks[type] = callback;
      },
      getStatus: () => recognizing
    }
  }

  const VoiceResponse = function () {
    this.recognition = null;
  };

  VoiceResponse.prototype = {
    setRecognition: (recognition) => {
      this.recognition = recognition;
    },
    start: (event, startCallback) => {
      if (typeof startCallback === 'function') {
        this.recognition.setCallback('onStart', startCallback);
      }
      this.recognition.start(event);
    },
    stop: (endCallback) => {
      if (typeof endCallback === 'function') {
        this.recognition.setCallback('onEnd', endCallback);
      }
      this.recognition.stop();
    },
    onResult: (callback) => {
      this.recognition.addEventListener('onResult', callback);
    },
    getStatus: () => this.recognition.getStatus()
  };

  const voiceResponse = new VoiceResponse()
  voiceResponse.setRecognition(new MissingWordsRecognition({}))
  voiceResponse.onResult((args) => {
    document.getElementById("final_span").innerText = args.args?.result
    document.getElementById("interim_span").innerText = args.args?.interim
  })

  function InnerDialog(title, content) {

    const html = `<div class="slide-hints-wrapper" style="background-color: white">
    <div style="display: flex; flex-direction: column; height: 100%; width: 100%; flex: 1 1 auto; justify-content: space-between">
        <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center">
            <h2>${title}</h2>
            <div class="header-actions">
                <button type="button" class="hints-close">&times;</button>
            </div>
        </div>
        <div id="dialog-body" style="display: flex; flex-direction: column; flex: 1 1 auto; max-height: 100%; overflow-y: auto;"></div>
    </div>
</div>
`
    this.showHandler = null
    this.hideHandler = null

    const hideDialog = () => {

      Reveal.configure({ keyboard: true })

      if ($(Reveal.getCurrentSlide()).find('.slide-hints-wrapper').length) {
        $(Reveal.getCurrentSlide())
          .find('.slide-hints-wrapper')
          .hide()
          .remove()
      }
      $('.reveal .story-controls').show()

      if (typeof this.hideHandler === "function") {
        this.hideHandler()
      }
    }

    this.show = () => {

      Reveal.configure({ keyboard: false })

      const $element = $(html)

      $element.find("#dialog-body").append(content)
      $element.on("click", ".hints-close", hideDialog)

      $('.reveal .story-controls').hide()
      $('.reveal .slides section.present')
        .append($element)
        .find(".slide-hints-wrapper")
        .show()

      if (typeof this.showHandler === "function") {
        this.showHandler($element)
      }
    }

    this.hide = hideDialog;

    this.onShow = callback => {
      this.showHandler = callback
    }

    this.onHide = callback => {
      this.hideHandler = callback
    }
  }

  const content = `<div style="height: 100%; position: relative; overflow-y: auto; display: flex; flex-direction: column; justify-content: space-between; flex: 1 1 auto">
    <div style="max-height: 100%; overflow-y: auto; display: flex; flex-direction: column; flex: 1 1 auto;">
        <h3>Пересказ пользователя:</h3>
        <div style="padding: 20px 40px; margin-bottom: 20px; background-color: #eee; font-size: 2.5rem; border-radius: 2rem">
                    <span contenteditable="plaintext-only" id="final_span"
                          style="outline: 0; background-color: #eee; line-height: 50px; color: black; margin-right: 3px; padding: 10px"></span>
            <span id="interim_span" style="color: gray"></span>
        </div>
        <div style="display: flex; flex-direction: row; align-items: center; justify-content: center">
            <button style="display: none" id="start-retelling" class="btn" type="button">Проверить</button>
        </div>
    </div>
    <div id="voice-area" style="position: relative; padding: 20px; height: 150px">
        <div id="voice-control" class="question-voice" data-trigger="hover"
             title="Нажмите что бы начать запись с микрофона"
             style="display: block; position:relative; bottom: 0">
            <div class="question-voice__inner">
                <div id="start-recording" class="gn">
                    <div class="mc"></div>
                </div>
            </div>
        </div>
        <div id="voice-loader" style="display: none">
            <div style="display: flex; flex-direction: row; align-items: center; justify-content: center">
                <img src="/img/loading.gif" alt="">
            </div>
        </div>
        <div id="retelling-response"
             style="display: none; font-size: 2.4rem; line-height: 3rem; overflow: auto; max-height: 100%;"></div>
    </div>
</div>
`

  const feedbackDialog = new InnerDialog('Пересказ', content)

  function startRecording(element) {
    const state = $(element).data('state')
    const $that = $(element)
    if (!state) {
      $(document.getElementById("start-retelling")).hide()
      setTimeout(function () {
        voiceResponse.start(new Event('voiceResponseStart'), function () {
          $that.data('state', 'recording');
          $that.addClass('recording');
          $that.before('<div class="pulse-ring"></div>');
        });
      }, 500);
    } else {
      voiceResponse.stop(function (args) {
        $that.siblings('.pulse-ring').remove();
        $that.removeClass('recording');
        $that.removeData('state');

        if ($(document.getElementById("final_span")).text().trim().length) {
          $(document.getElementById("start-retelling")).show()
        }
      })
    }
  }

  async function sendMessage(payload, $elem) {
    let accumulatedMessage = ""

    return sendEventSourceMessage({
      url: "/admin/index.php?r=gpt/stream/retelling",
      headers: {
        Accept: "text/event-stream",
        "X-CSRF-Token": $("meta[name=csrf-token]").attr("content")
      },
      body: JSON.stringify(payload),
      onMessage: (streamedResponse) => {
        $elem.find("#voice-loader").hide()
        $elem.find("#retelling-response").show()
        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }
        $elem.find("#retelling-response").text(accumulatedMessage)
      },
      onError: (streamedResponse) => {
        $elem.find("#voice-loader").hide()
        $elem.find("#retelling-response").show()
        accumulatedMessage = streamedResponse?.error_text
        $elem.find("#retelling-response").text(accumulatedMessage)
      }
    })
  }

  async function startRetelling($elem) {

    $elem.find(".question-voice").tooltip("hide")
    const userResponse = $elem.find("#final_span").text().trim()

    if (!userResponse) {
      alert("Ответ пользователя пуст")
      return
    }

    const slideTexts = $(Reveal.getCurrentSlide())
      .find("[data-block-type=text]")
      .map((i, el) => $(el).text())
      .get()
      .join("\n")

    if (!slideTexts) {
      alert("На слайде нет текста")
      return
    }

    ["voice-control", "voice-loader", "retelling-response"]
      .map(id => $elem.find(`#${id}`).hide())
    $elem.find("#voice-loader").show()
    $elem.find("#retelling-response").empty()

    const response = await sendMessage({
      userResponse,
      slideTexts
    }, $elem)
  }

  feedbackDialog.onShow($element => {
    $element
      .on("click", "#start-recording", function() {
        startRecording(this)
      })
      .on("click", "#start-retelling", () => {
        startRetelling($element).then(() => {
          console.log("success")
        })
      })
      .on("input", "#final_span", function() {
        const display = $(this).text().length > 0 ? "block" : "none"
        if (display !== $element.find("#start-retelling").css("display")) {
          $element.find("#start-retelling").css("display", display)
        }
      })
    $element.find(".question-voice").tooltip("show")
  })

  feedbackDialog.onHide(() => {
    if (voiceResponse.getStatus()) {
      voiceResponse.stop()
    }
  })

  return {
    begin: () => {
      feedbackDialog.show()
    }
  };
})()
