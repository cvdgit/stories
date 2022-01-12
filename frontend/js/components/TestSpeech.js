export default class TestSpeech {

    constructor(options) {

        const defaultOptions = {
            pitch: 1,
            rate: 0.8
        };
        this.options = Object.assign(defaultOptions, options || {});

        this.voices = [];
        this.voice = 'Google русский';
    }

    setVoice(voice) {
        this.voice = voice;
    }

    getSpeech() {
        return new Promise(function(resolve, reject) {
            let handle;
            handle = setInterval(function() {
                if (speechSynthesis.getVoices().length > 0) {
                    resolve(speechSynthesis.getVoices());
                    clearInterval(handle);
                }
            }, 50);
        });
    }

    read(voices, text, onEnd) {

        const utterance = new SpeechSynthesisUtterance(text);

        for (let [key, value] of Object.entries(this.options)) {
            utterance[key] = value;
        }

        for (let i = 0; i < voices.length; i++) {
            if (voices[i].name === this.voice) {
                utterance.voice = voices[i];
                break;
            }
        }

        if (typeof onEnd === 'function') {
            utterance.onend = onEnd;
        }

        utterance.onerror = (event) => {
            console.log('An error has occurred with the speech synthesis: ' + event.error);
            if (typeof onEnd === 'function') {
                onEnd();
            }
        }

        setTimeout(() => {
            speechSynthesis.speak(utterance);
        }, 50);
    }

    readText(text, onEnd) {
        if (this.voices.length > 0) {
            this.read(this.voices, text, onEnd);
        }
        else {
            this.getSpeech().then((voices) => {
                this.voices = voices;
                this.read(voices, text, onEnd);
            });
        }
    }

    cancel() {
        speechSynthesis.cancel();
    }
}