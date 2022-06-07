import {_extends} from "../common";

const VoiceResponse = function() {
  this.recognition = null;
};

VoiceResponse.prototype = {
  setRecognition: function(recognition) {
    this.recognition = recognition;
  },
  start: function(event, startCallback) {
    if (typeof startCallback === 'function') {
      this.recognition.setCallback('onStart', startCallback);
    }
    this.recognition.start(event);
  },
  stop: function(endCallback) {
    if (typeof endCallback === 'function') {
      this.recognition.setCallback('onEnd', endCallback);
    }
    this.recognition.stop();
  },
};

VoiceResponse.remove = function(container) {
  var elem = container.find('.question-voice');
  if (elem.length) {
    elem.fadeOut().remove();
  }
};

VoiceResponse.create = function(action) {

  var $button = $('<div class="gn"><div class="mc"></div></div>');
  $button.on('click', action);

  var $voiceWrap = $('<div class="question-voice"><div class="question-voice__inner"></div></div>');
  $voiceWrap.find('.question-voice__inner')
    .append($button);
  return $voiceWrap;
}

_extends(VoiceResponse, {
  pluginName: 'voiceResponse'
});

export default VoiceResponse;
