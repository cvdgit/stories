import GlobalContext from "../../GlobalContext";
import {fetchJsonPost} from "../../utils";

export default class LessonMoveAction {

  constructor() {
    /**
     *
     * @type {LessonManager}
     */
    this.lessonManager = GlobalContext.lessonManager;
  }

  action() {

    const data = this.lessonManager.getLessonsForSave()
      .map((lesson) => {
        return {lesson_id: lesson.getId(), lesson_name: lesson.getName(), lesson_order: lesson.getOrder()};
      });

    fetchJsonPost('/admin/index.php?r=course/update-lessons-order', data)
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
