(function() {
  const createPoetryDialog = new RemoteModal({id: 'create-poetry-modal', title: 'Запоминание стихов'});
  const poetryCallback = function() {
    const formElement = document.getElementById('poetry-form');
    attachBeforeSubmit(formElement, (form) => {
      sendForm($(form).attr('action'), $(form).attr('method'), new FormData(form))
        .done((response) => {
          if (response && response.success) {
            createPoetryDialog.hide();
            toastr.success(response.message);
          }
          if (response && response.success === false) {
            toastr.error(response.message);
          }
        })
        .fail(response => {
          toastr.error(response.responseText);
        });
    });
  };
  $('#create-poetry').on('click', function(e) {
    e.preventDefault();
    createPoetryDialog.show({url: $(this).attr('href'), callback: poetryCallback});
  });
})();
