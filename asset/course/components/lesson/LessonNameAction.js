import GlobalContext from "../../GlobalContext";
import {fetchJsonPost} from "../../utils";

export default class LessonNameAction {

  /**
   * @param {LessonModel} lesson
   */
  constructor(lesson) {

    this.lesson = lesson;

    /** @type {LessonManager} */
    this.lessonManager = GlobalContext.lessonManager;
  }

  action(name) {

    this.lessonManager.setLessonName(this.lesson.getUUID(), name);

    fetchJsonPost('/admin/index.php?r=course/update-lesson-name', {
      lesson_id: this.lesson.getId(),
      lesson_name: name
    })
      .then((responseJson) => {
        if (responseJson.success) {
          toastr.success(responseJson.message || 'Успешно');
        }
        else {
          toastr.error(responseJson.message || 'Ошибка');
        }
      })
      .catch((error) => {
        toastr.error(error);
      });
  }
}
