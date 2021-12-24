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
}

export default TestModel;