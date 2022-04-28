import GlobalContext from "../../GlobalContext";
import {fetchJsonPost} from "../../utils";

export default class UpdateAction {

  constructor() {
    /** @type {LessonManager} */
    this.lessonManager = GlobalContext.lessonManager;
    this.courseId = GlobalContext.courseId;
  }

  action() {

    const data = {
      course: {
        story_id: this.courseId,
        lessons: this.lessonManager.getLessonsForSave()
      }
    };

    fetchJsonPost('/admin/index.php?r=course/lessons-update', data)
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
