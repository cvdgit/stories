import QuestionModel from "./QuestionModel";

class QuestionsData {

    constructor(data, answersPropName) {
        this.data = data;
        this.questions = data.map(question => new QuestionModel(question, answersPropName));
    }

    getQuestions() {
        return this.questions;
    }
}

export default QuestionsData;