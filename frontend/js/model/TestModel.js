class TestModel {

    constructor(data) {
        this.data = data;
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
}

export default TestModel;