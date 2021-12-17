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

export default AnswerModel;