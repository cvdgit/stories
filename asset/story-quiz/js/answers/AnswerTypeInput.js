const answerTypeInput = {};
answerTypeInput.create = function(action) {
  //var $html = $('<input type="text" class="answer-input" style="width: 80%" />');
  var $html = $('<textarea class="answer-input" style="width: 80%" rows="5" />');
  $html.keypress(function(e) {
    if (e.which == 13) {
      action();
      return false;
    }
  });
  return $html;
};

export default answerTypeInput;
