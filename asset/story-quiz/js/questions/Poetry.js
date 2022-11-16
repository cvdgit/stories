import {_extends, shuffle} from "../common";

const Poetry = function (test) {

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

    const list = test.getQuestionList() || [];
    list.forEach((q) => {
      $content.append($('<div/>', {style: 'margin: 0 0 10px; font-size: 24px'}).text(q.name));
    });

    $content.append($('<p/>', {style: 'font-size:24px'}).text(question.name));

    const answers = shuffle(question.storyTestAnswers);
    answers.forEach((answer) => {

      const btn = $('<button/>', {type: 'button', class: 'pass-test-btn', style: 'margin-bottom: 10px'})
        .text(answer.name);

      btn.on('click', () => {
        test.nextQuestion([answer.id]);
      });

      $('<div/>')
        .append(btn)
        .appendTo($wrap);
    });

    return $wrap;
  };

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

    scroll() {
      const list = that.element.find('.poetry-list');
      list.prop('scrollTop', list.prop("scrollHeight"));
    }
  }
};

_extends(Poetry, {
  pluginName: 'poetryQuestion'
});

export default Poetry;
