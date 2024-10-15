(function () {
  $('#edu-stats').on('click', '.show-testing', function (e) {
    e.preventDefault();

    const thisRow = $(this).parents('tr:eq(0)');

    let testingRow = thisRow.next();
    if (!testingRow.hasClass('testing-row')) {
      testingRow = $('<tr/>', {class: 'testing-row'}).append(
        $('<td/>', {colspan: thisRow.find('td').length})
      );
      testingRow.insertAfter(thisRow);
    }

    const url = $(this).attr('href');

    testingRow.find('td').empty();
    $.getJSON(url)
      .done(function (response) {
        if (response && response.success) {
          const testings = response.data || [];
          if (testings.length === 0) {
            testingRow.find('td').text('Тестирование не найдено');
            return;
          }
          testings.forEach(testing => {
            const row = $('<div/>', {class: 'testing-item'})
              .data('resource', testing.resource)
              .append(
                $('<div/>', {class: 'testing-item__name'}).text(testing.name)
              )
              .append(
                $('<div/>', {class: 'testing-item__incorrect'}).text(testing.incorrect)
              )
              .append(
                $('<div/>', {class: 'testing-item__progress'}).text('Прогресс: ' + testing.progress)
              );
            testingRow.find('td').append(row);
          });
        }
      });
  });

  $('#edu-stats').on('click', '.testing-item', function (e) {
    e.preventDefault();
    const resource = $(this).data('resource');
    $('#test-detail-modal').modal({'remote': resource});
  });

  $('#test-detail-modal').on('hide.bs.modal', function () {
    $(this).removeData('bs.modal');
    $(this).find('.modal-content').html('');
  });

  $('.student-progress-table-content').on('click', '.content-lesson [data-to-lesson]', function () {
    const lessonId = $(this).attr('data-to-lesson');
    $('#edu-stats [data-lesson-id=' + lessonId + ']:eq(0)')[0].scrollIntoView({
      behavior: "smooth",
      block: "start",
      inline: "start"
    });
  });

  $('#edu-stats')
    .on('click', '.show-mental-maps', (e) => {
      e.preventDefault()

      const href = e.target.getAttribute('href')
      $('#test-detail-modal')
        .modal({'remote': href})
    })
})();
