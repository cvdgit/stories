(function() {

  const modal = new RemoteModal({
    id: 'slide-import-modal',
    title: 'Импортировать слайды',
    dialogClassName: 'modal-lg'
  });

  $('#slide-import').on('click', function(e) {
    e.preventDefault();

    modal.show({
      url: $(this).attr('href'),
      callback: function() {

      }
    })
  });
})();
