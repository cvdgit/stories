(function() {

  async function fetchMapReport(id) {
    const response = await fetch(`/admin/index.php?r=mental-map-history/map-report&id=${id}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
    })
    if (!response.ok) {
      throw new Error('Remove answer error')
    }
    return await response.json()
  }

  $('table[data-map-id]').each((i, elem) => {
    const id = $(elem).attr('data-map-id')
    fetchMapReport(id)
      .then(response => {
        $(elem).removeClass('pending')
        if (response && response.success) {
          response.rows.map(r => {
            const $row = $(elem).find(`tr[data-fragment-id=${r.fragmentId}]`)
            $row.find('.fragment-count')
              .html(`<a class="show-detail" href="/admin/index.php?r=mental-map-history/map-report-detail&map_id=${id}&fragment_id=${r.fragmentId}">${r.fragmentsCount}</a>`)
            $row.find('.fragment-correct').text(r.fragmentsCorrectCount)
            $row.find('.fragment-users').text(r.userNames)
          })
        }
    })
  })

  const modal = RemoteModal({id: 'detail-modal', 'title': 'Detail', dialogClassName: 'modal-lg'})
  $('table[data-map-id]').on('click', '.show-detail', e => {
    e.preventDefault()
    modal.show({url: e.target.getAttribute('href')})
  })
})();
