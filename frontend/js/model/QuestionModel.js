import AnswerModel from "./AnswerModel";

class QuestionModel {

    constructor(data, answersPropName) {
        this.id = parseInt(data.id);
        this.name = data.name;
        this.answers = data[answersPropName].map(answer => new AnswerModel(answer));
    }
}

export default QuestionModel;