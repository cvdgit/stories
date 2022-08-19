import "./customcontrols.css";
import SlidesPlayer from "../../SlidesPlayer";
import SlidesFullscreen from "../../SlidesFullscreen";

export default () => {

  return {
    id: 'customcontrols',
    init: (deck) => {

      const slidesPlayer = new SlidesPlayer(deck);
      const slidesFullscreen = new SlidesFullscreen();

      const controls = [
        {
          icon: 'icomoon-chevron-left',
          className: 'custom-navigate-left',
          title: 'Назад',
          action: () => {
            slidesPlayer.left();
          }
        },
        {
          icon: 'icomoon-chevron-right',
          className: 'custom-navigate-right',
          title: 'Вперед',
          action: () => {
            slidesPlayer.right();
          }
        },
        {
          icon: 'icomoon-arrows',
          className: 'custom-fullscreen',
          title: 'Полноэкранный режим',
          action: () => {

            slidesFullscreen.toggleFullscreen();

            const el = $(this).find('i');

            el
              .removeClass('icomoon-arrows')
              .removeClass('icomoon-arrows-alt');

            slidesFullscreen.inFullscreen()
              ? el.addClass('icomoon-arrows')
              : el.addClass('icomoon-arrows-alt');
          }
        },
        {
          icon: 'glyphicon glyphicon-bullhorn',
          className: 'custom-feedback',
          title: 'Сообщить об опечатке на слайде',
          action: () => {
            if (deck.hasPlugin('feedback')) {
              const feedback = deck.getPlugin('feedback');
              feedback.sendFeedback();
            }
          }
        }
      ];
      const rightControls = [];

      const $controls = $('<div/>');
      $controls.addClass('customcontrols');

      $.each(controls, function(i, control) {

        const $button = $('<button/>'),
              $icon = $('<i/>').addClass(control.icon);

        $button
          .addClass('enabled')
          .addClass(control.className)
          .attr('title', control.title)
          .on('click', control.action)
          .append($icon.wrap('<div class="controls-arrow"></div>'))
          .appendTo($controls);

      });

      $('.story-controls').append($controls);

      const $rightControls = $("<div/>");
      $rightControls
        .addClass("customcontrols")
        .addClass("customcontrols-right");

      $.each(rightControls, function(i, control) {

        const $button = $('<button/>'),
              $icon = $('<i/>').addClass(control.icon);

        $button
          .addClass('enabled')
          .addClass(control.className)
          .attr('title', control.title)
          .on('click', control.action)
          .append($icon.wrap('<div class="controls-arrow"></div>'))
          .appendTo($rightControls);

      });

      const $controlsWrapper = $('<div/>');

      $controlsWrapper
        .addClass('story-controls')
        .append($controls)
        .appendTo('.reveal');

      const callback = (ev) => {

        const left = $('.custom-navigate-left', $('.reveal'));

        deck.getProgress() === 0
          ? left.attr('disabled', 'disabled')
          : left.removeAttr('disabled');

        const right = $('.custom-navigate-right', $('.reveal'));

        deck.getProgress() === 1
          ? right.attr('disabled', 'disabled')
          : right.removeAttr('disabled');
      };

      deck.addEventListener('ready', callback);
      deck.addEventListener('slidechanged', callback);
    }
  };
}
