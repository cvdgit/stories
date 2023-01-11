(function() {
  $('#search').on('click', function(e) {
    e.preventDefault();

    const el = $('<div>' + $('#content').html() + '</div>');
    el.find('span[data-fragment-id]').replaceWith(function() {
      return '{' + $(this).attr('data-fragment-id') + '}';
    });

    const content = el[0].outerText;

    $.ajax({
      url: '/admin/index.php?r=fragment-list/search',
      type: 'post',
      data: content,
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false
    })
      .done(response => {
        if (response && response.success) {

          const {words} = response;

          $('#content-cache').empty();
          $('#content').clone().appendTo('#content-cache');

          $('#content-cache #content')
            .find('span[data-fragment-id]')
            .replaceWith(function() {
              return '{' + $(this).attr('data-fragment-id') + '}';
            });
          let contentHtml = $('#content-cache #content').html();
          $('#content-cache').empty();

          words.forEach(word => {
            const reg = new RegExp(`${word.word}`, 'igu');
            contentHtml = contentHtml.replace(reg, () => {
              return `<span class="btn btn-info search-fragment" data-list-id="${word.list_id}" contenteditable="false">${word.word}</span>`;
            });
          });

          dataWrapper.setContent(contentHtml);
          let content = dataWrapper.initFragments();
          $('#content').html(content);
          //$('#content').html(contentHtml);
        }
      });
  });

  const searchDialog = new RemoteModal({
    id: 'search-list-modal',
    title: 'Выбрать из списка',
    dialogClassName: 'modal-lg'
  });

  const dataWrapper = window.dataWrapper;

  $('#content').on('click', '.search-fragment', function(e) {

    const $item = $(this);
    const listId = $item.attr('data-list-id');

    searchDialog.show({
      url: `/admin/index.php?r=fragment-list/select-one&list_id=${listId}`,
      callback: function() {

        const $body = $(this);

        $(this).find('#delete-search-list').on('click', function(e) {
          $item.replaceWith($item.text());
          searchDialog.hide();
        });

        $(this).find('#create-search-list').on('click', function(e) {

          const elementId = generateUUID();
          dataWrapper.createFragment(elementId);

          $item.replaceWith(`<span data-fragment-id="${elementId}" class="dropdown" contenteditable="false"><button class="btn btn-default dropdown-toggle highlight" data-toggle="dropdown">${$item.text()}</button><ul class="dropdown-menu"></ul></span>`);

          $body.find('#selected-items-list li').each((i, elem) => {

            const title = $(elem).text().trim();

            dataWrapper.createFragmentItem(elementId, {
              id: generateUUID(),
              title,
              correct: title === $item.text()
            });
          });

          searchDialog.hide();
        });

      }
    });
  });
})();
