import {_extends, shuffle} from "../common";

const PassTest = function (test) {
  this.element = null;
};

function createSelectElement(fragmentId, items) {

  const select = $('<select/>', {
    'class': 'highlight custom-select',
    'data-fragment-id': fragmentId
  });
  $('<option/>').val('').text('').appendTo(select);

  items.forEach((item) => {
    $('<option/>').text(item.title).appendTo(select);
  });

  return select;
}

function createTextElement(fragmentId, size) {
  const input = $('<input/>', {
    'type': 'text',
    'class': 'highlight custom-input',
    'data-fragment-id': fragmentId
  });
  input.prop('size', size);
  return input;
}

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

  const {fragments} = question.payload;
  let {content} = question.payload;

  //question.item_view

  fragments.forEach(fragment => {

    const code = $('<span/>', {
      'class': 'dropdown',
      'data-fragment-id': fragment.id
    });
    code.append('<button class="pass-test-btn dropdown-toggle highlight" data-toggle="dropdown"></button><ul class="dropdown-menu"></ul>');

    let element;
    if (question.item_view === 'text') {
      const correctItem = fragment.items.filter(item => item.correct);
      if (correctItem.length === 0) {
        return;
      }
      element = createTextElement(fragment.id, correctItem[0].title.length);
    } else {
      element = createSelectElement(fragment.id, shuffle(fragment.items));
    }

    const reg = new RegExp('{' + fragment.id + '}');
    content = content.replace(reg, element[0].outerHTML);
  });

  const $content = $(content);
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
  return '<div style="max-width:800px;margin:0 auto;font-size:24px;line-height:1.4;">' + content.replace(/\s\s+/g, ' ') + '</div>';
};

PassTest.prototype.getUserAnswers = function() {
  return this.element.find('.highlight').map(function(index, elem) {
    const $el = $(elem);
    return ($el.is('select') ? $el.find('option:selected').text() : $el.val()).trim().toLowerCase();
  }).get();
}

_extends(PassTest, {
  pluginName: 'passTestQuestion'
});

export default PassTest;
