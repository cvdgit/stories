class AnswerModel {

    constructor(data) {
        this.data = data;
    }

    isCorrect() {
        return parseInt(this.data.is_correct) === 1;
    }
}

export default AnswerModel;