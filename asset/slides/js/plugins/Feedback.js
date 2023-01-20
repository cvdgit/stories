import SlidesConfig from "../SlidesConfig";

export default () => {
  return {
    deck: null,
    options: {},
    id: 'feedback',

    init(deck) {

      this.deck = deck;

      const slidesConfig = new SlidesConfig();
      this.config = slidesConfig.get('feedback');
    },

    sendFeedback() {

      const currentSlide = this.deck.getCurrentSlide();

      function InnerDialog(title, content) {

        var defIndex = 400;
        $(currentSlide).find('.slide-hints-wrapper').each(function () {
          defIndex++;
        });
        var $hintWrapper = $('<div/>', {'class': 'slide-hints-wrapper', 'css': {'z-index': defIndex}});
        var $hintBackground = $('<div/>', {'class': 'slide-hints-background'});
        var $hintInner = $('<div/>', {'class': 'slide-hints-inner'});
        var $hint = $('<div/>', {'class': 'slide-hints slide-hints--feedback'});

        $hintBackground.appendTo($hintWrapper);
        $hintInner.appendTo($hintWrapper);

        $('<header/>', {'class': 'slide-hints-header'})
          .append(
            $('<h3/>', {class: 'slide-hints-header__title'}).text(title)
          )
          .append(
            $('<div/>', {'class': 'header-actions'})
              .append(
                $('<button/>', {
                  'class': 'hints-close',
                  'html': '&times;'
                })
                  .on('click', function () {
                    hideDialog();
                  })
              )
          )
          .appendTo($hintInner);

        $hint.append(content);
        $hint.appendTo($hintInner);

        this.show = function() {
          $('.reveal .story-controls').hide();
          $('.reveal .slides section.present').append($hintWrapper);
        }

        function hideDialog() {
          $hintWrapper.hide().remove();
          if (!$(currentSlide).find('.slide-hints-wrapper').length) {
            $('.reveal .story-controls').show();
          }
        }

        this.hide = hideDialog;
      }

      const content = $('<div/>', {class: 'feedback-inner'})
        .append(
          $('<form/>', {class: 'feedback-form'})
            .append(
              $('<div/>', {class: 'feedback-row'})
                .append(
                  $('<textarea/>', {css: {'width': '100%'}, rows: 10})
                )
            )
            .append(
              $('<div/>', {class: 'feedback-row feedback-actions'})
                .append(
                  $('<button/>', {type: 'button', class: 'btn feedback-send'})
                    .text('Отправить')
                )
            )
        );

      var feedbackDialog = new InnerDialog('Обратная связь', content);
      feedbackDialog.show();

      content.find('textarea').focus();

      const send = (data) => {
        return $.ajax({
          url: this.config.action,
          type: 'POST',
          dataType: 'json',
          data: data
        });
      }

      const findQuizObject = () => {

        if (window['WikidsStoryTest']) {

          const elem = $(currentSlide).find('div.new-questions');

          if (elem.length) {

            const test = elem[0]['_wikids_test'];

            if (test !== undefined) {
              return test;
            }
          }
        }
      }

      const makePayload = (text) => {

        var payload = {
          text
        };

        const transitionPlugin = this.deck.getPlugin('transition');
        console.log(transitionPlugin.getInTransition());

        payload.slide_id = $(currentSlide).attr("data-id");

        const quiz = findQuizObject();
        if (quiz) {
          payload.testing_id = quiz.getTestingId();
          payload.question_id = quiz.getCurrentQuestionId();
        }
        return payload;
      }

      content.find('.feedback-send').on('click', function() {

        var text = content.find('textarea').val();
        var payload = makePayload(text);

        send(payload)
          .done(function(response) {
            if (response && response.success) {
              toastr.success('Сообщение успешно отправлено');
            }
            else {
              toastr.error((response && response['message']) || 'Неизвестная ошибка');
            }
          })
          .fail(function(response) {
            toastr.error((response['responseJSON'] && response.responseJSON.message) || 'Произошла ошибка');
          });

        feedbackDialog.hide();
      });
    }
  };
}
