const TestSpeech = function(options) {

  var defaultOptions = {
    pitch: 1,
    rate: 0.8
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
    },
    'cancel': function() {
      synthesis.cancel();
    }
  }
}

export default TestSpeech;
