import {_extends, shuffle} from "../common";

const PassTest = function (test) {
  this.element = null;
};

PassTest.prototype.createContent = function (payload) {

  let content = payload.content;
  payload.fragments.forEach(function(fragment) {

    const code = $('<span/>', {
      'class': 'dropdown',
      'data-fragment-id': fragment.id
    });
    code.append('<button class="pass-test-btn dropdown-toggle highlight" data-toggle="dropdown"></button><ul class="dropdown-menu"></ul>');

    const select = $('<select/>', {
      'class': 'highlight custom-select',
      'data-fragment-id': fragment.id
    });
    $('<option/>').val('').text('').appendTo(select);

    const items = shuffle(fragment.items);
    items.forEach((item) => {
      $('<option/>').text(item.title).appendTo(select);
    });

    const reg = new RegExp('{' + fragment.id + '}');
    content = content.replace(reg, select[0].outerHTML);
  });

  return content;
};

PassTest.prototype.createWrapper = function (content) {
  const $wrapper = $('<div class="seq-question pass-test-question"><div class="seq-question__wrap seq-question__wrap--full pass-test-question__wrap"></div></div>');
  const $answers = $('<div/>', {
    'class': 'wikids-test-answers seq-answers'
  });
  if (content) {
    $answers.append(content);
  }
  $wrapper
    .find(".seq-question__wrap")
    .append($answers);

  return $wrapper;
};

PassTest.prototype.create = function (question, answers) {

  const $content = $(this.createContent(question.payload));
  $content.on('click', '.dropdown-menu a', function(e) {
    e.preventDefault();
    const text = $(this).text();
    $(this).parents('[data-fragment-id]:eq(0)').find('.highlight').text(text);
  });

  this.element = this.createWrapper($content)
    .find(".seq-question__wrap");
  return this.element;
};

PassTest.prototype.getContent = function(payload) {

  let content = payload.content;

  payload.fragments.forEach(function(fragment) {

    const correctItem = fragment.items.filter((item) => {
      return item.correct;
    })[0];

    let correctItemTitle = '';
    if (correctItem) {
      correctItemTitle = correctItem.title;
    }

    const reg = new RegExp('{' + fragment.id + '}');
    content = content.replace(reg, correctItemTitle);
  });
  return '<div style="max-width:600px;margin:0 auto;font-size:24px">' + content + '</div>';
};

PassTest.prototype.getUserAnswers = function() {
  return this.element.find('.highlight').map(function(index, elem) {
    return $(elem).find('option:selected').text().toLowerCase();
  }).get();
}

_extends(PassTest, {
  pluginName: 'passTestQuestion'
});

export default PassTest;
