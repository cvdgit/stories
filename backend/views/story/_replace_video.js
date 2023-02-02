(function() {

  const modal = new RemoteModal({
    id: 'video-replace-modal',
    title: 'Заменить видео'
  });

  $('#video-replace').on('click', function(e) {
    e.preventDefault();

    modal.show({
      url: $(this).attr('href'),
      callback: function() {
        attachBeforeSubmit($(this).find('form')[0], function(form) {
          sendForm($(form).attr('action'), $(form).attr('method'), new FormData(form))
            .done(response => {
              if (response && response.success) {
                toastr.success(response.message);
              }
              if (response && response.success === false) {
                toastr.error(response.message);
              }
              modal.hide();
            });
        });
      }
    });
  });
})();
