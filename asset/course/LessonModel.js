export default class LessonModel {

  constructor(data) {
    this.id = data.id;
    this.uuid = data.uuid;
    this.name = data.name || 'Новый урок';
    this.order = data.order || 1;
    this.type = data.type;
    this.blocks = [];
  }

  getType() {
    return this.type;
  }

  setType(type) {
    this.type = type;
  }

  setTypeBlocks() {
    this.setType(1);
  }

  setTypeQuiz() {
    this.setType(2);
  }

  getId() {
    return this.id;
  }

  getUUID() {
    return this.uuid;
  }

  getName() {
    return this.name;
  }

  setName(name) {
    this.name = name;
  }

  getOrder() {
    return this.order;
  }

  setOrder(order) {
    this.order = order;
  }

  /**
   *
   * @param {BlockModel} block
   */
  addBlock(block) {
    this.blocks.push(block);
  }

  getBlocks() {
    return this.blocks;
  }

  getBlock(id) {
    return this.getBlocks().find(block => block.getId() === id);
  }

  setBlocks(blocks) {
    this.blocks = blocks;
  }

  removeBlock(id) {
    this.setBlocks(this.getBlocks().filter(block => block.getId() !== id));
  }

  getBlocksCount() {
    return this.blocks.length;
  }

  updateBlocksOrder() {
    this.getBlocks().forEach((block, index) => block.setOrder(++index));
  }

  typeIsQuiz() {
    return parseInt(this.type) === 2;
  }

  isNew() {
    return this.id === undefined;
  }

  getQuizId() {
    return this.blocks[0].getQuizId();
  }

  getQuizSlideId() {
    return this.blocks[0].getId();
  }

  getQuizBlockId() {
    return this.blocks[0].getQuizBlockId();
  }

  getQuizName() {
    return this.blocks[0].getQuizName();
  }

  updateQuiz(quiz_id, quiz_name, block_id) {
    this.setTypeQuiz();
    /** @type {QuizBlockModel} */
    const block = this.blocks[0];
    block.setQuizId(quiz_id);
    block.setQuizName(quiz_name);
    block.setQuizBlockId(block_id);
  }
}
