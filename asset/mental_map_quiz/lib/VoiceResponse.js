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
  onEnd(callback) {
    this.recognition.addEventListener('onEnd', callback);
  },
  getStatus() {
    return this.recognition.getStatus()
  },
  onError(callback) {
    this.recognition.addEventListener('onError', callback);
  },
  onStart(callback) {
    this.recognition.addEventListener('onStart', callback);
  }
}
