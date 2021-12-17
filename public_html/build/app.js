/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./frontend/js/Testing.js":
/*!********************************!*\
  !*** ./frontend/js/Testing.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _components_Loader__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/Loader */ "./frontend/js/components/Loader.js");
/* harmony import */ var _question_BaseQuestion__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./question/BaseQuestion */ "./frontend/js/question/BaseQuestion.js");



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
                questionComp = new _question_BaseQuestion__WEBPACK_IMPORTED_MODULE_1__["default"](question);
        }
        return questionComp;
    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Testing);

/***/ }),

/***/ "./frontend/js/components/Loader.js":
/*!******************************************!*\
  !*** ./frontend/js/components/Loader.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
class Loader {

    constructor(text) {
        this.text = text || 'Загрузка вопросов';
    }

    render() {
        let elem = document.createElement('div');
        elem.classList.add('wikids-test-loader');
        let textElem = document.createElement('p');
        textElem.textContent = this.text;
        elem.appendChild(textElem);
        let imgElem = document.createElement('img');
        imgElem.setAttribute('src', '/img/loading.gif');
        elem.appendChild(imgElem);
        return elem;
    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Loader);

/***/ }),

/***/ "./frontend/js/model/AnswerModel.js":
/*!******************************************!*\
  !*** ./frontend/js/model/AnswerModel.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
class AnswerModel {

    constructor(data) {
        this.data = data;
    }

    getId() {
        return parseInt(this.data.id);
    }

    getName() {
        return this.data.name;
    }

    isCorrect() {
        return parseInt(this.data.is_correct) === 1;
    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (AnswerModel);

/***/ }),

/***/ "./frontend/js/model/QuestionModel.js":
/*!********************************************!*\
  !*** ./frontend/js/model/QuestionModel.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _AnswerModel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AnswerModel */ "./frontend/js/model/AnswerModel.js");


class QuestionModel {

    constructor(data, answersPropName) {
        this.data = data;
        this.answers = data[answersPropName].map(answer => new _AnswerModel__WEBPACK_IMPORTED_MODULE_0__["default"](answer));
    }

    getId() {
        return parseInt(this.data.id);
    }

    getName() {
        return this.data.name;
    }

    getType() {
        return parseInt(this.data.type);
    }

    getAnswers() {
        return this.answers;
    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (QuestionModel);

/***/ }),

/***/ "./frontend/js/model/QuestionsData.js":
/*!********************************************!*\
  !*** ./frontend/js/model/QuestionsData.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _QuestionModel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./QuestionModel */ "./frontend/js/model/QuestionModel.js");


class QuestionsData {

    constructor(data, answersPropName) {
        this.data = data;
        this.questions = data.map(question => new _QuestionModel__WEBPACK_IMPORTED_MODULE_0__["default"](question, answersPropName));
    }

    getQuestions() {
        return this.questions;
    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (QuestionsData);

/***/ }),

/***/ "./frontend/js/model/TestModel.js":
/*!****************************************!*\
  !*** ./frontend/js/model/TestModel.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
class TestModel {

    constructor(data) {
        this.data = data;
    }

    getId() {
        return parseInt(this.data.id);
    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (TestModel);

/***/ }),

/***/ "./frontend/js/question/BaseQuestion.js":
/*!**********************************************!*\
  !*** ./frontend/js/question/BaseQuestion.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
class BaseQuestion {

    constructor(model) {
        this.model = model;
        this.userAnswers = new Set();
    }

    createAnswer(answer) {

        const inputElement = document.createElement('input');
        let inputId = 'answer' + answer.getId();
        const singleValue = false;
        inputElement.setAttribute('type', 'checkbox');
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
        }, false);
        answerElement.appendChild(inputElement);

        const labelElement = document.createElement('label');
        labelElement.setAttribute('for', inputId);
        labelElement.textContent = answer.getName();
        answerElement.appendChild(labelElement);

        return answerElement;
    }

    renderAnswers(answers) {

        const mainElement = document.createElement('div');
        mainElement.innerHTML =
            `<div class="row row-no-gutters">
                 <div class="col-md-4 question-image"></div>
                 <div class="col-md-8 question-wrapper">
                     <div class="wikids-test-answers"></div>
                 </div>
             </div>`;

        const answersElement = mainElement.querySelector('.wikids-test-answers');
        answers.forEach((answer) => {
            answersElement.appendChild(this.createAnswer(answer));
        });

        return mainElement;
    }

    render() {

        const titleElement = document.createElement('p');
        titleElement.classList.add('question-title');
        titleElement.textContent = this.model.getName();

        const questionElement = document.createElement('div');
        questionElement.classList.add('wikids-test-question');
        questionElement.setAttribute('data-question-id', this.model.getId());
        questionElement.appendChild(titleElement);

        questionElement.appendChild(this.renderAnswers(this.model.getAnswers()));

        return questionElement;
    }

    getUserAnswers() {
        return Array.from(this.userAnswers);
    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (BaseQuestion);

/***/ }),

