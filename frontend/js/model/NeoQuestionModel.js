import QuestionModel from "./QuestionModel";

export default class NeoQuestionModel extends QuestionModel {

    getAnswerNumber() {
        return parseInt(this.data.answer_number);
    }

    getTopicId() {
        return parseInt(this.data.topic_id);
    }
}