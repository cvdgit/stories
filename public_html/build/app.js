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
class Testing {

    /**
     *
     * @param element
     * @param data
     * @param options
     */
    constructor(element, data, options) {

        if (!(element && element.nodeType && element.nodeType === 1)) {
            throw "Element must be an HTMLElement, not ".concat({}.toString.call(element));
        }

        this.element = element;
        this.options = options = Object.assign({}, options);

        element['_wikids_test'] = this;

        this.testConfig = data.getTestModel();
    }

    setQuestions(questions) {
        // new Question(question)
    }

    start() {

        this.setupDOM();
    }

    setupDOM() {

    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Testing);

/***/ }),

/***/ "./frontend/js/TestingData.js":
/*!************************************!*\
  !*** ./frontend/js/TestingData.js ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _model_TestModel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./model/TestModel */ "./frontend/js/model/TestModel.js");


class TestingData {

    /**
     *
     * @param data
     * @param options
     * @param options.testPropName
     * @param options.questionsPropName
     * @param options.answersPropName
     */
    constructor(data, options) {
        this.testModel = new _model_TestModel__WEBPACK_IMPORTED_MODULE_0__["default"](data[options.testPropName], data[options.questionsPropName], options.answersPropName);
    }

    getTestModel() {
        return this.testModel;
    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (TestingData);

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
        this.id = parseInt(data.id);
        this.name = data.name;
        this.answers = data[answersPropName].map(answer => new _AnswerModel__WEBPACK_IMPORTED_MODULE_0__["default"](answer));
    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (QuestionModel);

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
/* harmony import */ var _QuestionModel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./QuestionModel */ "./frontend/js/model/QuestionModel.js");


class TestModel {

    constructor(data, questionsData, answersPropName) {

        this.id = parseInt(data.id);

        this.questions = questionsData.map(question => new _QuestionModel__WEBPACK_IMPORTED_MODULE_0__["default"](question, answersPropName));
    }

    getId() {
        return this.id;
    }

    getQuestions() {
        return this.questions;
    }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (TestModel);

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
/* harmony import */ var _TestingData__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TestingData */ "./frontend/js/TestingData.js");
/* harmony import */ var _Testing__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Testing */ "./frontend/js/Testing.js");



const data = __webpack_require__(/*! ./testing-data.json */ "./frontend/js/testing-data.json");
const testingData = new _TestingData__WEBPACK_IMPORTED_MODULE_0__["default"](data[0], {
    testPropName: 'test',
    questionsPropName: 'storyTestQuestions',
    answersPropName: 'storyTestAnswers'
});

const testing = new _Testing__WEBPACK_IMPORTED_MODULE_1__["default"](document.getElementById('mobile-testing'), testingData, {});

})();

/******/ })()
;
//# sourceMappingURL=app.js.map