(function () {

  const modal = new RemoteModal({
    id: 'update-test-repeat-modal',
    title: 'Изменить кол-во повторений в тестах',
    dialogClassName: 'modal-lg'
  });
  $('#update-test-repeat').on('click', function (e) {
    e.preventDefault();

    modal.show({
      url: $(this).attr('href'),
      callback: function () {

        const $modalContent = $(this);

        attachBeforeSubmit($modalContent.find("form"), function(form) {

          $modalContent.find('table tbody tr[data-test-id]').each((i, elem) => {

            const $row = $(elem);
            const $statusCol = $row.find('td.status');

            $statusCol.html('<img src="/img/loading.gif" width="20" height="20" alt="..." />');

            const formData = new FormData(form[0]);
            formData.append(`UpdateTestsRepeatForm[testId]`, $row.attr('data-test-id'))

            sendForm($(form).attr("action"), $(form).attr("method"), formData)
              .done(response => {
                if (response && response.success) {
                  $statusCol.html('<i class="glyphicon glyphicon-ok text-success"></i>');
                } else {
                  $statusCol.html('<i class="glyphicon glyphicon-remove text-danger"></i>');
                }
              });
          });
        })
      }
    });
  });

})();

(function () {

  const modal = new RemoteModal({
    id: 'update-pass-test-repeat-modal',
    title: 'Изменить значение "Возврат на" в вопросах с пропусками',
    dialogClassName: 'modal-lg'
  });
  $('#update-pass-test-repeat').on('click', function (e) {
    e.preventDefault();

    modal.show({
      url: $(this).attr('href'),
      callback: function () {

        const $modalContent = $(this);

        attachBeforeSubmit($modalContent.find("form"), function(form) {

          $modalContent.find('table tbody tr[data-question-id]').each((i, elem) => {

            const $row = $(elem);
            const $statusCol = $row.find('td.status');

            $statusCol.html('<img src="/img/loading.gif" width="20" height="20" alt="..." />');

            const formData = new FormData(form[0]);
            formData.append(`UpdatePassTestsRepeatForm[questionId]`, $row.attr('data-question-id'))

            sendForm($(form).attr("action"), $(form).attr("method"), formData)
              .done(response => {
                if (response && response.success) {
                  $statusCol.html('<i class="glyphicon glyphicon-ok text-success"></i>');
                } else {
                  $statusCol.html('<i class="glyphicon glyphicon-remove text-danger"></i>');
                }
              });
          });
        })
      }
    });
  });

})()
