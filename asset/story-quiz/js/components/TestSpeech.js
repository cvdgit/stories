const TestSpeech = function(options) {

  let defaultOptions = {
    pitch: 1,
    rate: 0.9
  };
  options = options || {};
  options = Object.assign(defaultOptions, options);

  const synthesis = window.speechSynthesis;

  function setSpeech() {
    return new Promise(function(resolve) {
      let handle;
      handle = setInterval(function() {
        if (synthesis.getVoices().length > 0) {
          resolve(synthesis.getVoices());
          clearInterval(handle);
        }
      }, 50);
    });
  }

  let voices = [];

  return {
    'readText': function(text, voice, onEnd) {

      const utterance = new SpeechSynthesisUtterance(text);
      voice = voice || 'Google русский';

      const read = (speechVoices) => {

        voices = speechVoices;

        for (let i = 0; i < voices.length; i++) {
          if (voices[i].name === voice) {
            utterance.voice = voices[i];
            break;
          }
        }

        for (let [key, value] of Object.entries(options)) {
          utterance[key] = value;
        }

        if (typeof onEnd === 'function') {
          utterance.onend = onEnd;
        }

        synthesis.speak(utterance);
      }

      if (!voices.length) {
        setSpeech().then((speechVoices) => read(speechVoices));
      }
      else {
        read(voices);
      }
    },
    'cancel': function() {
      synthesis.cancel();
    }
  }
}

export default TestSpeech;
