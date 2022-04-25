import "./scss/style.scss";
import GlobalContext from "./GlobalContext";
import LessonModel from "./LessonModel";
import LessonManager from "./LessonManager";
import BlockModel from "./BlockModel";
import CourseDom from "./CourseDom";
import QuizBlockModel from "./QuizBlockModel";
import QuizUpdateModal from "./components/QuizUpdateModal";
import QuizCreateModal from "./components/lesson/QuizCreateModal";

const app = () => {

  const lessonManager = GlobalContext.lessonManager = new LessonManager();
  const renderer = GlobalContext.renderer = new CourseDom(lessonManager);

  GlobalContext.quizUpdateModal = new QuizUpdateModal();
  GlobalContext.quizCreateModal = new QuizCreateModal();

  const courseData = window['courseData'] || {};
  const lessons = [];
  courseData.lessons.forEach((lessonItem) => {
    const lesson = new LessonModel(lessonItem);
    lessonItem.blocks.forEach((blockItem) => {
      let block;
      if (lesson.typeIsQuiz()) {
        block = new QuizBlockModel(blockItem);
      }
      else {
        block = new BlockModel(blockItem)
      }
      lesson.addBlock(block);
    });
    lessons.push(lesson);
  });

  GlobalContext.courseId = courseData.story_id;

  renderer.renderCourse(document.querySelector('#app'), lessons, (course) => {
    $('#save-course').removeClass('hide');
  });

  $('.lesson-list .lesson-block__action > .dropdown').on('show.bs.dropdown', (e) => {

    const list = $(e.target).find('ul.dropdown-menu');
    list.empty();

    const $lessonElem = $(e.target).parents('.lesson:eq(0)');
    const currentLessonId = $lessonElem.attr('data-lesson-id');

    lessonManager.getLessons().forEach((lesson) => {

      if (currentLessonId === lesson.getUUID()) {
        return;
      }

      const li = document.createElement('li');
      const link = document.createElement('a');
      link.setAttribute('href', '#');
      link.textContent = lesson.getName();
      li.addEventListener('click', (e) => {
        e.preventDefault();

        const $blockElem = $(e.target).parents('.lesson-block:eq(0)');
        renderer.moveToLesson($blockElem[0], currentLessonId, lesson.getUUID());
      });
      li.appendChild(link);
      list.append(li);
    });
  });

  $('#save-course').on('click', () => {

    const data = {
      course: {
        story_id: courseData.story_id,
        lessons: lessonManager.getLessonsForSave()
      }
    };

    fetch('/admin/index.php?r=course/save', {
      method: 'post',
      body: JSON.stringify(data),
      cache: 'no-cache',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      }
    })
      .then((response) => {
        if (response.ok) {
          return response.json();
        }
        throw new Error(response.statusText);
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
  });
};

document.addEventListener('DOMContentLoaded', app);
