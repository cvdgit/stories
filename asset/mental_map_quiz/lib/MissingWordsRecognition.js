export default function MissingWordsRecognition(config) {

  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
  const recorder = new SpeechRecognition()

  recorder.continuous = true;
  recorder.interimResults = true;
  recorder.lang = config.getRecordingLang?.() || 'ru-RU'; // en-US

  let recognizing = false;
  let startTimestamp = null;
  let finalTranscript = '';
  let targetElement;

  const eventListeners = [];
  const callbacks = {};

  recorder.onstart = function () {
    recognizing = true;
    dispatchEvent({type: 'onStart'});
    callCallback('onStart');
  };

  function callCallback(type, args) {
    const callback = callbacks[type];
    args = args || {};
    if (typeof callback === 'function') {
      callback(args);
    }
  }

  const DICTIONARY = {
    'директом': 'Directum',
    'директум': 'Directum'
  }

  function editInterim(s) {
    return s
      .split(' ')
      .map((word) => {
        word = word.trim()
        return DICTIONARY[word.toLowerCase()] ? DICTIONARY[word.toLowerCase()] : word
      })
      .join(' ')
  }

  recorder.onresult = function (event) {

    let interimTranscript = '';
    if (typeof (event.results) === 'undefined') {
      recorder.onend = null;
      recorder.stop();
      return;
    }

    for (let i = event.resultIndex; i < event.results.length; ++i) {
      if (event.results[i].isFinal) {
        const result = editInterim(event.results[i][0].transcript)
        finalTranscript += result;
      } else {
        interimTranscript += event.results[i][0].transcript;
      }
    }

    finalTranscript = capitalize(finalTranscript);
    dispatchEvent({
      type: 'onResult',
      args: {
        target: targetElement,
        result: linebreak(finalTranscript),
        interim: linebreak(interimTranscript)
      }
    });
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
    let result
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
    addEventListener(type, eventHandler) {
      const existsListener = eventListeners.find(e => e.type === type)
      if (existsListener) {
        existsListener.eventHandler = eventHandler
        return
      }
      eventListeners.push({type, eventHandler})
    },
    setCallback(type, callback) {
      callbacks[type] = callback
    },
    getStatus: () => recognizing
  }
}
