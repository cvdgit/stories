(function() {

  const modal = new RemoteModal({
    id: 'questions-import-modal',
    title: 'Импортировать вопросы',
    dialogClassName: 'modal-lg'
  });

  $('#questions-import').on('click', function(e) {
    e.preventDefault();

    modal.show({
      url: $(this).attr('href'),
      callback: function() {

      }
    })
  });
})();
