(function() {
  const modal = new RemoteModal({
    id: 'column-questions-import-modal',
    title: 'Импортировать вопросы'
  });
  $('#import-column-questions').on('click', (e) => {
    e.preventDefault();
    modal.show({
      url: $(e.target).attr('href'),
      callback: function() {
        attachBeforeSubmit(document.getElementById('column-questions-import-form'), (form) => {

          const formData = new FormData(form)

          sendForm($(form).attr('action'), $(form).attr('method'), formData)
            .done((response) => {
              if (response.success) {
                toastr.success('Успешно');
                location.reload()
              } else {
                toastr.error(response.message || 'Ошибка')
              }
            })
        })
      }
    })
  })
})()
