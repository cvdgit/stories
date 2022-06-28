import {_extends, shuffle} from "../common";
import Sortable, {Swap} from "sortablejs";

Sortable.mount(new Swap());

const DragWords = function (test) {
  this.element = null;
};

DragWords.prototype.createWrapper = function (content) {
  const $wrapper = $('<div class="drag-words-question"><div class="drag-words-question__wrap"></div></div>');
  const $answers = $('<div/>', {
    'class': 'wikids-test-answers drag-words-answers'
  });
  if (content && content.length) {
    content.forEach(elem => $answers.append(elem));
  }
  $wrapper
    .find(".drag-words-question__wrap")
    .append($answers);
  return $wrapper;
};

DragWords.prototype.create = function (question) {

  const answersContent = this.createAnswers(question.payload);
  this.element = this.createWrapper(answersContent)
    .find(".drag-words-question__wrap");

  return this.element;
};

DragWords.prototype.createAnswers = function (payload) {

  const $content = $('<div/>', {
    class: 'drag-words-answers__content'
  });

  const $words = $('<div/>', {
    class: 'drag-words-answers__words'
  })

  let payloadContent = payload.content;
  let words = [];
  payload.fragments.forEach(function(fragment) {

    const code = $('<span/>', {
      'data-fragment-id': fragment.id,
      class: 'words-spot',
      html: '<button type="button" class="pass-test-btn highlight filter-empty"></button>'
    });

    const reg = new RegExp('{' + fragment.id + '}');
    payloadContent = payloadContent.replace(reg, code[0].outerHTML);

    const $word = $('<button/>', {
      type: 'button',
      class: 'pass-test-btn highlight',
      text: fragment.title
    });
    words.push($word);
  });

  $content.html(payloadContent);

  const that = this;
  $content.on('click', 'button.highlight', function(e) {

    if ($(this).text() === '') {
      return;
    }

    const $firstEmptyWord = that.element.find('.drag-words-answers__words button').filter(function() {
      return $(this).text() === '';
    }).eq(0);

    swap($(this), $firstEmptyWord);
  });

  words = shuffle(words);
  words.forEach(elem => $words.append(elem));

  function swap(a, b) {
    a = $(a);
    b = $(b);
    const tmp = $('<span>').hide();
    a.before(tmp);
    b.before(a);
    tmp.replaceWith(b);
  }

  $words.on('click', 'button.highlight', function(e) {

    if ($(this).text() === '') {
      return;
    }

    const $firstEmptyWord = that.element.find('.drag-words-answers__content button').filter(function() {
      return $(this).text() === '';
    }).eq(0);

    swap($(this), $firstEmptyWord);
  });

  new Sortable($words[0], {
    swap: true,
    filter: '.filter-empty',
    group: {
      name: 'shared'
    }
  });

  $content.find('.words-spot').each((i, elem) => {
    new Sortable(elem, {
      swap: true,
      filter: '.filter-empty',
      group: {
        name: 'shared'
      }
    });
  });

  return [
    $content,
    $words
  ];
};

DragWords.prototype.createWords = function (payload) {

}

DragWords.prototype.getUserAnswers = function() {
  return this.element.find('.words-spot').map(function(index, elem) {
    return {
      id: $(elem).attr('data-fragment-id'),
      text: $(elem).find('button').text().toLowerCase()
    };
  }).get();
}

DragWords.prototype.checkAnswers = function(question, userAnswers) {
  const payload = question.payload;
  const answers = [];
  payload.fragments.forEach((fragment) => {
    if (fragment.correct) {
      answers.push({
        id: fragment.id,
        text: fragment.title.toLowerCase()
      })
    }
  });
  return answers.filter(answer => userAnswers.some(userAnswer => userAnswer.id === answer.id && userAnswer.text === answer.text)).length === answers.length;
}

DragWords.prototype.getContent = function(payload) {
  let content = payload.content;
  payload.fragments.forEach(function(fragment) {
    if (!fragment.correct) {
      return;
    }
    const reg = new RegExp('{' + fragment.id + '}');
    content = content.replace(reg, fragment.title);
  });
  return '<div style="max-width:600px;margin:0 auto;font-size:24px">' + content + '</div>';
};

_extends(DragWords, {
  pluginName: 'dragWordsQuestion'
});

export default DragWords;
