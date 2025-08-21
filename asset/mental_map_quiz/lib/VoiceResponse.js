export default function VoiceResponse(recognition) {
  this.recognition = recognition
}

VoiceResponse.prototype = {
  start(event, lang, startCallback) {
    this.recognition.start(event, lang);
    if (typeof startCallback === 'function') {
      this.recognition.setCallback('onStart', startCallback);
    }
  },
  stop(endCallback) {
    if (typeof endCallback === 'function') {
      this.recognition.setCallback('onEnd', endCallback);
    }
    this.recognition.stop();
  },
  onResult(callback) {
    this.recognition.addEventListener('onResult', callback);
  },
  onEnd(callback) {
    this.recognition.addEventListener('onEnd', callback);
  },
  getStatus() {
    return this.recognition.getStatus()
  }
}
