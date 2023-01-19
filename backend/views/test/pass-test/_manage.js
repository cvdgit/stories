(function() {

  const manageDialog = new RemoteModal({
    id: 'manage-modal',
    title: 'Управление списками',
    dialogClassName: 'modal-lg'
  });
  const manageCallback = function() {

    const $content = $(this);
    const $itemList = $content.find('#item-list');

    const $addWordButtonWrap = $content.find('#add-word-wrap');
    $addWordButtonWrap.find('.btn').on('click', function() {

      const listId = $itemList.attr('data-selected-list-id');

      $.getJSON(`/admin/index.php?r=manage-fragment-list/create&id=${listId}`)
        .done(response => {
          $itemList.append(addWordItem({
            id: response.id,
            name: response.name
          }));
        });
    });

    const addWordItem = (item) => {

      const {id, name} = item;

      const elem = $('<li/>', {class: 'list-group-item selected-item', 'data-item-id': id})
        .append('<div class="list-item-title" tabindex="-1" contenteditable="true">' + name + '</div>');

      elem.append(
        $('<i/>', {class: 'glyphicon glyphicon-trash selected-del'})
          .on('click', function(e) {
            if (!confirm('Подтверждаете?')) {
              return;
            }
            $(this).parent().remove();
          })
      );

      contentEditableWrap(elem.find('.list-item-title'))
        .on('change', (e) => {
          const val = $(e.target).text().trim();
          const formData = new FormData();
          formData.append('FragmentListItemForm[name]', val);
          sendForm(`/admin/index.php?r=manage-fragment-list/change-item-name&id=${id}`, 'post', formData)
            .done(response => {
              if (response.success) {
                toastr.success('Название успешно изменено');
              } else {
                $(e.target).text(response.name);
              }
            });
        });

      return elem;
    };

    $content
      .find('#all-items-list')
      .on('click', '.fragment-list-item', function(e) {

        $content
          .find('#all-items-list li.active')
          .removeClass('active');

        $(this)
          .addClass('active')
          .focus();

        const listId = $(this).attr('data-list-id');
        if ($itemList.attr('data-selected-list-id') === listId) {
          return;
        }

        $itemList.empty();
        $itemList.attr('data-selected-list-id', listId);
        $addWordButtonWrap.hide();

        $.getJSON(`/admin/index.php?r=manage-fragment-list/items&id=${listId}`)
          .done(response => {
            (response || []).forEach(item => {
              $itemList.append(addWordItem(item));
            });
            $addWordButtonWrap.show();
          });
      });

    $content
      .find('.list-title')
      .on('keydown', function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          return;
        }
        if (event.key !== ' ') {
          return;
        }
        event.preventDefault();
        document.execCommand("insertText", false, ' ');
      })
      .on('focus', (e) => {
        const elem = $(e.target);
        elem.data('before', elem.text());
      })
      .on('blur', (e) => {
        const elem = $(e.target);
        if (elem.data('before') !== elem.text()) {
          elem.data('before', elem.html());
          elem.trigger('change');
        }
      })
      .on('change', (e) => {

        const listId = $(e.target).parent().attr('data-list-id');

        const val = $(e.target).text().trim();
        const formData = new FormData();
        formData.append('FragmentListForm[name]', val);

        sendForm(`/admin/index.php?r=manage-fragment-list/change-list-name&id=${listId}`, 'post', formData)
          .done(response => {
            if (response.success) {
              toastr.success('Название списка успешно изменено');
            } else {
              $(e.target).text(response.name);
            }
          });
      });

    $content
      .find('.list-delete')
      .on('click', function(e) {

        if (!confirm('Подтверждаете?')) {
          return;
        }

        const $list = $(this).parent().parent();
        const listId = $list.attr('data-list-id');

        $.getJSON(`/admin/index.php?r=manage-fragment-list/remove-list&id=${listId}`)
          .done(response => {
            if (response.success) {
              $list.remove();
              $itemList.empty();
              $itemList.removeAttr('data-selected-list-id');
              $addWordButtonWrap.hide();
            }
          });
      });
  };

  const contentEditableWrap = (elem) => {
    return elem
      .on('keydown', function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          return;
        }
        if (event.key !== ' ') {
          return;
        }
        event.preventDefault();
        document.execCommand("insertText", false, ' ');
      })
      .on('focus', (e) => {
        const elem = $(e.target);
        elem.data('before', elem.text());
      })
      .on('blur', (e) => {
        const elem = $(e.target);
        if (elem.data('before') !== elem.text()) {
          elem.data('before', elem.html());
          elem.trigger('change');
        }
      });
  };

  $('#manage').on('click', function(e) {
    e.preventDefault();
    manageDialog.show({url: $(this).attr('href'), callback: manageCallback});
  });
})();
