import GlobalContext from "./GlobalContext";
import LessonModel from "./LessonModel";
import Modal from "./components/Modal";
import QuizBlockModel from "./QuizBlockModel";
import LessonDeleteAction from "./components/lesson/LessonDeleteAction";
import CreateLessonAction from "./components/lesson/CreateLessonAction";
import LessonMoveAction from "./components/lesson/LessonMoveAction";
import LessonNameAction from "./components/lesson/LessonNameAction";
import UpdateAction from "./components/lesson/UpdateAction";

let moveTimeout;

export default class Lesson {

  /**
   *
   * @param {LessonModel} lesson
   */
  constructor(lesson) {

    this.lesson = lesson;

    /**
     * @type {CourseDom}
     */
    this.renderer = GlobalContext.renderer;

    /**
     * @type {LessonManager}
     */
    this.lessonManager = GlobalContext.lessonManager;

    /**
     * @type {QuizUpdateModal}
     */
    this.quizUpdateModal = GlobalContext.quizUpdateModal;

    /**
     * @type {QuizCreateModal}
     */
    this.quizCreateModal = GlobalContext.quizCreateModal;
  }

  createEditElement() {
    const edit = document.createElement('a');
    edit.setAttribute('href', '#');
    edit.classList.add('lesson-edit');
    edit.textContent = `Слайды (${this.lesson.getBlocksCount()})`;
    if (this.lesson.typeIsQuiz()) {
      edit.textContent = this.lesson.getQuizName();
    }
    edit.addEventListener('click', (e) => {
      e.preventDefault();

      if (this.lesson.typeIsQuiz()) {
        const remote = `/admin/index.php?r=course/quiz-update-form&slide_id=${this.lesson.getQuizSlideId()}&lesson_id=${this.lesson.getId()}`;
        this.quizUpdateModal.modalRemote(remote, (response) => {
          this.lesson.updateQuiz(response.quiz_id, response.quiz_name, response.block_id);
          this.renderer.updateLesson(this.lesson.getUUID());
        });
        return;
      }

      const modal = new Modal('Слайды - ' + this.lesson.getName());

      const elem = document.createElement('div');
      elem.classList.add('lesson-blocks');
      this.lesson.getBlocks().forEach((block) => {
        elem.appendChild(this.renderer.renderLessonBlock(block));
      });
      modal.setContent(elem);

      modal.onShow((e) => {

        const lessons = [];
        const currentLessonId = this.lesson.getUUID();
        this.lessonManager.getLessons().forEach((lesson) => {
          if (currentLessonId === lesson.getUUID()) {
            return;
          }
          if (lesson.typeIsQuiz()) {
            return;
          }
          lessons.push(lesson);
        });

        $(e.target).find('.dropdown').on('show.bs.dropdown', (e) => {

          const list = $(e.target).find('ul.dropdown-menu');
          list.empty();

          if (lessons.length > 0) {
            lessons.forEach((lesson) => {
              const li = document.createElement('li');
              const link = document.createElement('a');
              link.setAttribute('href', '#');
              link.textContent = lesson.getName();
              li.addEventListener('click', (e) => {
                e.preventDefault();
                const $blockElem = $(e.target).parents('.lesson-block:eq(0)');
                this.renderer.moveToLesson($blockElem[0], currentLessonId, lesson.getUUID());
              });
              li.appendChild(link);
              list.append(li);
            });
          }
          else {
            list.append('<li><a>Пусто</a></li>');
          }
        });
      });

      modal.onHide(() => {
        this.renderer.updateLesson(this.lesson.getUUID());
        new UpdateAction().action();
      });

      modal.show();
    });
    return edit;
  }

  createEditContentElement() {
    const contentEdit = document.createElement('a');
    contentEdit.classList.add('btn');
    contentEdit.classList.add('btn-success');
    contentEdit.classList.add('btn-xs');
    contentEdit.classList.add('lesson-content');
    contentEdit.textContent = 'Изменить содержимое';
    if (this.lesson.typeIsQuiz()) {
      contentEdit.setAttribute('target', '_blank');
      contentEdit.setAttribute('href', `/admin/index.php?r=test/update&id=${this.lesson.getQuizId()}`);
    }
    else {
      contentEdit.setAttribute('href', `/admin/index.php?r=editor/lesson&uuid=${this.lesson.getUUID()}`);
    }
    if (this.lesson.getBlocksCount() === 0) {
      contentEdit.textContent = 'Добавить содержимое';
      contentEdit.setAttribute('href', '#');
      contentEdit.addEventListener('click', (e) => {
        e.preventDefault();

        const modal = new Modal('Добавить содержимое', 'sm');
        const content = document.createElement('div');
        content.innerHTML =
          `<button id="add-content-slides" type="button" class="btn btn-block">Слайды</button>
           <button id="add-content-quiz" type="button" class="btn btn-block">Тест</button>`;
        content.querySelector('#add-content-slides').addEventListener('click', (e) => {
          location.href = `/admin/index.php?r=editor/lesson&uuid=${this.lesson.getUUID()}`;
        });
        content.querySelector('#add-content-quiz').addEventListener('click', (e) => {
          modal.hide();
          const remote = `/admin/index.php?r=course/quiz-create-form&lesson_uuid=${this.lesson.getUUID()}`;
          this.quizCreateModal.modalRemote(remote, (response) => {

            const block = new QuizBlockModel({
              slide_id: response.slide_id,
              data: '',
              order: 1,
              quiz_id: response.quiz_id,
              quiz_name: response.quiz_name,
              block_id: response.block_id
            });
            this.lesson.addBlock(block);
            this.lesson.setTypeQuiz();
            this.renderer.updateLesson(this.lesson.getUUID());
          });
        });
        modal.setContent(content);
        modal.show();
      });
    }
    return contentEdit;
  }

