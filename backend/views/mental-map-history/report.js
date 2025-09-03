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

  async function fetchFragmentLog(url) {
    const response = await fetch(url, {
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
            $row.find('.fragment-ratio').text(parseFloat(r.userIds.split(',').length / r.fragmentsCount, 1).toFixed(1))
          })
        }
    })
  })

  $('table[data-map-id]')
    .on('click', '.show-detail', e => {
      e.preventDefault()
      const modal = RemoteModal({id: 'detail-modal', 'title': 'Detail', dialogClassName: 'modal-lg'})
      modal.show({
        url: e.target.getAttribute('href'),
        callback: function() {
          $(this).on('click', '.show-log', e => {
            e.preventDefault()
            fetchFragmentLog(e.target.getAttribute('href'))
              .then(response => {
                if (response && response.success) {
                  const $row = $(e.target).parents('tr:eq(0)')
                  if ($row.next('tr.detail-row').length) {
                    $row.next('tr.detail-row').remove()
                  }
                  const $detailRow = $(`<tr class="detail-row"><td colspan="${$row.find('td').length}"><div class="detail-rows"></div></td></tr>`)
                  response.rows.map(row => {
                    $detailRow.find('.detail-rows').append(
                      `<div class="detail-row-item"><div style="font-size: 14px">${row.created_at}</div><div style="flex: 1"><pre><b>Запрос:</b> ${row.input}</pre></div><div style="flex: 1"><pre><b>Ответ:</b> ${row.output}</pre></div></div>`
                    )
                  })
                  $row.after($detailRow)
                }
              })
          })
        }
      })
    })
})();
