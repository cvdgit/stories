import GlobalContext from "./GlobalContext";
import LessonModel from "./LessonModel";
import {uuidv4} from "./utils";
import CreateLessonAction from "./components/lesson/CreateLessonAction";

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
    if (this.lessons.length === 0) {
      wrap.innerHTML =
        `<div>
           <div>
             <p>Разделы не найдены</p>
           </div>
           <div>
             <button id="create-lesson" type="button" class="btn btn-primary">Создать раздел</button>
           </div>
         </div>`;
      wrap.querySelector('#create-lesson').addEventListener('click', (e) => {

        const createLessonAction = new CreateLessonAction();
        createLessonAction
          .action('before', 1, (response) => {

            /*
            const lesson = new LessonModel({uuid: uuidv4()});
            lesson.setTypeBlocks();
            const lessonRender = this.renderer.renderLesson(lesson);

            $(wrap).empty().append(lessonRender);

            this.renderer.updateLessonOrder();*/

            location.reload();
          });
      });
    }
    else {
      this.lessons.forEach((lesson) => {
        wrap.appendChild(this.renderer.renderLesson(lesson));
      });
    }
    return wrap;
  }
}
