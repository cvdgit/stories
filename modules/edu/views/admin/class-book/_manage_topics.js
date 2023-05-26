(function() {

  const modal = new RemoteModal({
    id: 'manage-topics',
    title: 'Управление доступом к темам',
    dialogClassName: 'modal-lg'
  })

  $('#manage-topics').on('click', (e) => {
    e.preventDefault();
    const url = e.target.getAttribute('href');
    modal.show({
      url,
      callback: function() {

        const $form = $(this).find('form');

        attachBeforeSubmit($form[0], form => {

          const formData = new FormData(form);

          $(this).find('#topic-access-list').find('input[type=checkbox]:checked').each((i, elem) => {
            formData.append('TopicAccessForm[' + i + '][class_program_id]', $(elem).attr('data-program-id'));
            formData.append('TopicAccessForm[' + i + '][topic_id]', $(elem).val());
          });

          sendForm($form.attr('action'), $form.attr('method'), formData)
            .done(response => {
              if (response && response.success) {
                toastr.success(response.message);
                modal.hide();
              }
              if (response && response.success === false) {
                toastr.error(response.message || 'Произошла ошибка');
              }
            });
        });
      }
    })
  });
})();
