const TestLinked = function(data) {

  var stories = [];

  function init() {
    if (!data || !data.length) {
      return;
    }
    stories = data;
  }

  init();

  function getHtml() {
    if (!stories.length) {
      return '';
    }
    var $wrapper = $('<div/>')
      .addClass('test-linked-stories-wrapper');
    $wrapper.append($('<p/>').text('Посмотрите историю'))
    stories.forEach(function(story) {
      $('<a/>')
        .attr('href', story['url'])
        .css('display', 'block')
        .append(
          $('<img/>').attr('src', story['image'])
        )
        .append($('<p/>').text(story['title']))
        .appendTo($wrapper);
    });
    return $wrapper;
  }

  var API = {};
  API.getHtml = getHtml;
  return API;
}

export default TestLinked;
