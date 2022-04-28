import Lesson from "./Lesson";
import LessonBlock from "./LessonBlock";
import Course from "./Course";

export default class CourseDom {

  /**
   *
   * @param {LessonManager} lessonManager
   */
  constructor(lessonManager) {
    this.lessonManager = lessonManager;
    this.course = null;
    this.container = null;
    this.lessons = new Map();
  }

  /**
   *
   * @param {HTMLElement} container
   * @param initLessons
   * @param onComplete
   */
  renderCourse(container, initLessons, onComplete) {

    this.container = container;
    this.container.innerHTML = '';

    this.lessons = new Map();

    this.course = new Course(initLessons);
    this.container.appendChild(this.course.render());
    if (typeof onComplete === 'function') {
      onComplete(this.course);
    }
  }

  /**
   *
   * @param {LessonModel} lesson
   */
  renderLesson(lesson) {
    const render = new Lesson(lesson).render();
    this.lessons.set(lesson.getUUID(), render);
    this.lessonManager.addLesson(lesson);
    return render;
  }

  /**
   *
   * @param {BlockModel} block
   */
  renderLessonBlock(block) {
    const render = new LessonBlock(block).render();
    return render;
  }

  updateLessonOrder() {
    $(this.container).find('section').each((index, element) => {
      const order = index + 1;
      $(element).attr('data-lesson-order', order);
      const lessonId = $(element).attr('data-lesson-id');
      this.lessonManager.updateOrder(lessonId, order);
    });
  }

  /**
   * @param {HTMLElement} blockElement
   * @param {String} fromLessonId
   * @param {String} toLessonId
   */
  moveToLesson(blockElement, fromLessonId, toLessonId) {
    this.lessonManager.moveBlock(blockElement.getAttribute('data-block-id'), fromLessonId, toLessonId);
    blockElement.remove();
    this.updateLesson(toLessonId);
  }

  updateLesson(lessonId) {
    const lesson = this.lessonManager.getLesson(lessonId);
    const render = this.renderLesson(lesson);
    $(this.container).find("section[data-lesson-id='" + lessonId + "']")
      .parents('.lesson-wrap:eq(0)').empty().replaceWith(render);
  }

  beforeDeleteLesson(lessonUUID) {
    /** @type {HTMLElement} */
    const elem = this.lessons.get(lessonUUID);
    elem.classList.add('lesson-wrap--deleted');
  }

  restoreDeleteLesson(lessonUUID) {
    /** @type {HTMLElement} */
    const elem = this.lessons.get(lessonUUID);
    elem.classList.remove('lesson-wrap--deleted');
  }

  deleteLesson(lessonUUID, onDelete) {

    /** @type {HTMLElement} */
    const elem = this.lessons.get(lessonUUID);

    $(elem).fadeOut('slow', () => {
      elem.remove();
      this.lessons.delete(lessonUUID);
      this.lessonManager.deleteLesson(lessonUUID);
      if (typeof onDelete === 'function') {
        onDelete(this.lessonManager.isEmpty());
      }
    });
  }
}
