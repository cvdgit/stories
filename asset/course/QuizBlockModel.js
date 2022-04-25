import BlockModel from "./BlockModel";

export default class QuizBlockModel extends BlockModel {

  constructor(data) {
    super(data);
    this.quiz_id = data.quiz_id;
    this.block_id = data.block_id;
    this.quiz_name = data.quiz_name || 'Тест';
  }

  getQuizId() {
    return this.quiz_id;
  }

  getQuizBlockId() {
    return this.block_id;
  }

  getQuizName() {
    return this.quiz_name;
  }

  setQuizId(id) {
    if (!Number.isInteger(id)) {
      throw 'QuizBlockModel.setQuizId error ' + id;
    }
    this.quiz_id = id;
  }

  setQuizName(name) {
    this.quiz_name = name;
  }

  setQuizBlockId(id) {
    this.block_id = id;
  }
}
