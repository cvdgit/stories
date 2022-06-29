import {_extends, shuffle} from "../common";
import Sortable from "sortablejs";

const SequenceQuestion = function(test) {

  this.instances = [];

  this.createAnswer = function (list, answers, isHorizontalView) {

    list.empty();

    var _answers = [];
    _extends(_answers, answers);

    _answers = shuffle(_answers);

    _answers.forEach(function (answer) {

      var item = $('<div/>', {
        'class': 'seq-item wikids-sortable-handle' + (isHorizontalView ? ' seq-item--horizontal' : ''),
        'data-answer-id': answer.id
      });

      var $itemWrap = $('<div/>', {
        'class': 'seq-item__wrap' + (isHorizontalView ? ' seq-item__wrap--horizontal' : ''),
      });

      if (!isHorizontalView) {
        $('<span/>', {
          'class': 'seq-item-handle',
          'html': '<i class="glyphicon glyphicon-menu-hamburger"></i>',
        })
          .appendTo($itemWrap);
      }

      if (answer.image) {
        $('<div/>', {
          'class': 'seq-item-image',
          'css': {'cursor': 'zoom-in'}
        })
          .on('click', function () {
            test.showOrigImage(answer['orig_image'] || $(this).attr('src'));
          })
          .append(
            $('<div/>', {
              'class': 'seq-item-image__image',
              'css': {
                'background-color': '#99cd50',
                'background-image': 'url(' + answer.image + ')'
              }
            })
          )
          .appendTo($itemWrap);

        if (!isHorizontalView) {
          $('<div/>', {'class': 'seq-item-title', 'text': answer.name}).appendTo($itemWrap);
        }
      } else {
        $('<div/>', {
          'class': 'seq-item-title' + (isHorizontalView ? ' seq-item-title--horizontal' : ''),
          'text': answer.name
        })
          .appendTo($itemWrap);
      }

      $itemWrap.appendTo(item);
      item.appendTo(list);
    });
    return list;
  }

  this.getAnswerIDs = function(questionId) {

    var instance = this.instances.find(function(item) {
      return parseInt(item.question_id) === parseInt(questionId);
    });

    return instance.element.find('[data-answer-id]').map(function () {
      return parseInt($(this).attr('data-answer-id'));
    }).get();
  }
}

SequenceQuestion.prototype = {

  createWrapper: function(answers, large) {
    var $wrapper = $('<div class="seq-question"><div class="seq-question__wrap"></div></div>');
    var $answers = $('<div/>', {
      'class': 'wikids-test-answers seq-answers' + (large ? ' seq-answers--large' : '')
    });
    if (answers) {
      $answers.append(answers);
    }
    $wrapper.find(".seq-question__wrap").append($answers);
    return $wrapper;
  },

  createAnswers: function(question, answers) {
    var isHorizontalView = parseInt(question['sort_view']) === 1;
    return this.createWrapper(this.createAnswer(answers, isHorizontalView), isHorizontalView);
  },

  create: function(question, answers) {

    var instance = this.instances.find(function(item) {
      return parseInt(item.question_id) === parseInt(question.id);
    });

    if (instance) {
      instance.sorter.destroy();
      instance.element.remove();
      this.instances = this.instances.filter(function(item) {
        return parseInt(item.question_id) !== parseInt(instance.question_id);
      });
    }

    instance = {
      question_id: question.id
    };

    var $list = $('<div/>', {
      class: 'sequence-question-list'
    });

    var isHorizontalView = parseInt(question['sort_view']) === 1;

    instance.sorter = Sortable.create($list[0], {
      ghostClass: 'wikids-sortable-ghost',
      cursor: 'move',
      opacity: 0.6,
      handle: '.wikids-sortable-handle',
      direction: isHorizontalView ? 'horizontal' : 'vertical'
    });

    instance.element = this.createWrapper(this.createAnswer($list, answers, isHorizontalView), isHorizontalView)
      .find(".seq-question__wrap");

    this.instances.push(instance);

    return instance.element;
  },

  createCorrectPage: function(question, answers, showOriginalImageCallback) {

    const isHorizontalView = parseInt(question['sort_view']) === 1;

    answers.sort((a, b) => parseInt(a.order) - parseInt(b.order));

    const $wrapper = $('<div/>', {
      class: 'sequence-correct-page',
      html: '<div class="sequence-correct-page__wrap"></div>'
    });

    const $answers = $(isHorizontalView ? '<ul/>' : '<ol/>');
    answers.forEach((answer) => {

      const $elem = $('<li/>');

      if (answer.image) {
        const $image = $('<img/>')
          .attr("src", answer.image)
          .attr("width", 110)
          .css('cursor', 'zoom-in')
          .on('click', function() {
            showOriginalImageCallback(questionAnswer['orig_image'] || $(this).attr('src'));
          });
        $elem.append($image);
      }

      $elem.append('<div class="answer-name">' + answer.name + '</div>');
      $answers.append($elem);
    });

    $wrapper.find('.sequence-correct-page__wrap').append($answers);

    return $wrapper;
  }
};

_extends(SequenceQuestion, {
  pluginName: 'sequenceQuestion'
});

export default SequenceQuestion;
