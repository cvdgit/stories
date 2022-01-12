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
        console.log('getSpeech')
        return new Promise(function(resolve, reject) {
            let handle;
            handle = setInterval(function() {
                console.log('speechSynthesis.getVoices()')
                if (speechSynthesis.getVoices().length > 0) {
                    resolve(speechSynthesis.getVoices());
                    clearInterval(handle);
                    console.log('speechSynthesis.getVoices() - done')
                }
            }, 50);
        });
    }

    read(voices, text, onEnd) {
        console.log('read');

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
            if (typeof onEnd === 'function') {
                onEnd();
            }
        }

        setTimeout(() => {
            console.log('speechSynthesis.speak')
            speechSynthesis.speak(utterance);
        }, 50);
    }

    readText(text, onEnd) {
        if (this.voices.length > 0) {
            console.log('this.voices.length > 0')
            this.read(this.voices, text, onEnd);
        }
        else {
            this.getSpeech().then((voices) => {
                this.voices = voices;
                console.log(voices);
                this.read(voices, text, onEnd);
            });
        }
    }

    cancel() {
        speechSynthesis.cancel();
    }
}