import AnswerModel from "./AnswerModel";

class QuestionModel {

    constructor(data, answersPropName) {
        this.data = data;
        this.answers = data[answersPropName].map(answer => new AnswerModel(answer));
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

export default QuestionModel;