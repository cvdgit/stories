
const MissingWordsRecognition = function(config) {

  var recorder = new webkitSpeechRecognition();
  recorder.continuous = true;
  recorder.interimResults = true;
  recorder.lang = config.getRecordingLang() || 'ru-RU';

  var recognizing = false;
  var startTimestamp = null;
  var finalTranscript = '';
  var targetElement;

  var eventListeners = [];
  var callbacks = {};

  recorder.onstart = function() {
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
    callCallback('onEnd', {
      target: targetElement,
      result: linebreak(finalTranscript)
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
    addEventListener: function(type, eventHandler) {
      var listener = {};
      listener.type = type;
      listener.eventHandler = eventHandler;
      eventListeners.push(listener);
    },
    setCallback: function(type, callback) {
      callbacks[type] = callback;
    }
  }
}

export default MissingWordsRecognition;
