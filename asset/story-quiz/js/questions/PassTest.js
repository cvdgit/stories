import {_extends, shuffle} from "../common";

const PassTest = function (test) {
  this.element = null;
};

function createSelectElement(fragmentId, attrs = {}, items) {
  attrs = {
    'data-fragment-id': fragmentId,
    class: 'highlight custom-select',
    ...attrs
  };
  const select = $('<select/>', attrs);
  $('<option/>').val('').text('').appendTo(select);

  items.forEach((item) => {
    $('<option/>').text(item.title).appendTo(select);
  });

  return select;
}

function createTextElement(fragmentId, attrs = {}) {
  attrs = {
    type: 'text',
    'data-fragment-id': fragmentId,
    class: 'highlight custom-input',
    ...attrs
  };
  const input = $('<input/>', attrs);
  input.prop('size', attrs.size);
  return input;
}

function checkFragmentValueIsCorrect(fragmentId, value, fragments) {
  const fragment = fragments.find(elem => elem.id === fragmentId);
  if (fragment) {
    const fragmentItem = fragment.items.find(item => item.title === value);
    return fragmentItem && fragmentItem.correct;
  }
  return false;
}

function resetFragmentElement(element) {
  element
    .removeClass('disabled')
    .removeClass('highlight-done')
    .removeClass('highlight-fail')
    .removeAttr('disabled')
    .val('');
  element
    .addClass('disabled')
    .prop('disabled', true);
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

PassTest.prototype.create = function (question, fragmentAnswerCallback) {

  const {fragments} = question.payload;
  let {content} = question.payload;
console.log(question);
  fragments.forEach((fragment) => {
    let element;

    const elemAttrs = {
      class: 'highlight disabled',
      disabled: 'disabled'
    };

    if (question.item_view === 'text') {
      const correctItem = fragment.items.filter(item => item.correct);
      if (correctItem.length === 0) {
        return;
      }
      elemAttrs.size = correctItem[0].title.length;
      elemAttrs.class += ' custom-input';
      element = createTextElement(fragment.id, elemAttrs);
    } else {
      elemAttrs.class += ' custom-select';
      element = createSelectElement(fragment.id, elemAttrs, shuffle(fragment.items));
    }

    const reg = new RegExp('{' + fragment.id + '}');
    content = content.replace(reg, element[0].outerHTML);
  });

  const $content = $(content);

  $content
    .find('.highlight:eq(0)')
    .removeClass('disabled')
    .removeAttr('disabled');

  $content.on('change', 'select,input[type=text]', (e) => {
    const value = e.target.value;

    const fragmentId = $(e.target).attr('data-fragment-id');
    const check = checkFragmentValueIsCorrect(fragmentId, value, fragments);

    $(e.target)
      .removeClass('highlight-fail')
      .removeClass('highlight-done');

    if (check) {

      $(e.target)
        .addClass('highlight-done disabled')
        .prop('disabled', true);

      const next = $content.find('.highlight:not(.highlight-done,.highlight-fail):eq(0)');
      if (next.length && next.hasClass('disabled') && (!next.hasClass('highlight-done'))) {
        next.removeClass('disabled');
        next.removeAttr('disabled');
      }

    } else {

      $(e.target).addClass('highlight-fail');

      /*$content.find('.highlight.highlight-done,.highlight.highlight-fail').each((i, elem) => {
        if ($(elem).attr('data-fragment-id') !== fragmentId) {
          resetFragmentElement($(elem));
        }
      });*/

      const prevAll = $(e.target).prevAll('.highlight.highlight-done,.highlight.highlight-fail');
      if (prevAll.length) {
        const max = question['max_prev_items'] || 0;
        prevAll.each((i, elem) => {

          if (i >= max) {
            return;
          }

          resetFragmentElement($(elem));
        });
      }
    }

    if (typeof fragmentAnswerCallback === 'function') {
      fragmentAnswerCallback(check, $(e.target).val());
    }
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
