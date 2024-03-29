import {objectToQueryString} from "../utils";

export default class HistoryModel {

    constructor() {
        this.data = {
            answers: []
        };
    }

    #setDataValue(key, value) {
        this.data[key] = value;
    }

    setSource(value) {
        if (!Number.isInteger(value)) {
            throw 'HistoryModel.source value is not integer';
        }
        this.#setDataValue('source', value);
        return this;
    }

    setTestId(value) {
        if (!Number.isInteger(value)) {
            throw 'HistoryModel.test_id value is not integer';
        }
        this.#setDataValue('test_id', value);
        return this;
    }

    setStudentId(value) {
        if (!Number.isInteger(value)) {
            throw 'HistoryModel.student_id value is not integer';
        }
        this.#setDataValue('student_id', value);
        return this;
    }

    setEntityId(value) {
        if (!Number.isInteger(value)) {
            throw 'HistoryModel.entity_id value is not integer';
        }
        this.#setDataValue('entity_id', value);
        return this;
    }

    setEntityName(value) {
        this.#setDataValue('entity_name', value);
        return this;
    }

    setCorrectAnswer(value) {
        this.#setDataValue('correct_answer', value ? 1 : 0);
        return this;
    }

    setProgress(value) {
        if (!Number.isInteger(value)) {
            throw 'HistoryModel.progress value is not integer';
        }
        this.#setDataValue('progress', value);
        return this;
    }

    setStars(value) {
        if (!Number.isInteger(value)) {
            throw 'HistoryModel.stars value is not integer';
        }
        this.#setDataValue('stars', value);
        return this;
    }

    addAnswer(id, name) {
        if (!Number.isInteger(id)) {
            throw 'HistoryModel.answers.answer_entity_id value is not integer';
        }
        if (typeof name === 'undefined' && !name) {
            throw 'HistoryModel.answers.answer_entity_name value is empty';
        }
        this.data.answers.push({
            'answer_entity_id': id,
            'answer_entity_name': name
        });
    }

    getData() {
        return this.data;
    }

    asQueryString() {
        return objectToQueryString(this.data);
    }

    setQuestionTopicId(value) {
        if (!Number.isInteger(value)) {
            throw 'HistoryModel.question_topic_id value is not integer';
        }
        this.#setDataValue('question_topic_id', value);
        return this;
    }

    setQuestionTopicName(value) {
        this.#setDataValue('question_topic_name', value);
        return this;
    }

    setRelationId(value) {
        if (!Number.isInteger(value)) {
            throw 'HistoryModel.relation_id value is not integer';
        }
        this.#setDataValue('relation_id', value);
        return this;
    }

    setRelationName(value) {
        this.#setDataValue('relation_name', value);
        return this;
    }
}