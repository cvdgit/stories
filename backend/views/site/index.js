(function() {
  const $modal = $('#changelog-modal')
  $modal.on('hide.bs.modal', function() {
    $(this).removeData('bs.modal');
    $(this).find('.modal-content').html('');
  });
  $('#changelogs').on('click', '.changelog-item', e => {
    e.preventDefault()
    const url = $(e.target).closest('a.changelog-item').attr('href')
    $modal.modal({'remote': url});
  });
})();
