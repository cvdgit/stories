import {_extends} from "../common";
import InnerDialog from "../components/Dialog";

const ImageGaps = function (test) {
  this.element = null;
  this.container = test.container;
  this.deck = test.getDeck();
};

ImageGaps.prototype.createWrapper = function (content) {
  const $wrapper = $('<div class="seq-question image-gaps-question"></div>');
  if (content) {
    $wrapper.append(content);
  }
  return $wrapper;
};

ImageGaps.prototype.create = function (question, fragmentAnswerCallback) {
  const {fragments, content} = question.payload;

  const $content = $(content);

  let allAnswers = [];
  let currentIncorrectFragmentId;
  const maxPrevItems = parseInt(question['max_prev_items']) || 0;

  const that = this;
  $content.on('click', '.scheme-mark:not(.image-gaps-fragment__done)', function(e) {
    e.preventDefault();

    const $elem = $(this);
    const fragmentId = $elem.attr('data-answer-id');
    const fragment = fragments.find(f => f.id === fragmentId);

    const $answers = $('<div/>');
    fragment.items.map(item => {
      $('<div/>', {class: "image-gaps-answer-item"})
        .append(
          $('<label/>')
            .append($('<input/>', {type: "checkbox", "data-item-id": item.id, class: "image-gaps-answer-item__check"}))
            .append(`<span class="image-gaps-answer-item__title">${item.title}</span>`)
        )
        .appendTo($answers);
    });

    const dialog = new InnerDialog(that.container, {title: 'Варианты ответов', content: $answers});

    const values = [];
    dialog.show((wrap) => {
      wrap.on('change', 'input[type=checkbox]', function() {

        const $target = $(this);
        const itemId = $(this).attr("data-item-id");

        if ($target.is(':checked') && !values.includes(itemId)) {
          values.push(itemId);
        }

        if (!$target.is(':checked') && values.includes(itemId)) {
          values.splice(values.indexOf(itemId), 1);
        }

        if (values.length === fragment.items.filter(item => item.correct).length) {

          const correctValues = fragment.items.filter(i => i.correct).map(i => i.id);
          const check = correctValues.every(correctValue => {
            return values.some(value => {
              return correctValue === value;
            });
          });

          if (check) {
            allAnswers = [...allAnswers, {fragmentId, answers: values}];
            $elem.addClass("image-gaps-fragment__done");
          } else {
            if (maxPrevItems > 0) {

              for (let i = 0; i < maxPrevItems; i++) {
                const answer = allAnswers.pop();
                if (answer) {
                  $content.find(`[data-answer-id='${answer.fragmentId}'].image-gaps-fragment__done`)
                    .removeClass("image-gaps-fragment__done");
                }
              }

            } else {
              allAnswers = [];
              $content.find(".image-gaps-fragment__done")
                .removeClass("image-gaps-fragment__done");
            }
          }

          dialog.hide();

          if (typeof fragmentAnswerCallback === 'function') {
            fragmentAnswerCallback(check, allAnswers);
          }
        }
      });
    });
  })

  const {imageWidth, imageHeight} = question.params;
  let initialZoom = 0.5;
  if (imageHeight > 500) {
    initialZoom = 500 / imageHeight;
  } else {
    initialZoom = 1;
  }

  window.regionZoom = panzoom($content.find('#regionImageWrap')[0], {
    excludeClass: 'scheme-mark',
    bounds: true,
    initialZoom,
    initialX: 0,
    initialY: 0
  });

  this.element = $('<div/>', {class: 'image-gaps'}).append($content);

  return this.element;
};

ImageGaps.prototype.getContent = function(question) {
  const {fragments, content} = question.payload;

  const $content = $(content);
  $content.find(".scheme-mark").remove();

  const {imageWidth, imageHeight} = question.params;
  let initialZoom = 0.5;
  if (imageHeight > 500) {
    initialZoom = 500 / imageHeight;
  } else {
    initialZoom = 1;
  }

  window.regionZoom = panzoom($content.find('#regionImageWrap')[0], {
    excludeClass: 'scheme-mark',
    bounds: true,
    initialZoom,
    initialX: 0,
    initialY: 0
  });
  return this.createWrapper($('<div/>', {class: 'image-gaps'}).append($content));
};

_extends(ImageGaps, {
  pluginName: 'imageGapsQuestion'
});

export default ImageGaps;
