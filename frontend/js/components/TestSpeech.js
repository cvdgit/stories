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

    findVoice(voices, voiceName) {

        const voiceMap = [
            {name: 'Google русский', lang: 'ru-RU'},
            {name: 'Microsoft Irina Desktop - Russian', lang: 'ru-RU'},
            {name: 'Google US English', lang: 'en-US'},
            {name: 'Microsoft Zira Desktop - English (United States)', lang: 'en-US'},
            {name: 'Microsoft David Desktop - English (United States)', lang: 'en-US'}
        ];

        let voiceMapItem = voiceMap.filter(item => item.name === voiceName);
        if (voiceMapItem.length > 0) {
            voiceMapItem = voiceMapItem[0];
        }
        else {
            voiceMapItem = voiceMap[0];
        }
        console.log(voiceMapItem);

        const foundVoice = [];
        for (let i = 0; i < voices.length; i++) {
            if (voices[i].lang === voiceMapItem.lang) {
                foundVoice.push(voices[i]);
            }
        }

        if (foundVoice.length === 0) {
            return;
        }

        if (foundVoice.length === 1) {
            return foundVoice[0];
        }

        let voice;
        foundVoice.forEach(foundVoice => {
            if (foundVoice.default) {
                voice = foundVoice;
            }
        });
        if (voice) {
            return voice;
        }

        return foundVoice[0];
    }

    read(voices, text, onEnd) {

        const utterance = new SpeechSynthesisUtterance(text);

        for (let [key, value] of Object.entries(this.options)) {
            utterance[key] = value;
        }

        utterance.voice = this.findVoice(voices, this.voice);
        console.log(utterance.voice)

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