/***/ "./frontend/scss/style.scss":
/*!**********************************!*\
  !*** ./frontend/scss/style.scss ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./frontend/js/testing-data.json":
/*!***************************************!*\
  !*** ./frontend/js/testing-data.json ***!
  \***************************************/
/***/ ((module) => {

module.exports = JSON.parse('[{"storyTestQuestions":[{"stars":{"total":1,"current":0},"view":"default","type":0,"mix_answers":1,"id":3578,"name":"1","image":"","orig_image":"","original_image":false,"correct_number":1,"storyTestAnswers":[{"id":17778,"name":"1","is_correct":1,"description":null,"region_id":"","image":"/test_images/thumb_3WDkR5mBuM-cu0ozfImxxO2AlWi908K1.jpg","orig_image":"/test_images/3WDkR5mBuM-cu0ozfImxxO2AlWi908K1.jpg","original_image":true,"order":0},{"id":17779,"name":"2","is_correct":0,"description":null,"region_id":"","image":"","orig_image":"","original_image":false,"order":0},{"id":17780,"name":"3","is_correct":0,"description":null,"region_id":"","image":"","orig_image":"","original_image":false,"order":0}],"lastAnswerIsCorrect":true,"haveSlides":false,"hint":null},{"stars":{"total":1,"current":0},"view":"default","type":0,"mix_answers":1,"id":3579,"name":"Вопрос номер 2","image":"","orig_image":"","original_image":false,"correct_number":1,"storyTestAnswers":[{"id":17781,"name":"222","is_correct":1,"description":null,"region_id":"","image":"","orig_image":"","original_image":false,"order":0},{"id":17782,"name":"333","is_correct":0,"description":null,"region_id":"","image":"","orig_image":"","original_image":false,"order":0},{"id":17783,"name":"444","is_correct":0,"description":null,"region_id":"","image":"","orig_image":"","original_image":false,"order":0}],"lastAnswerIsCorrect":true,"haveSlides":false,"hint":null}],"test":{"id":144,"progress":{"total":2,"current":0},"showAnswerImage":true,"showAnswerText":true,"showQuestionImage":true,"source":1,"answerType":0,"strictAnswer":0,"inputVoice":null,"recordingLang":null,"rememberAnswers":false,"askQuestion":false,"askQuestionLang":"Google русский","hideQuestionName":false},"students":[{"id":3,"name":"Алексей","progress":100},{"id":4,"name":"Дарья","progress":90},{"id":5,"name":"Александр Карачёв","progress":100},{"id":15,"name":"alex","progress":100},{"id":20,"name":"7887788","progress":0}],"stories":[]}]');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!****************************!*\
  !*** ./frontend/js/app.js ***!
  \****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _scss_style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../scss/style.scss */ "./frontend/scss/style.scss");
/* harmony import */ var _Testing__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Testing */ "./frontend/js/Testing.js");
/* harmony import */ var _model_TestModel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./model/TestModel */ "./frontend/js/model/TestModel.js");
/* harmony import */ var _model_QuestionsData__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./model/QuestionsData */ "./frontend/js/model/QuestionsData.js");





const testing = new _Testing__WEBPACK_IMPORTED_MODULE_1__["default"](document.getElementById('mobile-testing'), {});

const data = __webpack_require__(/*! ./testing-data.json */ "./frontend/js/testing-data.json");
const testConfig = new _model_TestModel__WEBPACK_IMPORTED_MODULE_2__["default"](data[0]['test']);
const questionsData = new _model_QuestionsData__WEBPACK_IMPORTED_MODULE_3__["default"](data[0]['storyTestQuestions'], 'storyTestAnswers');
testing.initialize(testConfig, questionsData);
})();

/******/ })()
;
//# sourceMappingURL=app.js.map