  render() {

    const wrap = document.createElement('div');
    wrap.classList.add('lesson-wrap');
    wrap.innerHTML =
      `<div class="insert-lesson insert-lesson--before">
           <button data-action="insert-lesson" data-position="before" class="btn btn-primary btn-xs">Добавить выше</button>
       </div>
       <section class="lesson" data-lesson-id="${this.lesson.getUUID()}" data-lesson-order="${this.lesson.getOrder()}">
         <div class="lesson-head">
            <div class="lesson-head__icon"><i class="glyphicon glyphicon-tasks"></i></div>
            <div class="lesson-head__title" contenteditable="true">${this.lesson.getName()}</div>
            <div class="lesson-head__action">
                <a href="#"><i class="glyphicon glyphicon-arrow-up move-up-lesson"></i></a>
                <a href="#"><i class="glyphicon glyphicon-arrow-down move-down-lesson"></i></a>
                <a href="#"><i class="glyphicon glyphicon-trash delete-lesson"></i></a>
            </div>
         </div>
         <div class="lesson-body">
           <div class="lesson-body__content"></div>
           <div class="lesson-body__edit"></div>
         </div>
       </section>
       <div class="insert-lesson insert-lesson--after">
           <button data-action="insert-lesson" data-position="after" class="btn btn-primary btn-xs">Добавить ниже</button>
       </div>`;

    if (this.lesson.typeIsQuiz()) {
      wrap.querySelector('.lesson').classList.add('lesson--quiz')
      wrap.querySelector('.lesson-head__icon').innerHTML = '<i class="glyphicon glyphicon-education"></i>';
    }

    if (this.lesson.getBlocksCount() > 0) {
      const edit = this.createEditElement();
      wrap.querySelector('.lesson-body__content')
        .appendChild(edit);
    }

    const contentEdit = this.createEditContentElement();
    wrap.querySelector('.lesson-body__edit')
      .appendChild(contentEdit);

    $(wrap).on('click','[data-action=insert-lesson]', (e) => {

      const position = e.target.getAttribute('data-position');
      const createLessonAction = new CreateLessonAction();
      createLessonAction.action(position, this.lesson.getOrder(), (response) => {

        const elem = $(e.target).parents('.lesson-wrap:eq(0)');

        const lesson = new LessonModel(response);
        lesson.setTypeBlocks();
        const lessonRender = this.renderer.renderLesson(lesson);

        if (position === 'after') {
          $(lessonRender).insertAfter(elem);
        }
        if (position === 'before') {
          $(lessonRender).insertBefore(elem);
        }
        this.renderer.updateLessonOrder();
      });
    });

    $(wrap).find('.lesson-head__title')
      .on('focus', (e) => {
        const elem = $(e.target);
        elem.data('before', elem.text());
      })
      .on('blur', (e) => {
        const elem = $(e.target);
        if (elem.data('before') !== elem.text()) {
          elem.data('before', elem.html());
          elem.trigger('change');
        }
      })
      .on('change', (e) => {
        new LessonNameAction(this.lesson).action($(e.target).text());
      });

    wrap.querySelector('.delete-lesson').addEventListener('click', (e) => {
      e.preventDefault();

      if (!confirm('Удалить урок?')) {
        return;
      }

      new LessonDeleteAction(this.lesson).action();
    });

    const moveLesson = () => {
      if (moveTimeout) {
        clearTimeout(moveTimeout);
      }
      moveTimeout = setTimeout(() => {
        new LessonMoveAction().action();
      }, 1500);
    }

    wrap.querySelector('.move-up-lesson').addEventListener('click', (e) => {
      e.preventDefault();

      const elem = $(e.target).parents('.lesson-wrap:eq(0)');
      const sibling = elem[0].previousSibling;
      if (sibling) {
        elem.insertBefore(sibling);
        this.renderer.updateLessonOrder();
        moveLesson();
      }
    });

    wrap.querySelector('.move-down-lesson').addEventListener('click', (e) => {
      e.preventDefault();

      const elem = $(e.target).parents('.lesson-wrap:eq(0)');
      const sibling = elem[0].nextSibling;
      if (sibling) {
        elem.insertAfter(sibling);
        this.renderer.updateLessonOrder();
        moveLesson();
      }
    });

    return wrap;
  }
}
