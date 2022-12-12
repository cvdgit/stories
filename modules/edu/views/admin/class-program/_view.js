(function() {

  const $lessonList = $('#lesson-list');
  const $controls = $('#controls');
  $lessonList.on('click', 'input[data-lesson-id]', function() {
    const ids = getLessonIds($lessonList);
    $controls.show();
  });

  function getLessonIds($container) {
    return $container
      .find('input[type=checkbox]:checked')
      .map((i, elem) => parseInt($(elem).attr('data-lesson-id')))
      .get();
  }

  $controls.find('button').on('click', function() {
    const ids = getLessonIds($lessonList);
    const formData = new FormData();
    formData.append('LessonAccessForm[action]', 'access');
    ids.map(lessonId => formData.append('LessonAccessForm[lessonIds][]', lessonId));
    sendForm($(this).attr('data-action'), 'post', formData)
      .then(response => {
        if (response && response.success) {
          toastr.success('Успешно');
          $controls.hide();
        }
      });
  });
})();
