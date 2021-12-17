import Loader from "./components/Loader";
import BaseQuestion from "./question/BaseQuestion";

class Testing {

    dom = {};
    testQuestions = [];
    currentQuestionComp;

    /**
     *
     * @param element
     * @param options
     */
    constructor(element, options) {

        if (!(element && element.nodeType && element.nodeType === 1)) {
            throw "Element must be an HTMLElement, not ".concat({}.toString.call(element));
        }

        this.element = element;
        this.options = Object.assign({}, options);

        element['_wikids_test'] = this;

        //this.currentQuestionComp = {};
    }

    initialize(testConfig, questionsData) {

        this.testConfig = testConfig;
        this.questions = questionsData.getQuestions();

        this.load();
    }

    makeTestQuestions() {
        let end = false;
        let max = 1; //getQuestionRepeat();
        while (!end && this.testQuestions.length < max) {
            end = this.questions.length === 0;
            if (!end) {
                this.testQuestions.push(this.questions.shift());
            }
        }
    }

    load() {

        this.makeTestQuestions();
        this.setupDOM();
        this.start();
        this.addEventListeners();

        this.element.appendChild(this.dom.wrapper);
    }

    start() {

        this.showNextQuestion();
    }

    setupDOM() {

        this.dom.header = document.createElement('div');
        this.dom.header.classList.add('wikids-test-header');

        this.dom.questions = document.createElement('div');
        this.dom.questions.classList.add('wikids-test-questions');

        this.dom.controls = document.createElement('div');
        this.dom.controls.classList.add('wikids-test-controls');
        const buttonsElement = document.createElement('div');
        buttonsElement.classList.add('wikids-test-buttons');

        this.dom.nextButton = document.createElement('button');
        this.dom.nextButton.classList.add('btn');
        this.dom.nextButton.classList.add('btn-small');
        this.dom.nextButton.classList.add('btn-test');
        this.dom.nextButton.classList.add('wikids-test-next');
        this.dom.nextButton.textContent = 'Следующий вопрос';
        buttonsElement.appendChild(this.dom.nextButton);

        this.dom.controls.appendChild(buttonsElement);

        this.dom.wrapper = document.createElement('div');
        this.dom.wrapper.classList.add('wikids-test');
        this.dom.wrapper.appendChild(this.dom.header);
        this.dom.wrapper.appendChild(this.dom.questions);
        this.dom.wrapper.appendChild(this.dom.controls);
    }

    addEventListeners() {
        this.dom.nextButton.addEventListener('click', (e) => {
            this.nextQuestion();
        }, false);
    }

    nextQuestion() {
        console.log(this.currentQuestionComp.getUserAnswers());
    }

    showNextQuestion() {

        const currentQuestion = this.testQuestions.shift();
        this.dom.questions.innerHTML = '';

        const questionComp = this.createQuestion(currentQuestion);
        this.currentQuestionComp = questionComp;

        this.dom.questions.append(questionComp.render());
    }

    createQuestion(question) {

        let questionComp;
        switch (question.getType()) {
            case 0:
                questionComp = new BaseQuestion(question);
        }
        return questionComp;
    }
}

export default Testing;