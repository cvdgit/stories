import {_extends, shuffle} from "../common";

const Poetry = function (test, el, options) {

  this.element = null;

  const createContent = (question) => {

    const $wrap = $('<div/>', {
      style: 'text-align: center'
    });

    const $content = $('<div/>', {
      class: 'poetry-list',
      style: 'text-align: center; max-height: 350px; overflow-y: auto; margin-bottom: 20px'
    });

    $content.appendTo($wrap);

    options.historyValues.forEach(val => {
      $content.append($('<div/>', {style: 'margin: 0 0 10px; font-size: 2rem'}).text(val));
    });

    const list = test.getQuestionList() || [];
    list.forEach((q) => {
      $content.append($('<div/>', {style: 'margin: 0 0 10px; font-size: 2rem'}).text(q.name));
    });

    $content.append($('<p/>', {style: 'font-size:2rem'}).text(question.name));

    const answers = shuffle(question.storyTestAnswers);
    const btnWrap = $('<div/>', {'class': 'drag-words-answers__words'});
    answers.forEach((answer) => {

      const btn = $('<button/>', {type: 'button', class: 'pass-test-btn', style: 'margin-bottom: 10px'})
        .text(answer.name);

      btn.attr('data-answer-id', answer.id);

      btn.on('click', () => {
        test.nextQuestion([answer.id]);
      });

      btn.appendTo(btnWrap);
    });
    $wrap.append(btnWrap);

    return $wrap;
  };

  this.overlay = null;

  const that = this;

  return {

    createWrapper(content) {
      const $wrapper = $('<div class="drag-words-question"><div class="drag-words-question__wrap"></div></div>');
      const $answers = $('<div/>', {
        'class': 'wikids-test-answers drag-words-answers'
      });
      if (content && content.length) {
        $answers.append(content);
      }
      $wrapper
        .find(".drag-words-question__wrap")
        .append($answers);
      return $wrapper;
    },

    create(question) {

      that.element = this.createWrapper(createContent(question))
        .find(".drag-words-question__wrap");

      return that.element;
    },

    scroll(el) {
      const list = el.find('.poetry-list');
      list.prop('scrollTop', list.prop("scrollHeight"));
    },

    showCorrectOverlay(answer, correct) {
      (answer || []).forEach(answerId => that.element.find(`button[data-answer-id=${answerId}]`).addClass('danger'));
      correct.forEach(correctAnswer => that.element.find(`button[data-answer-id=${correctAnswer.id}]`).addClass('success'));
    },

    createOverlay(clickCallback) {
      that.overlay = $(`
    <div class="audio-backdrop" style="position: absolute; width: 100%; height: 100%; left: 0; top: 0; z-index: 10; display: flex; align-items: center; justify-content: center">
      <div style="pointer-events: none; position: absolute; width: 100%; height: 100%; left: 0; top: 0; background: rgba(255,255,255,0.1)"></div>
    </div>
  `);
      that.overlay.on('click', clickCallback);
      return that.overlay;
    },

    removeOverlay() {
      that.overlay && that.overlay.remove();
    }
  }
};

_extends(Poetry, {
  pluginName: 'poetryQuestion'
});

export default Poetry;
