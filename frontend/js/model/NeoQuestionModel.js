import QuestionModel from "./QuestionModel";

export default class NeoQuestionModel extends QuestionModel {

    getAnswerNumber() {
        return parseInt(this.data.answer_number);
    }

    getTopicId() {
        return parseInt(this.data.topic_id);
    }

    getRelationId() {
        return parseInt(this.data.relation_id);
    }

    getRelationName() {
        return this.data.relation_name;
    }
}