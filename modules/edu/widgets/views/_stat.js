(function () {
  $('#edu-stats').on('click', '.show-testing', function (e) {
    e.preventDefault();
    const href = e.target.getAttribute('href');
    $('#test-detail-modal')
      .on('hide.bs.modal', function () {
        $(this).find('.modal-content').html('');
        $(this).removeData('bs.modal');
      })
      .modal({'remote': href});
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
      e.preventDefault();
      const href = e.target.getAttribute('href');
      $('#mental-map-detail-modal')
        .on('hide.bs.modal', function () {
          $(this).find('.modal-content').html('');
          $(this).removeData('bs.modal');
        })
        .modal({'remote': href});
    });

  $('#edu-stats').on('click', '.show-detail', e => {
    e.preventDefault();
    const dialog = new RemoteModal({
      id: 'story-detail-dialog',
      title: 'Содержимое истории, просмотренное в рамках сессии',
      dialogClassName:'modal-lg'
    });
    dialog.show({url: e.target.getAttribute('href'), callback: () => {}});
  });
})();
