import createDescription from "./description";

export function createBeginPage(testResponse, options = {canModerate: false, onActive: null, onStart: null, onRestart: null}) {

  const $listGroup = $('<div/>').addClass('list-group');

  let activeStudent;

  if (testResponse.students.length > 0) {
    activeStudent = testResponse.students[0];
  }

  testResponse.students.forEach((student) => {

    const $item = $('<a/>')
      .attr('href', '')
      .addClass('list-group-item')
      .data('student', student)
      .append(
        $('<h4/>')
          .addClass('list-group-item-heading')
          .text(student.name)
      );

    const progress = parseInt(student['progress'])
    if (progress > 0) {
      $item.append(
        $('<p/>').addClass('list-group-item-text').text(progress + '% завершено')
      );
    }

    $item.on('click', function(e) {
      e.preventDefault();

      const stud = $(this).data('student');
      const studProgress = parseInt(stud.progress);

      activeStudent = stud;
      $beginButton.text(studProgress === 0 ? 'Начать тест' : 'Продолжить тест');

      $(this).siblings().removeClass('active');
      $(this).addClass('active');

      if (studProgress === 0) {
        $restartQuiz.hide();
      }
      else {
        $restartQuiz.css('display', 'block');
      }

      if (typeof options.onActive === 'function') {
        options.onActive(stud);
      }
    });

    $item.appendTo($listGroup);
  });

  const $beginButton = $('<button/>')
    .addClass('btn wikids-test-begin')
    .text('...')
    .on('click', function () {
      const fastMode = $col.find('.fast-mode-check').is(':checked');
      if (typeof options.onStart === 'function') {
        options.onStart(fastMode);
      }
    });

  const $col = $('<div/>').addClass('col-md-6')
    .append($('<h3/>').text('Выберите ученика:'))
    .append($listGroup);

  if (options.canModerate) {
    const $options = $('<div/>', {
      class: 'wikids-test-begin-page-options'
    });
    $options.append('<label><input class="fast-mode-check" type="checkbox" /> быстрый режим</label>');
    $col.append($options);
  }

  $col.append($beginButton);

  const $restartQuiz = $('<a/>', {
    href: '',
    text: 'Начать заново',
    class: 'restart-quiz',
    css: {
      display: 'none',
      padding: '10px'
    }
  });
  $restartQuiz.on('click', function(e) {
    e.preventDefault();
    if (!confirm('Вы уверены, что хотите начать тестирование сначала?')) {
      return;
    }

    const that = $(this);

    if (typeof options.onRestart === 'function') {
      options.onRestart(activeStudent.id)
        .done((response) => {
          if (response && response.success) {
            toastr.success('Успешно');

            const active = $listGroup.find('a.active')

            active.find('.list-group-item-text').remove();

            const stud = active.data('student');
            stud.progress = 0;
            active.data('student', stud);

            active.click();
          }
          else {
            toastr.error(response['message'] || 'Неизвестная ошибка');
          }
        })
    }
  });
  $col.append($restartQuiz);

  $listGroup.find('a:eq(0)').click();

  return $('<div/>', {class: 'wikids-test-begin-page row row-no-gutters'})
    .append($('<h3/>').text(testResponse.test.header))
    .append($col)
    .append(
      $('<div/>', {class: 'col-md-6'}).append(
        createDescription(testResponse.test.description)
      )
    );
}
