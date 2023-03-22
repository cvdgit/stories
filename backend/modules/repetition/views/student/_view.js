(function() {

  const modal = new RemoteModal({id: 'create-repetition-modal', title: 'Создать повторения', dialogClassName: 'modal-lg'});
  $('#create-repetition').on('click', function(e) {
    e.preventDefault();

    modal.show({
      url: $(this).attr('href'),
      callback: function() {

        const $modalContent = $(this);

        $('#create-next-repetition').on('click', function(e) {
          e.preventDefault();

          const url = $(this).attr('href');

          $modalContent.find('table tbody tr[data-test-id]').each((i, elem) => {

            const $row = $(elem);
            const $statusCol = $row.find('td.status');

            $statusCol.html('<img src="/img/loading.gif" width="20" height="20" alt="..." />');

            const formData = new FormData();
            formData.append('NextRepetitionForm[test_id]', $row.attr('data-test-id'));

            sendForm(url, 'post', formData)
              .done(response => {
                if (response && response.success) {
                  $statusCol.html('<i class="glyphicon glyphicon-ok text-success"></i>');
                } else {
                  $statusCol.html('<i class="glyphicon glyphicon-remove text-danger"></i>');
                }
              });
          });
        });
      }
    });
  });
})();
