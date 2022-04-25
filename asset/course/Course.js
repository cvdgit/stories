import GlobalContext from "./GlobalContext";

export default class Course {

  /**
   *
   * @param lessons
   */
  constructor(lessons) {
    this.lessons = lessons;
    this.renderer = GlobalContext.renderer;
  }

  render() {
    const wrap = document.createElement('div');
    wrap.classList.add('lesson-list');
    this.lessons.forEach((lesson) => {
      wrap.appendChild(this.renderer.renderLesson(lesson));
    });
    return wrap;
  }
}
