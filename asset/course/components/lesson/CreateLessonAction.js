import {fetchJsonPost} from "../../utils";
import GlobalContext from "../../GlobalContext";

export default class CreateLessonAction {

  /**
   * @param {LessonModel} lesson
   */
  constructor(lesson) {
    this.lesson = lesson;
    this.renderer = GlobalContext.renderer;
  }

  action(insertPosition, targetOrder, onSuccess) {

    fetchJsonPost('/admin/index.php?r=course/lesson-create', {
      course_id: GlobalContext.courseId,
      insert_position: insertPosition,
      lesson_order: targetOrder
    })
      .then((responseJson) => {
        if (responseJson.success) {
          if (typeof onSuccess === 'function') {
            onSuccess(responseJson.lesson);
          }
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
