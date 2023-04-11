(function() {

  const $lessonList = $('#lesson-list');
  const $controls = $('#controls');
  $lessonList.on('change', '[data-lesson-id]', function() {
    $controls.show();
  });

  function getLessonIds($container) {
    return $container
      .find('.edu-topic .edu-lesson [data-lesson-id]')
      .map((i, elem) => parseInt($(elem).attr('data-lesson-id')))
      .get();
  }

  $controls.find('button').on('click', function() {

    const formData = new FormData();
    formData.append('LessonAccessForm[action]', 'access');

    $lessonList
      .find('.edu-topic .edu-lesson [data-lesson-id]')
      .each((i, elem) => {

        const lessonId = $(elem).attr('data-lesson-id');
        const accessType = $(elem).find('option:selected').val();

        formData.append(`LessonAccessForm[lessonIds][${i}]`, lessonId);
        formData.append(`LessonAccessForm[accessTypes][${i}]`, accessType);
      });

    sendForm($(this).attr('data-action'), 'post', formData)
      .then(response => {
        if (response && response.success) {
          toastr.success('Успешно');
          $controls.hide();
        }
      });
  });
})();
