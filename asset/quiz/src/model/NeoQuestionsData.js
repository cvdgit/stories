import NeoQuestionModel from "./NeoQuestionModel";

export default class NeoQuestionsData {

    constructor(data, answersPropName) {
        this.data = data;
        this.questions = data.map(question => new NeoQuestionModel(question, answersPropName));
    }

    getQuestions() {
        return this.questions;
    }
}