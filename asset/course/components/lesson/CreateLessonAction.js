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

  action(insertPosition) {

    fetchJsonPost('/admin/index.php?r=course/lesson-create', {
      course_id: GlobalContext.courseId,
      insert_position: insertPosition,
      lesson_order: this.lesson.getOrder()
    })
      .then((responseJson) => {
        if (responseJson.success) {

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
