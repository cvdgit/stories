(function() {

  const modal = new RemoteModal({id: 'create-repetition-modal', title: 'Создать повторение'});
  $('#create-repetition').on('click', function(e) {
    e.preventDefault();
    modal.show({
      url: $(this).attr('href'),
      callback: function() {



        const formElement = document.getElementById('create-repetition-form');
        attachBeforeSubmit(formElement, (form) => {

          $(this).find('#test-items-list input[type=checkbox][name=test_id]:checked').each((i, elem) => {

            const $container = $(elem).parents('.test-actions:eq(0)');
            $container.html('<img src="/img/loading.gif" width="20" height="20" alt="..." />');

            const formData = new FormData();

            const testId = parseInt($(elem).val());
            formData.append('test_id', testId);

            formData.append('student_id', $(form).find('#createrepetitionform-student_id').val());
            formData.append('schedule_id', $(form).find('#createrepetitionform-schedule_id').val());

            sendForm('/admin/index.php?r=/repetition/story/start', 'post', formData)
              .done(response => {
                if (response && response.success) {
                  $container.html('<i class="glyphicon glyphicon-ok text-success"></i>');
                } else {
                  $container.html('<i class="glyphicon glyphicon-remove text-danger"></i>');
                }
              });
          });
        });
      }
    });
  });
})();
