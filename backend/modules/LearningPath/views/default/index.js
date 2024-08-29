(function() {
  const modal = new RemoteModal({
    id: 'learning-path-create-modal',
    title: 'Новая запись'
  });

  $("#create-learning-path").on("click", function(e) {
    e.preventDefault();
    modal.show({
      url: $(this).attr('href'),
      callback: () => {}
    })
  })
})()
