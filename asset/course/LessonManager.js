export default class LessonManager {

  constructor() {
    this.lessons = new Map();
  }

  /**
   *
   * @param {LessonModel} lesson
   */
  addLesson(lesson) {
    this.lessons.set(lesson.getUUID(), lesson);
  }

  getLessons() {
    return this.lessons;
  }

  getLesson(id) {
    return this.lessons.get(id);
  }

  setLessonName(id, name) {
    const lesson = this.lessons.get(id);
    lesson.setName(name);
  }

  updateOrder(id, order) {
    const lesson = this.lessons.get(id);
    lesson.setOrder(order);
  }

  /**
   *
   * @param {Number} blockId
   * @param {String} fromLessonId
   * @param {String} toLessonId
   */
  moveBlock(blockId, fromLessonId, toLessonId) {

    blockId = parseInt(blockId);

    /* @type {LessonModel} */
    const fromLesson = this.lessons.get(fromLessonId);
    /* @type {BlockModel} */
    const block = fromLesson.getBlock(blockId);

    /* @type {LessonModel} */
    const toLesson = this.lessons.get(toLessonId);
    toLesson.addBlock(block);

    fromLesson.removeBlock(blockId);

    if (toLesson.getType() === undefined) {
      toLesson.setTypeBlocks();
    }
  }

  getLessonsForSave() {
    return Array.from(this.getLessons())
      .map(([key, lesson]) => {
        lesson.updateBlocksOrder();
        return lesson;
      })
      .sort((a, b) => {
        return a.order - b.order;
      });
  }

  deleteLesson(uuid) {
    this.getLessons().delete(uuid);
  }
}
