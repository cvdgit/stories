import ProgressModel from "./ProgressModel";

class TestModel {

    constructor(data) {
        this.data = data;
        this.progress = new ProgressModel(data.progress);
    }

    getId() {
        return parseInt(this.data.id);
    }

    isShowAnswerImage() {
        return Boolean(this.data.showAnswerImage);
    }

    isShowQuestionImage() {
        return Boolean(this.data.showQuestionImage);
    }

    getRepeatQuestions() {
        return parseInt(this.data.repeatQuestions);
    }

    getProgress() {
        return this.progress;
    }

    isHideQuestionName() {
        return Boolean(this.data.hideQuestionName);
    }

    isHideAnswersName() {
        return Boolean(this.data.hideAnswersName);
    }

    isAskQuestion() {
        return Boolean(this.data.askQuestion);
    }

    getAskQuestionLang() {
        return this.data.askQuestionLang;
    }

    getSource() {
        return parseInt(this.data.source);
    }
}

export default TestModel;