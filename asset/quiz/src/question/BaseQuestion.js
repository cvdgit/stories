import Question from "./Question";
import {shuffle} from "../utils";
import QuestionStars from "../components/QuestionStars";

class BaseQuestion extends Question {

    element;

    constructor(model, options) {
        super();
        this.model = model;
        this.options = options;
        this.userAnswers = new Set();
        this.userStars = new QuestionStars(model.getStars(), options.repeatQuestions);
    }

    getQuestionModel() {
        return this.model;
    }

    createAnswer(answer) {

        const inputElement = document.createElement('input');
        let inputId = 'answer' + answer.getId();
        let singleValue = true;
        let type = 'radio';
        if (this.model.getCorrectAnswers().length > 1) {
            singleValue = false;
            type = 'checkbox';
        }
        inputElement.setAttribute('type', type);
        inputElement.setAttribute('id', inputId);
        inputElement.setAttribute('name', 'qwe');
        inputElement.setAttribute('value', answer.getId());

        const answerElement = document.createElement('div');
        answerElement.classList.add('wikids-test-answer');
        answerElement.addEventListener('click', (e) => {
            let tagName = e.target.tagName;
            let tags = ['INPUT'];
            //if (originalImageExists) {
            //    tags.push('IMG');
            //}
            let input = e.target.querySelector('input');
            if (!input) {
              input = e.target;
            }
            if (!tags.includes(tagName)) {
                input.checked = !input.checked;
            }

            if (singleValue) {
                this.userAnswers.clear();
                this.userAnswers.add(input.value);
            }
            else {
                if (input.checked) {
                    this.userAnswers.add(input.value);
                }
                else {
                    this.userAnswers.delete(input.value);
                }
            }

            this.emit('answerQuestion', this.getUserAnswers());
        }, false);
        answerElement.appendChild(inputElement);

        if (this.options.showAnswerImage && answer.haveImage()) {
            const answerImageElement = document.createElement('img');
            answerImageElement.setAttribute('src', answer.getImage());
            answerImageElement.style.height = '100px';
            answerElement.appendChild(answerImageElement);
        }

        const labelElement = document.createElement('label');
        labelElement.setAttribute('for', inputId);
        if (!this.options.hideAnswersName) {
            labelElement.textContent = answer.getName();
        }
        answerElement.appendChild(labelElement);

        return answerElement;
    }

    renderAnswers(answers) {

        const answersElement = document.createElement('div');
        answersElement.classList.add('.wikids-test-answers');

        if (this.model.isMixAnswers()) {
            answers = shuffle(answers);
        }

        answers.forEach((answer) => {
            answersElement.appendChild(this.createAnswer(answer));
        });

        return answersElement;
    }

    createQuestionName() {
        let questionName = this.model.getName();
        if (this.model.getCorrectNumber() > 1) {
            questionName += ' (верных ответов: ' + this.model.getCorrectNumber() + ')';
        }
        return questionName;
    }

    renderTitle() {
        const titleElement = document.createElement('p');
        titleElement.classList.add('question-title');
        titleElement.innerHTML = this.options.hideQuestionName ? '' : this.createQuestionName();
        return titleElement;
    }

    render() {

        const preTitleElement = document.createElement('p');
        preTitleElement.classList.add('pre-question-title');
        preTitleElement.textContent = this.options.hideQuestionName ? 'Прослушайте вопрос' : 'Ответьте на вопрос:';

        const questionElement = document.createElement('div');
        this.element = questionElement;
        questionElement.classList.add('wikids-test-question');
        questionElement.classList.add('wikids-test-active-question');
        questionElement.setAttribute('data-question-id', this.model.getId());

        questionElement.appendChild(this.userStars.render());
        questionElement.appendChild(preTitleElement);
        questionElement.appendChild(this.renderTitle());

        const mainElement = document.createElement('div');
        mainElement.classList.add('row');
        mainElement.classList.add('row-no-gutters');
        mainElement.innerHTML =
            `<div class="col-xs-12 col-sm-4 col-md-4 question-image"></div>
             <div class="col-xs-12 col-sm-8 col-md-8 question-wrapper"></div>`;

        questionElement.appendChild(mainElement);

        if (this.options.showQuestionImage && this.model.haveImage()) {
            const questionImageElement = document.createElement('img');
            questionImageElement.setAttribute('src', this.model.getImage());
            questionImageElement.style.width = '100%';
            questionElement.querySelector('.question-image').classList.add('thumbnail');
            questionElement.querySelector('.question-image').appendChild(questionImageElement);
        }

        const preAnswersElement = document.createElement('p');
        preAnswersElement.classList.add('pre-question-title');
        preAnswersElement.textContent = 'Варианты ответов:';
        questionElement.querySelector('.question-wrapper').appendChild(preAnswersElement);
        questionElement.querySelector('.question-wrapper').appendChild(this.renderAnswers(this.model.getAnswers()));

        return questionElement;
    }

    getUserAnswers() {
        return Array.from(this.userAnswers);
    }

    hideQuestion() {
        this.element.style.display = 'none';
        this.element.classList.remove('wikids-test-active-question');
    }

    checkAnswerCorrect() {
        const userAnswers = this.getUserAnswers();
        const correctAnswers = this.model.getCorrectAnswers().map((item) => item.getId());
        return userAnswers.length === correctAnswers.length && userAnswers.sort()
            .every((val, index) => parseInt(val) === correctAnswers.sort()[index]);
    }

    isShowNextButton() {
        return false;
    }

    getCorrectAnswers() {
        return this.model.getCorrectAnswers();
    }

    incStars() {
        this.userStars.inc();
    }

    decStars() {
        return this.userStars.dec();
    }

    isDoneStars() {
        return this.userStars.isDone();
    }

    getCurrentStars() {
        return this.userStars.getCurrent();
    }
}

export default BaseQuestion;
