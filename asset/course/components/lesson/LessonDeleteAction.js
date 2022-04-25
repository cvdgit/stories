import {fetchJsonPost} from "../../utils";
import GlobalContext from "../../GlobalContext";

export default class LessonDeleteAction {

  /**
   * @param {LessonModel} lesson
   */
  constructor(lesson) {
    this.lesson = lesson;
    this.renderer = GlobalContext.renderer;
  }

  action() {
    this.renderer.beforeDeleteLesson(this.lesson.getUUID());

    const deleteLessonElement = (message) => {
      this.renderer.deleteLesson(this.lesson.getUUID(), () => {
        toastr.success(message || 'Успешно');
        this.renderer.updateLessonOrder();
      });
    };

    if (this.lesson.isNew()) {
      deleteLessonElement();
      return;
    }

    fetchJsonPost('/admin/index.php?r=course/lesson-delete', {lesson_id: this.lesson.getId()})
      .then((responseJson) => {
        if (responseJson.success) {
          deleteLessonElement(responseJson.message);
        }
        else {
          toastr.error(responseJson.message || 'Ошибка');
        }
      })
      .catch((error) => {
        toastr.error(error);
        this.renderer.restoreDeleteLesson(this.lesson.getUUID());
      });
  }
}
