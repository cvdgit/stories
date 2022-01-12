import BaseQuestion from "./BaseQuestion";

export default class AskQuestion extends BaseQuestion {

    onShowQuestion() {
        const element = this.element.querySelector('.ask-question');
        const i = element.querySelector('i');
        this.speakQuestion(i);
    }

    onHideQuestion() {
        this.speech.cancel();
    }

    setSpeech(speech) {
        this.speech = speech;
    }

    speakQuestion(element) {
        if (element.dataset.processing === '1') {
            return false;
        }
        element.dataset.processing = '1';
        element.classList.replace('glyphicon-volume-up', 'glyphicon-option-horizontal');
        this.speech.readText(this.model.getName(), () => {
            element.classList.replace('glyphicon-option-horizontal', 'glyphicon-volume-up');
            element.dataset.processing = null;
        });
    }

    renderReadQuestion() {
        const element = document.createElement('div');
        element.classList.add('ask-question');
        element.title = 'Прослушать';
        element.innerHTML = '<i class="glyphicon glyphicon-volume-up"></i>';
        element.addEventListener('click', (e) => {
            const i = e.target.querySelector('i');
            this.speakQuestion(i);
        });
        return element;
    }

    renderTitle() {
        const titleElement = document.createElement('p');
        titleElement.classList.add('question-title');
        if (this.options.hideQuestionName) {
            titleElement.innerHTML = '';
        }
        else {
            titleElement.innerHTML = this.createQuestionName();
        }
        if (this.options.askQuestion) {
            titleElement.appendChild(this.renderReadQuestion());
        }
        return titleElement;
    }
}