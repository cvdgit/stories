export default function VoiceResponse(recognition) {
  this.recognition = recognition
}

VoiceResponse.prototype = {
  start(event, lang, startCallback) {
    if (typeof startCallback === 'function') {
      this.recognition.setCallback('onStart', startCallback);
    }
    this.recognition.start(event, lang);
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
  getStatus() {
    return this.recognition.getStatus()
  }
}
