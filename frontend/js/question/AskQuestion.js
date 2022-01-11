import BaseQuestion from "./BaseQuestion";

export default class AskQuestion extends BaseQuestion {

    onShowQuestion() {
        const event = new MouseEvent('click');
        this.element.querySelector('.ask-question').dispatchEvent(event);
    }

    onHideQuestion() {
        this.speech.cancel();
    }

    setSpeech(speech) {
        this.speech = speech;
    }

    renderReadQuestion() {
        const element = document.createElement('div');
        element.classList.add('ask-question');
        element.title = 'Прослушать';
        element.innerHTML = '<i class="glyphicon glyphicon-volume-up"></i>';
        element.addEventListener('click', (e) => {
            const i = e.target.querySelector('i');
            if (i.dataset.processing === '1') {
                return false;
            }
            i.dataset.processing = '1';
            i.classList.replace('glyphicon-volume-up', 'glyphicon-option-horizontal');
            this.speech.readText(this.model.getName(), () => {
                i.classList.replace('glyphicon-option-horizontal', 'glyphicon-volume-up');
                i.dataset.processing = null;
            });
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