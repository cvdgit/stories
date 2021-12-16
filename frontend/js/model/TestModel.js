import QuestionModel from "./QuestionModel";

class TestModel {

    constructor(data, questionsData, answersPropName) {

        this.id = parseInt(data.id);

        this.questions = questionsData.map(question => new QuestionModel(question, answersPropName));
    }

    getId() {
        return this.id;
    }

    getQuestions() {
        return this.questions;
    }
}

export default TestModel;