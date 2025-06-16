const answerTypeInput = {};
answerTypeInput.create = function(action, question, showQuestionImage, showOriginalImage) {
  var $html = $(`
<div style="display: flex; align-items: start; justify-content: center;flex-direction: row-reverse; column-gap: 20px">
<textarea spellcheck="false" class="answer-input" style="width: 80%" rows="5" /></div>`);
  $html.keypress(function(e) {
    if (e.which == 13) {
      action();
      return false;
    }
  });

  if (showQuestionImage && question.image) {
    const $image = $('<img/>')
      .attr("src", question.image)
      .css('max-width', '330px');
    const originalImageExists = question['original_image'] === undefined ? true : question['original_image'];
    if (originalImageExists || question['orig_image']) {
      $image
        .css('cursor', 'zoom-in')
        .on('click', function () {
          showOriginalImage(question['orig_image'] || $(this).attr('src'));
        });
    }
    $image.appendTo($html);
  }

  $html.on('paste', (e) => {
    e.preventDefault()
    return false
  })
  return $html;
};

export default answerTypeInput;
