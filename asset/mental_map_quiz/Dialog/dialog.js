
const InnerDialog = function(container, {title, content}) {

  const $wrapper = $('<div/>', {class: 'slide-hints-wrapper', 'css': {'z-index': 400, display: 'none'}});
  const $background = $('<div/>', {class: 'slide-hints-background'});
  const $inner = $('<div/>', {class: 'slide-hints-inner'});
  const $content = $('<div/>', {class: 'slide-hints', css: {height: '640px'}});

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
              hideHandler()
            })
        )
    )
    .appendTo($inner);

  $content.append(content);
  $content.appendTo($inner);

  /**
   * @callback afterShowCallback
   * @param {HTMLElement} wrapper
   */

  /**
   * @param {afterShowCallback} afterShowCallback
   */
  this.show = (afterShowCallback) => {
    container.append($wrapper);
    $wrapper.fadeIn();
    if (typeof afterShowCallback === 'function') {
      afterShowCallback($wrapper.find('.slide-hints-inner')[0]);
    }
  };

  const hideHandler = () => {
    $wrapper.hide()
    $wrapper.remove();
    if (typeof this.hideCallback === 'function') {
      this.hideCallback()
    }
  }

  this.hide = hideHandler

  this.onHide = callback => {
    this.hideCallback = callback
  }

  this.getWrapper = () => $wrapper.find('.slide-hints-inner')[0]
};

export default InnerDialog;
