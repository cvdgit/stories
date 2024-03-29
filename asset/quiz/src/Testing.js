import Loader from "./components/Loader";
import BaseQuestion from "./question/BaseQuestion";
import DefaultWrongAnswer from "./components/DefaultWrongAnswer";
import TestProgress from "./components/TestProgress";
import ErrorPage from "./components/ErrorPage";
import WelcomePage from "./components/WelcomePage";
import TestSpeech from "./components/TestSpeech";
import AskQuestion from "./question/AskQuestion";
import HistoryModel from "./model/HistoryModel";
import WelcomeGuestPage from "./components/WelcomeGuestPage";
import {isMobileDevice} from "./utils";
import NeoBaseQuestion from "./question/NeoBaseQuestion";

export default class Testing {

    dom = {};
    testQuestions = [];
    currentQuestionComp;

    speechSynth;

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

        this.numQuestions = 0;
        this.isMobile = isMobileDevice();

        element['_wikids_test'] = this;

        this.renderLoader();

        options['welcomeGuest'] = options['welcomeGuest'] || false;

        if (options.welcomeGuest) {
            this.welcomeGuest();
        }
        else {
            if (options['welcome'] && typeof options['welcome'] === 'function') {
                options.welcome(this.welcome.bind(this), this.error.bind(this));
            }
        }
    }

    error(params) {
        console.log(params);
        this.element.innerHTML = new ErrorPage().render().outerHTML;
    }

    renderLoader() {
        this.element.innerHTML = new Loader().render().outerHTML;
    }

    /**
     *
     * @param {WelcomeModel} model
     * @param {?Number} studentId
     */
    welcome(model, studentId) {
        console.log('welcome');

        this.element.innerHTML = '';

        const welcomePage = new WelcomePage(model);
        if (welcomePage.setActiveStudent(studentId)) {
            this.student = welcomePage.getActiveStudent();
            if (this.student && this.student.isDone()) {
                this.student = null;
            }
            if (this.student) {

                const welcomeGuestPage = new WelcomeGuestPage();
                this.element.appendChild(welcomeGuestPage.render(() => {
                    this.renderLoader();
                    this.options.initialize(this.initialize.bind(this), this.error.bind(this), this.student.getId());
                }));

                //this.renderLoader();
                //this.options.initialize(this.initialize.bind(this), this.error.bind(this), this.student.getId());
            }
        }
        if (!this.student) {
            this.element.appendChild(welcomePage.render((activeStudent) => {
                this.student = activeStudent;
                this.renderLoader();
                this.options.initialize(this.initialize.bind(this), this.error.bind(this), activeStudent.getId());
            }));
        }
    }

    welcomeGuest() {
        console.log('welcomeGuest');

        this.element.innerHTML = '';

        const welcomeGuestPage = new WelcomeGuestPage();
        this.element.appendChild(welcomeGuestPage.render(() => {
            this.renderLoader();
            this.options.initialize(this.initialize.bind(this), this.error.bind(this));
        }));
    }

    /**
     *
     * @param {TestModel} testConfig
     * @param {QuestionsData} questionsData
     */
    initialize(testConfig, questionsData) {
        console.log('initialize');
        this.testConfig = testConfig;
        this.questions = questionsData.getQuestions();
        this.numQuestions = this.questions.length;
        this.load();
    }

    makeTestQuestions() {
        //console.log('makeTestQuestions');
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

        if (this.isMobile && this.options.welcomeGuest) {

            const backdropElement = document.createElement('div');
            backdropElement.classList.add('modal-backdrop');
            backdropElement.classList.add('fade');
            backdropElement.classList.add('in');
            document.body.appendChild(backdropElement);

            const parentNode = this.dom.wrapper.parentNode;
            parentNode.classList.add('mobile-testing');
            parentNode.addEventListener('click', (e) => {
                if (e.target === e.currentTarget || e.target.closest('button.close')) {
                    backdropElement.remove();
                    parentNode.classList.remove('mobile-testing');
                    this.welcomeGuest();
                }
            });
        }
    }

    start() {

        if (this.numQuestions === 0) {
            this.dom.results.style.display = 'block';
            this.dom.results.innerHTML = '<h2>В тесте нет вопросов</h2>';
            return;
        }

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

        if (this.isMobile && this.options.welcomeGuest) {
            const mobileHeader = document.createElement('div');
            mobileHeader.classList.add('clearfix');
            mobileHeader.innerHTML = `<div style="height:50px"><button class="close"><span>×</span></button></div>`;
            this.dom.header.appendChild(mobileHeader);
        }

        if (this.student) {
            const studentElement = document.createElement('div');
            studentElement.classList.add('wikids-test-student-info');
            studentElement.textContent = this.student.getName();
            this.dom.header.appendChild(studentElement);
        }

        this.testProgress = new TestProgress(this.testConfig.getProgress());
        this.dom.header.appendChild(this.testProgress.render());

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

        this.currentQuestionComp.onHideQuestion();

        const currentQuestionModel = this.currentQuestionComp.getQuestionModel();
        const answerIsCorrect = this.checkAnswerCorrect();

        if (answerIsCorrect) {
            if (currentQuestionModel.lastAnswerIsCorrect()) {
                this.currentQuestionComp.incStars();
                this.testProgress.inc();
            }
            else {
                currentQuestionModel.setLastAnswersIsCorrect(true);
                if (this.testConfig.getRepeatQuestions() === 1) {
                    this.currentQuestionComp.incStars();
                    this.testProgress.inc();
                }
            }
        }
        else {
            currentQuestionModel.setLastAnswersIsCorrect(false);
            let dec = this.currentQuestionComp.decStars();
            if (dec) {
                this.testProgress.dec();
            }
        }

        this.testProgress.updateProgress();

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

        if (this.options['history'] && typeof this.options['history'] === 'function') {
            const historyModel = new HistoryModel();
            historyModel
                .setSource(this.testConfig.getSource())
                .setTestId(this.testConfig.getId())
                .setStudentId(this.student.getId())
                .setEntityId(currentQuestionModel.getId())
                .setEntityName(currentQuestionModel.getName())
                .setCorrectAnswer(answerIsCorrect)
                .setProgress(this.testProgress.calcPercent())
                .setStars(this.currentQuestionComp.getCurrentStars());
            if (this.testConfig.sourceIsNeo()) {
                historyModel
                    .setQuestionTopicId(currentQuestionModel.getTopicId())
                    .setQuestionTopicName(currentQuestionModel.getName())
                    .setRelationId(currentQuestionModel.getRelationId())
                    .setRelationName(currentQuestionModel.getRelationName());
            }
            userAnswers.map(answerId => {
                const answer = currentQuestionModel.getAnswerById(parseInt(answerId));
                historyModel.addAnswer(answer.getId(), answer.getName());
            });
            this.options.history(historyModel);
        }

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

        questionComp.onShowQuestion();
    }

    getSpeechSynth() {
        if (!this.speechSynth) {
            this.speechSynth = new TestSpeech();
        }
        return this.speechSynth;
    }

    createQuestion(question) {

        let questionComp;
        const options = {
            'showQuestionImage': this.testConfig.isShowQuestionImage(),
            'showAnswerImage': this.testConfig.isShowAnswerImage(),
            'repeatQuestions': this.testConfig.getRepeatQuestions(),
            'hideQuestionName': this.testConfig.isHideQuestionName(),
            'hideAnswersName': this.testConfig.isHideAnswersName(),
            'askQuestion': this.testConfig.isAskQuestion(),
            'askQuestionLang': this.testConfig.getAskQuestionLang()
        }
        switch (question.getType()) {
            case 0:
            case 1:
                if (this.testConfig.sourceIsNeo()) {
                    questionComp = new NeoBaseQuestion(question, options);
                }
                else {
                    if (this.testConfig.isAskQuestion()) {
                        questionComp = new AskQuestion(question, options);
                        const speech = this.getSpeechSynth();
                        speech.setVoice(this.testConfig.getAskQuestionLang());
                        questionComp.setSpeech(speech);
                    } else {
                        questionComp = new BaseQuestion(question, options);
                    }
                }
                break;
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