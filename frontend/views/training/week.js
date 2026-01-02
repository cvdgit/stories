(function () {
  const detailModal = new RemoteModal({id: 'week-detail-modal', title: 'Детализация', dialogClassName: 'modal-xl'})
  $('.history-content').on('click', '.detail-modal', e => {
    e.preventDefault()
    detailModal.show({
      url: e.target.getAttribute('href'),
      callback: function (response, status, xhr) {
        if (status === 'error') {
          $(this).text(xhr.responseText)
          return
        }
      }
    })
  })
})();
