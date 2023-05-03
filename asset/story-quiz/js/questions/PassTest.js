import {_extends, shuffle} from "../common";
import InnerDialog from "../components/Dialog";
import passTestRegionContent from "./PassTestRegionContent";

const PassTest = function (test) {
  this.element = null;
  this.container = test.container;
  this.deck = test.getDeck();
};

function createSelectElement(fragmentId, attrs = {}, items, multi = false) {

  const $wrapper = $('<div/>', {
    'data-fragment-id': fragmentId,
    class: 'highlight custom-select',
    ...attrs
  });

  const $toggle = $('<div/>', {
    class: 'dropdown-toggle disabled',
    'data-toggle': 'dropdown'
  });
  $toggle.html('&nbsp;')
  $toggle.appendTo($wrapper);

  const $menu = $('<div/>', {class: 'dropdown-menu'});

  const singleCallback = item => $('<div/>', {class: 'dropdown-item'})
    .append(
      $('<div/>', {class: 'custom-control'})
        .append(
          $('<input/>', {type: 'radio', name: fragmentId, id: 'item' + item.id})
        )
        .append(
          $('<label/>', {for: 'item' + item.id}).html(item.title)
        )
    );

  const multipleCallback = item => $('<div/>', {class: 'dropdown-item'})
    .append(
      $('<div/>', {class: 'custom-control'})
        .append(
          $('<input/>', {type: 'checkbox', id: 'item' + item.id, 'value': item.title})
        )
        .append(
          $('<label/>', {for: 'item' + item.id}).html(item.title)
        )
    );

  items.forEach(
    item => $menu.append(multi ? multipleCallback(item) : singleCallback(item))
  );

  $menu.appendTo($wrapper);

  return $wrapper;
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

function createRegionElement(fragment, attrs = {}) {
  attrs = {
    'data-fragment-id': fragment.id,
    class: 'highlight custom-input',
    ...attrs
  };
  const div = $('<div/>', attrs);

  div.text('Выберите область');
  return div;
}

/*function checkFragmentValueIsCorrect(fragmentId, value, fragments) {
  const fragment = fragments.find(elem => elem.id === fragmentId);
  if (fragment) {
    const fragmentItem = fragment.items.find(item => item.title === value);
    return fragmentItem && fragmentItem.correct;
  }
  return false;
}*/

function checkFragmentValueIsCorrect(fragmentId, value, fragments) {
  const values = Array.isArray(value) ? value : [value];
  const fragment = fragments.find(elem => elem.id === fragmentId);
  if (fragment) {
    const correctValues = fragment.items.filter(item => item.correct).map(item => item.title);
    return correctValues.every(correctValue => {
      return values.some(value => {
        return correctValue === value;
      });
    });
  }
  return false;
}

function getFragmentCorrectValues(fragments, fragmentId) {
  const fragment = fragments.find(elem => elem.id === fragmentId);
  if (fragment) {
    return fragment.items.filter(item => item.correct).map(item => item.title);
  }
  return [];
}

function resetFragmentElement(element) {
  element
    .removeClass('disabled')
    .removeClass('highlight-done')
    .removeClass('highlight-fail')
    .removeAttr('disabled')
    .find('.dropdown-toggle')
    .html('&nbsp;')
    .addClass('disabled');
  element
    .addClass('disabled')
    .prop('disabled', true);

  element.removeData('values');

  element.find('.dropdown-menu input[type=radio]').prop('checked', false);
  element.find('.dropdown-menu input[type=checkbox]').prop('checked', false);

  if (element.hasClass('custom-input')) {
    element.val('');
  }

  if (element.hasClass('region-fragment-btn')) {
    element.text('Выберите область');
  }
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

let currentIncorrectFragmentId;

function checkHandler($target, check, fragmentId, $content, maxPrevItems) {

  $target
    .removeClass('highlight-fail')
    .removeClass('highlight-done');

  if (check) {

    currentIncorrectFragmentId = null;

    $target
      .addClass('highlight-done disabled')
      .prop('disabled', true)
      .find('.dropdown-toggle')
      .addClass('disabled');

    const next = $content.find('.highlight:not(.highlight-done,.highlight-fail):eq(0)');
    if (next.length && next.hasClass('disabled') && (!next.hasClass('highlight-done'))) {
      next.removeClass('disabled');
      next.removeAttr('disabled');
      next.find('.dropdown-toggle').removeClass('disabled');
    }

  } else {

    $target.addClass('highlight-fail');

    if (currentIncorrectFragmentId !== fragmentId) {

      const max = maxPrevItems || 0;
      if (max === 0) {
        $content.find('.highlight.highlight-done,.highlight.highlight-fail').each((i, elem) => {
          if ($(elem).attr('data-fragment-id') !== fragmentId) {
            resetFragmentElement($(elem));
          }
        });
      } else {

        const prevList = [];
        $content.find('.highlight.highlight-done,.highlight.highlight-fail').each((i, elem) => {
          if ($(elem).attr('data-fragment-id') === fragmentId) {
            return;
          }
          prevList.push($(elem));
        });

        if (prevList.length) {
          prevList.reverse();
          prevList.forEach((elem, i) => {
            if (i >= max) {
              return;
            }
            resetFragmentElement($(elem));
          });
        }
      }
    }

    currentIncorrectFragmentId = fragmentId;
  }
}

PassTest.prototype.create = function (question, fragmentAnswerCallback) {

  const {fragments} = question.payload;
  let {content} = question.payload;

  fragments.forEach((fragment) => {
    let element;

    const elemAttrs = {
      class: 'highlight disabled',
      disabled: 'disabled'
    };

    if (fragment['type'] === 'region') {
      elemAttrs.class += ' custom-input region-fragment-btn';
      element = createRegionElement(fragment, elemAttrs);
    } else {
      if (question.item_view === 'text' || (fragment.items.length === 1 && 1 === fragment.items.filter(item => item.correct).length)) {
        const correctItem = fragment.items.filter(item => item.correct);
        if (correctItem.length === 0) {
          return;
        }
        elemAttrs.size = correctItem[0].title.length;
        elemAttrs.class += ' custom-input';
        element = createTextElement(fragment.id, elemAttrs);
      } else {
        elemAttrs.class += ' custom-select';
        element = createSelectElement(fragment.id, elemAttrs, shuffle(fragment.items), fragment.multi);
      }
    }

    const reg = new RegExp('{' + fragment.id + '}');
    content = content.replace(reg, element[0].outerHTML);
  });

  const $content = $(content);

  $content
    .find('.highlight:eq(0)')
    .removeClass('disabled')
    .removeAttr('disabled')
    .find('.dropdown-toggle')
    .removeClass('disabled');

  $content.on('change', '.dropdown-item input[type=radio]', e => {
    const $target = $(e.target);
    const value = $target.siblings('label').html().trim();

    $target.parents('.highlight:eq(0)').find('.dropdown-toggle').html(value);

    const fragmentId = $target.parents('.highlight:eq(0)').attr('data-fragment-id');
    const check = checkFragmentValueIsCorrect(fragmentId, value, fragments);

    $target.parents('.highlight:eq(0)').find('.dropdown-toggle').dropdown('toggle');

    checkHandler($target.parents('.highlight:eq(0)'), check, fragmentId, $content, question['max_prev_items']);

    if (typeof fragmentAnswerCallback === 'function') {
      fragmentAnswerCallback(check, value);
    }
  });

  $content.on('change', '.dropdown-item input[type=checkbox]', e => {

    const $target = $(e.target);
    const $elem = $target.parents('.highlight:eq(0)');
    const values = $elem.data('values') || [];

    const value = $target.val();
    if ($target.is(':checked') && !values.includes(value)) {
      values.push(value);
    }

    if (!$target.is(':checked') && values.includes(value)) {
      values.splice(values.indexOf(value), 1);
    }

    $elem.data('values', values);

    $target
      .parents('.highlight:eq(0)')
      .find('.dropdown-toggle').html(values.length ? values.join(', ') : '&nbsp;');

    const fragmentId = $target.parents('.highlight:eq(0)').attr('data-fragment-id');
    const fragment = fragments.find(elem => elem.id === fragmentId);
    if (values.length === fragment.items.filter(item => item.correct).length) {

      const check = checkFragmentValueIsCorrect(fragmentId, values, fragments);

      $elem.find('.dropdown-toggle').dropdown('toggle');

      checkHandler($target.parents('.highlight:eq(0)'), check, fragmentId, $content, question['max_prev_items']);

      if (typeof fragmentAnswerCallback === 'function') {
        fragmentAnswerCallback(check, values.join(', '));
      }
    }
  });

  $content.on('change', 'input[type=text]', (e) => {
    const value = e.target.value;

    const fragmentId = $(e.target).attr('data-fragment-id');
    const check = checkFragmentValueIsCorrect(fragmentId, value, fragments);

    checkHandler($(e.target), check, fragmentId, $content, question['max_prev_items']);

    const $target = $(e.target);
    if (check) {
      if ($target.parent().hasClass('highlight-wrap')) {
        $target.parent().popover('destroy');
        $target.unwrap();
      }
    } else {
      if ($target.parent().hasClass('highlight-wrap')) {
        $target.parent().popover('show');
      } else {
        const correctItems = getFragmentCorrectValues(fragments, fragmentId);
        if (correctItems.length) {
          $target.wrap(`<div class="highlight-wrap" data-trigger="manual" style="display: inline-block; position: relative" data-placement="auto" data-content="${correctItems.join(', ')}"></div>`);
          $target.parent().popover('show');
        }
      }
    }

    if (typeof fragmentAnswerCallback === 'function') {
      fragmentAnswerCallback(check, $(e.target).val());
    }
  });

  $content.on('click', '.dropdown-menu', function(e) {
    e.stopPropagation();
  });

  $content.on('click', '.region-fragment-btn', (e) => {

    const fragmentId = e.target.getAttribute('data-fragment-id');
    const fragment = fragments.find(elem => elem.id === fragmentId);

    const { region } = fragment;
    const { image, regions } = region;

    const props = {scale: (this.deck && this.deck.getScale()) || null};
    const regionContent = passTestRegionContent('q' + fragmentId, image, regions, props, (check, answers) => {

      checkHandler($(e.target), check, fragmentId, $content, question['max_prev_items']);

      dialog.hide();

      e.target.textContent = check ? fragment.items[0].title : 'Выберите область';

      if (typeof fragmentAnswerCallback === 'function') {
        fragmentAnswerCallback(check, answers.join(', '));
      }
    });

    const dialog = new InnerDialog(this.container, {title: 'Отметьте правильную область', content: regionContent});
    dialog.show();
  });

  this.element = this.createWrapper($content)
    .find(".seq-question__wrap");
  return this.element;
};

PassTest.prototype.getContent = function(payload) {

  let content = payload.content;

  payload.fragments.forEach(function(fragment) {

    const correctItems = fragment.items
      .filter(item => item.correct)
      .sort((a, b) => parseInt(a.order) - parseInt(b.order));

    let correctItemTitle = '';
    if (correctItems.length) {
      correctItemTitle = correctItems.map(item => item.title).join(', ');
    }

    const reg = new RegExp('{' + fragment.id + '}');
    content = content.replace(reg, correctItemTitle);
  });
  return '<div style="max-width:800px;margin:0 auto;font-size:24px;line-height:1.4;">' + content.replace(/\s\s+/g, ' ') + '</div>';
};

PassTest.prototype.getUserAnswers = function() {
  const answers = this.element.find('.highlight').map(function(index, elem) {

    const $el = $(elem);

    if ($el.is('input[type=text]')) {
      return [$el.val().trim().toLowerCase()];
    }

    const $radio = $el.find('.dropdown-menu input[type=radio]:checked');
    if ($radio.length) {
      return [$radio.siblings('label').html().trim().toLowerCase()];
    }

    const $boxes = $el.find('.dropdown-menu input[type=checkbox]:checked');
    if ($boxes.length) {
      const values = $el.data('values') || [];
      if (values.length) {
        return values.map(val => val.trim().toLowerCase());
      }
      return $boxes.map((i, box) => $(box).attr('value').trim().toLowerCase()).get();
    }

    if ($el.hasClass('region-fragment-btn')) {
      return [$el.html().toLowerCase()];
    }
  }).get();
  console.log(answers);
  return answers;
}

_extends(PassTest, {
  pluginName: 'passTestQuestion'
});

export default PassTest;
