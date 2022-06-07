
const AnswerTypeNumPad = function() {

};

AnswerTypeNumPad.prototype.create = function(callback) {

  var html = '<div class="keyboard-wrapper"><ul id="keyboard" class="clearfix">' +
      '<li class="letter">0</li>' +
      '<li class="letter-empty"></li>' +
      '<li class="letter">1</li>' +
      '<li class="letter">2</li>' +
      '<li class="letter">3</li>' +
      '<li class="letter">4</li>' +
      '<li class="letter">5</li>' +
      '<li class="letter">6</li>' +
      '<li class="letter">7</li>' +
      '<li class="letter">8</li>' +
      '<li class="letter">9</li>' +
      '<li class="letter">10</li>' +
      '<li class="letter-empty clearl"></li>' +
      '<li class="letter-empty"></li>' +
      '<li class="letter">11</li>' +
      '<li class="letter">12</li>' +
      '<li class="letter">13</li>' +
      '<li class="letter">14</li>' +
      '<li class="letter">15</li>' +
      '<li class="letter">16</li>' +
      '<li class="letter">17</li>' +
      '<li class="letter">18</li>' +
      '<li class="letter">19</li>' +
      '<li class="letter">20</li>' +
      '</ul>' +
      '<p></p></div>',
    $html = $(html);

  $html.find('li.letter').on('click', function() {
    $(this).parent().parent().find('p').text($(this).text());
    callback($(this).text());
  });

  return $html;
}

AnswerTypeNumPad.prototype.reset = function(element) {
  element.find('#keyboard + p').text('');
}

export default AnswerTypeNumPad;
