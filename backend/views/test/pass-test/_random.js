(function() {
  $('#random').on('click', function(e) {

    e.preventDefault();

    const el = $('<div>' + $('#content').html() + '</div>');
    el.find('span[data-fragment-id]').replaceWith(function() {
      return '{' + $(this).attr('data-fragment-id') + '}';
    });

    const content = el[0].outerText;

    $.ajax({
      url: '/admin/index.php?r=fragment-list/random',
      type: 'post',
      data: content,
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false
    })
      .done(response => {

        if (response && response.success) {

          const contentHtml = response.content;
          dataWrapper.setContent(contentHtml);

          response.fragments.map(f => {
            const fragmentId = dataWrapper.createFragment(f.id);
            f.items.map(i => {
              dataWrapper.createFragmentItem(fragmentId, i);
            })
          });

          let content = dataWrapper.initFragments();
          $('#content').html(content);
        }
      })
      .fail(response => toastr.error("Повторите запрос еще раз"))
  });
})();
