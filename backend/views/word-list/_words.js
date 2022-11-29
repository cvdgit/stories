(function() {

  const createWordDialog = new RemoteModal({id: 'create-word-modal', title: 'Новое слово'});
  const createCallback = function() {
    const formElement = document.getElementById('create-test-word-form');
    attachBeforeSubmit(formElement, (form) => {
      sendForm($(form).attr('action'), $(form).attr('method'), new FormData(form))
        .done((response) => {
          if (response && response.success) {
            createWordDialog.hide();
            toastr.success(response.message);
            $.pjax.reload('#pjax-words', {timeout: 3000});
          }
        })
    });
  };
  $('#create-test-word').on('click', function(e) {
    e.preventDefault();
    createWordDialog.show({url: $(this).attr('href'), callback: createCallback});
  });

  const updateWordDialog = new RemoteModal({id: 'update-word-modal', title: 'Изменить слово'});
  const updateCallback = function() {
    const formElement = document.getElementById('update-test-word-form');
    attachBeforeSubmit(formElement, (form) => {
      sendForm($(form).attr('action'), $(form).attr('method'), new FormData(form))
        .done((response) => {
          if (response && response.success) {
            updateWordDialog.hide();
            toastr.success(response.message);
            $.pjax.reload('#pjax-words', {timeout: 3000});
          }
        })
    });
  };
  $('#test-word-table').on('click', '.update-test-word', function(e) {
    e.preventDefault();
    updateWordDialog.show({url: $(this).attr('href'), callback: updateCallback});
  });

  const copyWordDialog = new RemoteModal({id: 'copy-word-modal', title: 'Копировать слово'});
  const copyCallback = function() {
    const formElement = document.getElementById('copy-test-word-form');
    attachBeforeSubmit(formElement, (form) => {
      sendForm($(form).attr('action'), $(form).attr('method'), new FormData(form))
        .done((response) => {
          if (response && response.success) {
            copyWordDialog.hide();
            toastr.success(response.message);
            $.pjax.reload('#pjax-words', {timeout: 3000});
          }
        })
    });
  };
  $('#test-word-table').on('click', '.copy-test-word', function(e) {
    e.preventDefault();
    copyWordDialog.show({url: $(this).attr('href'), callback: copyCallback});
  });
})();
