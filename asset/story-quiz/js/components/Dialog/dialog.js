
const InnerDialog = function(container, {title, content}) {

  const $wrapper = $('<div/>', {class: 'slide-hints-wrapper', 'css': {'z-index': 400}});
  const $background = $('<div/>', {class: 'slide-hints-background'});
  const $inner = $('<div/>', {class: 'slide-hints-inner'});
  const $content = $('<div/>', {class: 'slide-hints'});

  $background.appendTo($wrapper);
  $inner.appendTo($wrapper);

  $('<header/>', {class: 'slide-hints-header'})
    .append(
      $('<h3/>', {class: 'slide-hints-header__title'}).text(title)
    )
    .append(
      $('<div/>', {'class': 'header-actions'})
        .append(
          $('<button/>', {
            class: 'hints-close',
            html: '&times;'
          })
            .on('click', function () {
              /*$hintWrapper.hide();
              $(this).parents('.slide-hints-wrapper:eq(0)').remove();
              if (!that.container.find('.slide-hints-wrapper').length) {
                $('.reveal .story-controls').show();
              }*/
              hide();
            })
        )
    )
    .appendTo($inner);

  $content.append(content);
  $content.appendTo($inner);

  const show = () => {
    console.log('show');
    container.append($wrapper);
  };

  const hide = () => {
    console.log('hide');
    $wrapper.hide()
    $wrapper.remove();
  };

  return {
    show,
    hide
  }
};

export default InnerDialog;
