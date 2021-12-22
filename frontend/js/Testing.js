import Loader from "./components/Loader";
import BaseQuestion from "./question/BaseQuestion";
import DefaultWrongAnswer from "./components/DefaultWrongAnswer";

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

        this.element.innerHTML = '';
        const loader = new Loader();
        this.element.innerHTML = loader.render().outerHTML;
    }

    initialize(testConfig, questionsData) {
        this.testConfig = testConfig;
        this.questions = questionsData.getQuestions();
        this.load();
    }

    makeTestQuestions() {
        let end = false;
        let max = this.testConfig.getRepeatQuestions();
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
        this.addEventListeners();
        this.start();

        this.element.innerHTML = '';
        this.element.appendChild(this.dom.wrapper);
    }

    start() {
        this.showNextQuestion();
    }

    finish() {

        this.currentQuestionComp.hideQuestion();
        this.nextButtonVisible(true);

        this.dom.results.style.display = 'block';
        this.dom.results.innerHTML = '<h2>Тест пройден</h2>';
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
        this.dom.nextButton.style.display = 'none';
        this.dom.nextButton.classList.add('btn');
        this.dom.nextButton.classList.add('btn-small');
        this.dom.nextButton.classList.add('btn-test');
        this.dom.nextButton.classList.add('wikids-test-next');
        this.dom.nextButton.textContent = 'Следующий вопрос';
        buttonsElement.appendChild(this.dom.nextButton);

        this.dom.controls.appendChild(buttonsElement);

        this.dom.results = document.createElement('div');
        this.dom.results.classList.add('wikids-test-results');
        this.dom.results.style.display = 'none';

        this.dom.wrapper = document.createElement('div');
        this.dom.wrapper.classList.add('wikids-test');
        this.dom.wrapper.appendChild(this.dom.header);
        this.dom.wrapper.appendChild(this.dom.questions);
        this.dom.wrapper.appendChild(this.dom.results);
        this.dom.wrapper.appendChild(this.dom.controls);
    }

    addEventListeners() {
        this.dom.nextButton.addEventListener('click', (e) => {
            this.nextQuestion();
        }, false);
    }

    checkAnswerCorrect() {
        return this.currentQuestionComp.checkAnswerCorrect();
    }

    nextButtonVisible(forceHide) {
        forceHide = forceHide || false;
        let display = 'none';
        if (!forceHide) {
            display = this.currentQuestionComp.isShowNextButton() ? 'inline-block' : 'none';
        }
        this.dom.nextButton.style.display = display;
    }

    nextQuestion() {

        const userAnswers = this.currentQuestionComp.getUserAnswers();
        if (userAnswers.length === 0) {
            return;
        }

        const currentQuestionModel = this.currentQuestionComp.getQuestionModel();
        const answerIsCorrect = this.checkAnswerCorrect();

        if (answerIsCorrect) {
            if (currentQuestionModel.lastAnswerIsCorrect()) {
                this.currentQuestionComp.incStars();
            }
            else {
                currentQuestionModel.setLastAnswersIsCorrect(true);
            }
        }
        else {
            currentQuestionModel.setLastAnswersIsCorrect(false);
            this.currentQuestionComp.decStars();
        }

        let done = false;
        if (!answerIsCorrect) {
            this.testQuestions.unshift(currentQuestionModel);
        }
        else {
            done = this.currentQuestionComp.isDoneStars();
            if (done) {
                this.makeTestQuestions();
            }
            else {
                this.testQuestions.push(currentQuestionModel);
            }
        }

        this.currentQuestionComp.hideQuestion();
        this.nextButtonVisible();

        if (!answerIsCorrect) {
            this.dom.results.style.display = 'block';
            this.dom.results.innerHTML = '<p class="incorrect-text">Вы ответили неправильно</p>';

            const wrongAnswer = new DefaultWrongAnswer(this.currentQuestionComp);
            wrongAnswer.on('wrongAnswerNext', () => {
                this.continueTestAction(answerIsCorrect);
            });
            this.dom.results.appendChild(wrongAnswer.render());
        }
        else {
            this.continueTestAction(answerIsCorrect);
        }
    }

    continueTestAction(answerIsCorrect) {
        console.log('continueTestAction');

        const isLastQuestion = (this.testQuestions.length === 0);
        this.dom.results.style.display = 'none';

        if (isLastQuestion) {
            if (answerIsCorrect) {
                this.finish();
            }
            else {
                this.showNextQuestion();
                this.nextButtonVisible();
            }
        }
        else {
            this.showNextQuestion();
            this.nextButtonVisible();
        }
    }

    showNextQuestion() {
        console.log('showNextQuestion');

        const currentQuestion = this.testQuestions.shift();
        if (currentQuestion === undefined) {
            return;
        }

        const questionComp = this.createQuestion(currentQuestion);
        this.currentQuestionComp = questionComp;

        this.dom.questions.innerHTML = '';
        this.dom.questions.append(questionComp.render());
    }

    createQuestion(question) {

        let questionComp;
        const options = {
            'showQuestionImage': this.testConfig.isShowQuestionImage(),
            'showAnswerImage': this.testConfig.isShowAnswerImage(),
            'repeatQuestions': this.testConfig.getRepeatQuestions()
        }
        switch (question.getType()) {
            case 0:
            case 1:
                questionComp = new BaseQuestion(question, options);
        }

        const correctAnswers = question.getCorrectAnswers();
        questionComp.on('answerQuestion', (userAnswers) => {
            if (userAnswers.length === correctAnswers.length) {
                this.nextQuestion();
            }
        });

        console.log('stars', questionComp.getCurrentStars());

        return questionComp;
    }
}

export default Testing;