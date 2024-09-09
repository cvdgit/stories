(function() {
  const $modal = $('#changelog-modal')
  $('#changelogs').on('click', '.changelog-item', e => {
    e.preventDefault()
    const url = $(e.target).closest('a.changelog-item').attr('href')
    $modal.modal({'remote': url});
  });
})();
