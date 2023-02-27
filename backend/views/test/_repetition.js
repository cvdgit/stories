(function() {

  const modal = new RemoteModal({id: 'create-repetition-modal', title: 'Создать повторение'});
  $('#create-repetition').on('click', function(e) {
    e.preventDefault();
    modal.show({
      url: $(this).attr('href'),
      callback: function() {

        const formElement = document.getElementById('create-repetition-form');
        attachBeforeSubmit(formElement, (form) => {
          sendForm($(form).attr('action'), $(form).attr('method'), new FormData(form))
            .done((response) => {
              if (response) {
                if (response.success) {
                  toastr.success(response.message);
                  modal.hide();
                } else {
                  toastr.error(response.message);
                }
              } else {
                toastr.error('Неизвестная ошибка');
              }
            })
        });

      }
    });
  });

  const listModal = new RemoteModal({id: 'list-repetition-modal', title: 'Список повторений', dialogClassName: 'modal-lg'});
  $('#list-repetition').on('click', function(e) {
    e.preventDefault();
    listModal.show({
      url: $(this).attr('href'),
      callback: () => {}
    });
  });
})();
