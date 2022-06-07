import {createStarFillBig} from "./stars";

const QuestionSuccess = function(stars) {

  const textMap = [];
  textMap[1] = 'одну звезду';
  textMap[2] = 'две звезды';
  textMap[3] = 'три звезды';
  textMap[4] = 'четыре звезды';
  textMap[5] = 'пять звезд';

  function create(title, image) {

    var $wrap = $('<div/>')
      .addClass('wikids-test-success-question-page')
      .hide();

    var $content = $('<div/>')
      .addClass('wikids-test-success-question-page-content');

    for (var $i = 1; $i <= stars; $i++) {
      $content.append(createStarFillBig());
    }

    $content
      .append(
        $('<h4/>').text('Вы заработали ' + textMap[stars] + '!')
      )
      .append($('<p/>').text(title))
      .append($('<img/>').attr('src', image))
      .appendTo($wrap);

    return $wrap;
  }

  return {
    'create': create
  }
}

export default QuestionSuccess;
