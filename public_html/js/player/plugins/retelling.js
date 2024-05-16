const RetellingPlugin = window.RetellingPlugin || (function () {
  const {retelling} = Reveal.getConfig()

  const MissingWordsRecognition = function (config) {

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
    const recorder = new SpeechRecognition()

    recorder.continuous = true;
    recorder.interimResults = true;
    recorder.lang = config.getRecordingLang?.() || 'ru-RU'; // en-US

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

    function start(event, lang) {
      if (recognizing) {
        recorder.stop();
        return;
      }
      finalTranscript = '';
      recorder.lang = lang
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
    start: (event, lang, startCallback) => {
      if (typeof startCallback === 'function') {
        this.recognition.setCallback('onStart', startCallback);
      }
      this.recognition.start(event, lang);
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
    const finalSpan = document.getElementById("final_span")
    if (finalSpan) {
      finalSpan.innerHTML = args.args?.result
    }
    const interimSpan = document.getElementById("interim_span")
    if (interimSpan) {
      interimSpan.innerHTML = args.args?.interim
    }
  })

  function InnerDialog(title, content) {
    const html = `<div class="slide-hints-wrapper" style="background-color: white">
    <div class="retelling-dialog-inner">
        <div class="retelling-dialog-header">
            <h2>${title}</h2>
            <div class="header-actions">
                <button type="button" class="hints-close">&times;</button>
            </div>
        </div>
        <div id="dialog-body" class="retelling-dialog-body"></div>
    </div>
</div>
`
    this.showHandler = null
    this.hideHandler = null

    const hideDialog = () => {

      Reveal.configure({keyboard: true})

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

      Reveal.configure({keyboard: false})

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

  const content = `<div class="retelling-two-cols">
    <div class="retelling-answers-col">
    <h2 class="h3">Генерация вопросов <button id="answers-abort" style="display: none" class="btn">Остановить</button></h2>
        <div class="retelling-answers" id="retelling-answers"></div>
        <div id="retelling-error" style="display: none" class="alert alert-danger retelling-error"></div>
    </div>
    <div class="retelling-content">
        <div style="max-height: 100%; overflow-y: auto; display: flex; flex-direction: column; flex: 1 1 auto;">
            <h3>Пересказ пользователя:</h3>
            <div style="padding: 20px 40px; margin-bottom: 20px; background-color: #eee; font-size: 2.5rem; border-radius: 2rem">
            <span contenteditable="plaintext-only" id="result_span"
                  style="outline: 0; background-color: #eee; line-height: 50px; color: black; margin-right: 3px; padding: 10px"></span>
                <span contenteditable="plaintext-only" id="final_span"
                      style="outline: 0; background-color: #eee; line-height: 50px; color: black; margin-right: 3px; padding: 10px"></span>
                <span id="interim_span" style="color: gray"></span>
            </div>
            <div style="display: flex; flex-direction: row; align-items: center; justify-content: center">
                <button style="display: none" id="start-retelling" class="btn" type="button">Проверить</button>
            </div>
        </div>
        <div id="voice-area" style="position: relative; padding: 20px; height: 150px">
            <div style="display: flex; gap: 20px; flex-direction: row; justify-content: center; align-items: center">
                <select id="voice-lang">
                    <option value="ru-RU" selected>rus</option>
                    <option value="en-US">eng</option>
                </select>
                <div id="voice-control" class="question-voice" data-trigger="hover"
                     title="Нажмите что бы начать запись с микрофона"
                     style="display: block; position:relative; bottom: 0; margin: 0">
                    <div class="question-voice__inner">
                        <div id="start-recording" class="gn">
                            <div class="mc"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="retelling-content-overlay" class="retelling-content-overlay" title="Пока недоступно"></div>
    </div>
</div>
`

  const feedbackDialog = new InnerDialog('Пересказ', content)

  function startRecording(element, lang) {
    const state = $(element).data('state')
    const $that = $(element)
    if (!state) {
      $(document.getElementById("start-retelling")).hide()
      setTimeout(function () {
        voiceResponse.start(new Event('voiceResponseStart'), lang, function () {
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

        const $resultSpan = $(document.getElementById("result_span"))
        const $finalSpan = $(document.getElementById("final_span"))

        if ($finalSpan.text().trim().length) {
          $resultSpan.text(
            $resultSpan.text().trim().length
              ? $resultSpan.text().trim() + "\n" + $finalSpan.text().trim()
              : $finalSpan.text().trim()
          )
          $finalSpan.empty()
        }

        if ($resultSpan.text().trim().length) {
          $(document.getElementById("start-retelling")).show()
        }
      })
    }
  }

  async function sendMessage(payload, $elem, onEndCallback) {
    let accumulatedMessage = ""

    return sendEventSourceMessage({
      url: "/admin/index.php?r=gpt/stream/retelling",
      headers: {
        Accept: "text/event-stream",
        "X-CSRF-Token": $("meta[name=csrf-token]").attr("content")
      },
      body: JSON.stringify(payload),
      onMessage: (streamedResponse) => {
        //$elem.find("#voice-loader").hide()
        $elem.find("#retelling-response").show()
        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }
        $elem.find("#retelling-response")[0].innerText = accumulatedMessage
        $elem.find("#retelling-response")[0].scrollTop = $elem.find("#retelling-response")[0].scrollHeight;
      },
      onError: (streamedResponse) => {
        //$elem.find("#voice-loader").hide()
        $elem.find("#retelling-response").show()
        accumulatedMessage = streamedResponse?.error_text
        $elem.find("#retelling-response")[0].innerText = accumulatedMessage
      },
      onEnd: onEndCallback
    })
  }

  async function getCache(slideTexts) {
    const json = await fetchCache(`/admin/index.php?r=gpt/cache/get`, {slideTexts})
    if (json?.success && json?.content) {
      return json.content
    }
    return ''
  }

  async function setCache(slideTexts, content) {
    return await fetchCache(`/admin/index.php?r=gpt/cache/set`, {slideTexts, content})
  }

  async function fetchCache(url, payload) {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      },
      body: JSON.stringify(payload),
    })
    if (!response.ok) {
      throw new Error(response.statusText)
    }
    return await response.json()
  }

  async function answersSendMessage({payload, onMessage, onError, onEndCallback, signal}) {
    let accumulatedMessage = ""
    return sendEventSourceMessage({
      url: "/admin/index.php?r=gpt/stream/retelling-answers",
      headers: {
        Accept: "text/event-stream",
        "X-CSRF-Token": $("meta[name=csrf-token]").attr("content")
      },
      body: JSON.stringify(payload),
      signal,
      onMessage: (streamedResponse) => {
        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }
        onMessage(accumulatedMessage)
      },
      onError: (streamedResponse) => {
        accumulatedMessage = streamedResponse?.error_text
        onError(accumulatedMessage)
      },
      onEnd: onEndCallback
    })
  }

  function getSlideText() {
    return $(Reveal.getCurrentSlide())
      .find("[data-block-type=text]")
      .map((i, el) => $(el).text())
      .get()
      .join("\n")
  }

  async function startRetelling($elem) {

    if (voiceResponse.getStatus()) {
      voiceResponse.stop()
    }

    const $wrap = $(`<div style="position: absolute; left: 0; top: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; background-color: rgba(0, 0, 0, .5)">
    <div style="background-color: #fff; padding: 20px; max-width: 800px; height: 500px; display: flex; justify-content: space-between; flex-direction: column; flex: 1 1 auto">
        <div contenteditable="plaintext-only" id="retelling-response"
             style="font-size: 2.2rem; text-align: left; line-height: 3.5rem; overflow-y: scroll; height: 100%; max-height: 100%;"></div>
        <div style="display: flex; margin-top: 10px; flex-direction: row; align-items: center; justify-content: center">
            <img id="voice-loader" height="50px" src="/img/loading.gif" alt="">
            <button style="display: none" id="voice-finish" type="button" class="btn">OK</button>
        </div>
    </div>
</div>`)

    $wrap.find("#voice-finish").on("click", feedbackDialog.hide)

    $elem.append($wrap)

    $elem.find(".question-voice").tooltip("hide")

    const userResponse = $elem.find("#result_span").text().trim()
    if (!userResponse) {
      alert("Ответ пользователя пуст")
      return
    }

    const slideTexts = getSlideText()
    if (!slideTexts) {
      alert("На слайде нет текста")
      return
    }

    ["voice-control"]
      .map(id => $elem.find(`#${id}`).hide())

    //$elem.find("#voice-loader").show()
    $wrap.find("#retelling-response").empty()

    const response = await sendMessage({
      userResponse,
      slideTexts
    }, $wrap, () => {
      $wrap.find("#voice-loader").hide()
      $wrap.find("#voice-finish").show()
    })
  }

  async function generateAnswers($elem, signal) {
    const slideTexts = getSlideText()
    if (!slideTexts) {
      alert("На слайде нет текста")
      return
    }

    const $overlay = $elem.find('#retelling-content-overlay')
    $overlay.show()

    const content = await getCache(slideTexts)
    if (content) {
      $elem.find("#retelling-answers")[0].innerHTML = content
      $overlay.hide()
      return
    }

    const $abortButton = $elem.find('#answers-abort')
    $abortButton.show()

    const response = await answersSendMessage({
      payload: {
        slideTexts
      },
      signal,
      onMessage: (message) => {
        $elem.find("#retelling-answers")[0].innerText = message
        $elem.find("#retelling-answers")[0].scrollTop = $elem.find("#retelling-answers")[0].scrollHeight;
      },
      onError: (error) => {
        $overlay.hide()
        $abortButton.hide()
        $elem.find('#retelling-error').text(error).slideDown()
      },
      onEndCallback: () => {
        $overlay.hide()
        $abortButton.hide()

        setCache(slideTexts, $elem.find("#retelling-answers").html())
      }
    })
  }

  feedbackDialog.onShow($element => {
    $element
      .on("click", "#start-recording", function () {
        const voiceLang = $element.find("#voice-lang option:selected").val()
        startRecording(this, voiceLang)
      })
      .on("click", "#start-retelling", () => {
        startRetelling($element).then(() => {
          console.log("success")
        })
      })
      .on("input", "#result_span", function () {
        const display = $(this).text().length > 0 ? "block" : "none"
        if (display !== $element.find("#start-retelling").css("display")) {
          $element.find("#start-retelling").css("display", display)
        }
      })
      .on("input", "#final_span", function () {
        const display = $element.find("#result_span").text().length > 0 ? "block" : "none"
        if (display !== $element.find("#start-retelling").css("display")) {
          $element.find("#start-retelling").css("display", display)
        }
      })

    // $element.find(".question-voice").tooltip("show")

    const controller = new AbortController()

    setTimeout(() => generateAnswers($element, controller.signal), 500)
    $element.find('#retelling-answers').text('...')

    $element.find('#answers-abort').on('click', function() {
      controller.abort()
      $(this).hide()
    });
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
