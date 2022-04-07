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
            {name: 'Google русский', lang: 'ru-RU', lang2: 'ru_RU'},
            {name: 'Microsoft Irina Desktop - Russian', lang: 'ru-RU', lang2: 'ru_RU'},
            {name: 'Google US English', lang: 'en-US', lang2: 'en_US'},
            {name: 'Microsoft Zira Desktop - English (United States)', lang: 'en-US', lang2: 'en_US'},
            {name: 'Microsoft David Desktop - English (United States)', lang: 'en-US', lang2: 'en_US'}
        ];

        let voiceMapItem = voiceMap.filter(item => item.name === voiceName);
        if (voiceMapItem.length > 0) {
            voiceMapItem = voiceMapItem[0];
        }
        else {
            voiceMapItem = voiceMap[0];
        }
        console.log(voiceMapItem);
        console.log(voices);

        const foundVoices = [];
        for (let i = 0; i < voices.length; i++) {
            if (voices[i].lang === voiceMapItem.lang || voices[i].lang === voiceMapItem.lang2) {
                foundVoices.push(voices[i]);
            }
        }

        console.log(foundVoices);

        if (foundVoices.length === 0) {
            return;
        }

        if (foundVoices.length === 1) {
            return foundVoices[0];
        }

        let voice;
        foundVoices.forEach(foundVoice => {
            if (foundVoice.default) {
                voice = foundVoice;
            }
        });
        if (voice) {
            return voice;
        }

        return foundVoices[0];
    }

    read(voices, text, onEnd) {

        const utterance = new SpeechSynthesisUtterance(text);

        for (let [key, value] of Object.entries(this.options)) {
            utterance[key] = value;
        }

        const voice = this.findVoice(voices, this.voice);
        utterance.voice = voice;
        utterance.lang = voice.lang;
        utterance.voiceURI = voice.voiceURI;
        console.log(utterance.voice);

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