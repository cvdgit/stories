(function() {
  const modal = RemoteModal({id: 'detail-modal', 'title': 'Detail', dialogClassName: 'modal-lg'})
  $('.mental-map-table').on('click', '.show-detail', (e) => {
    e.preventDefault()
    modal.show({url: e.target.getAttribute('href')})
  })
})()
