(function() {
  function btnLoading(elem) {
    $(elem).attr("data-original-text", $(elem).html());
    $(elem).prop("disabled", true);
    $(elem).html('<i class="spinner-border"></i> Загрузка...');
  }
  function btnReset(elem) {
    $(elem).prop("disabled", false);
    $(elem).html($(elem).attr("data-original-text"));
  }
  $('#consult-request-form')
    .on('beforeSubmit', function(e) {
      e.preventDefault();
      const btn = $(this).find('button[type=submit]');
      btnLoading(btn);
      $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: new FormData(this),
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false
      })
        .done((response) => {
          if (response?.success) {
            toastr.success('Заявка успешно отправлена')
            $('#consult-request-modal').modal('hide')
          } else {
            toastr.error(response?.message || 'Произошла ошибка')
          }
        })
        .fail((response) => toastr.error(response.statusText || 'Произошла ошибка'))
        .always(function() {
          btnReset(btn);
        });
      return false;
    })
    .on('submit', function(e) {
      e.preventDefault();
    });
})();
