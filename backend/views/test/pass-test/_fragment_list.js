(function() {
  const createDialog = new RemoteModal({id: 'create-list-modal', title: 'Новый список'});
  const createCallback = function () {
    const formElement = document.getElementById('create-list-form');
    attachBeforeSubmit(formElement, (form) => {
      sendForm($(form).attr('action'), $(form).attr('method'), new FormData(form))
        .done((response) => {
          if (response) {
            if (response.success) {
              toastr.success(response.message);
              createDialog.hide();
            } else {
              toastr.error(response.message);
            }
          } else {
            toastr.error('Неизвестная ошибка');
          }
        })
    });
  };
  $('#create-fragment-list').on('click', function (e) {
    e.preventDefault();
    createDialog.show({url: $(this).attr('href'), callback: createCallback});
  });

  const selectDialog = new RemoteModal({
    id: 'select-list-modal',
    title: 'Выбрать из списка',
    dialogClassName: 'modal-lg'
  });

  const templateElement = document.createElement("span");
  templateElement.className = "dropdown";
  templateElement.setAttribute('contenteditable', 'false');
  templateElement.innerHTML = '<button class="btn btn-default dropdown-toggle highlight" data-toggle="dropdown"></button><ul class="dropdown-menu"></ul>';

  const dataWrapper = window.dataWrapper;

  function SelectedItems() {
    let selectedIds = [];
    return {
      exists(id) {
        return selectedIds.some(item => parseInt(item.id) === parseInt(id));
      },
      add(item) {
        selectedIds.push(item);
      },
      del(id) {
        selectedIds = selectedIds.filter(item => parseInt(item.id) !== parseInt(id));
      },
      getAll() {
        return selectedIds;
      },
      isEmpty() {
        return selectedIds.length === 0;
      },
      reset() {
        selectedIds = [];
      }
    }
  }

  const selectedItems = new SelectedItems();

  const selectCallback = function(fragmentId) {

    const $content = $(this);

    $content.off('click').on('click', '#create-fragment-list', function(e) {

      if (selectedItems.isEmpty()) {
        toastr.info('Итоговый список пуст');
        return;
      }

      selectedItems.getAll().forEach(item => {
        dataWrapper.createFragmentItem(fragmentId, {
          id: generateUUID(),
          title: item.title,
          correct: false
        });
      });

      selectedItems.reset();

      selectDialog.hide();
    });

    $content.on('click', ' #all-items-list .add-items', function(e) {

      $(this).parent().find('.items .list-item').each((i, item) => {

        const itemId = parseInt($(item).attr('data-item-id'));
        if (selectedItems.exists(itemId)) {
          return;
        }

        selectedItems.add({id: itemId, title: $(item).text()});

        const elem = $('<li/>', {class: 'list-group-item selected-item', 'data-item-id': itemId})
          .append('<p>' + $(item).text() + '</p>');

        elem.append(
          $('<i/>', {class: 'glyphicon glyphicon-trash selected-del'})
            .on('click', function(e) {
              $(this).parent().remove();
              selectedItems.del(itemId);
            })
        );

        $content.find('#selected-items-list').append(elem);
      });
    });

    $content.find('#lists-filter-form input[type=checkbox]').on('click', function() {
      selectedItems.reset();
      $.pjax.reload({
        container: '#pjax-lists',
        replace: false,
        url: $content.find('#lists-filter-form').attr('action'),
        method: 'post',
        data: $content.find('#lists-filter-form').serialize()
      });
    });
  };

  $('#select-fragment-list').on('click', function(e) {
    e.preventDefault();

    if (!window.getSelection) {
      toastr.error('window.getSelection error');
      return;
    }

    const selection = window.getSelection();
    if (selection.toString().length === 0) {
      toastr.info('Необходимо выделить фрагмент текста');
      return;
    }

    if (selection.isCollapsed) {
      return;
    }

    const selText = selection.toString();
    const skipTrim = (selText.length === 1) && (selText === ' ');
    if (!skipTrim) {
      trimRanges(selection);
    }

    const ranges = [];
    for (let i = 0, len = selection.rangeCount; i < len; ++i) {
      ranges.push(selection.getRangeAt(i));
    }
    selection.removeAllRanges();

    let range;
    i = ranges.length;
    const elementId = generateUUID();
    while (i--) {

      range = ranges[i];

      /*surroundRangeContents(range, templateElement, function(element, textNode) {

        dataWrapper.createFragment(elementId);
        element.setAttribute('data-fragment-id', elementId);

        if (textNode.textContent === ' ') {
          textNode.textContent = '\u00A0';
        }

        dataWrapper.createFragmentItem(elementId, {
          id: generateUUID(),
          title: textNode.textContent,
          correct: true
        });

        element.querySelector('.dropdown-toggle').appendChild(textNode);
      });*/

      surroundRangeContents(range, (textNodes) => {

        const element = fragmentElementBuilder('single').cloneNode(true);
        textNodes[0].parentNode.insertBefore(element, textNodes[0]);

        let textContent = '';
        for (let i = 0, node; node = textNodes[i++];) {
          element.appendChild(node);
          textContent += node.nodeType === 3 ? node.textContent : node.outerHTML;
          element.querySelector('.dropdown-toggle').appendChild(node);
        }

        dataWrapper.createFragment(elementId);
        element.setAttribute('data-fragment-id', elementId);

        if (textNodes[0].textContent === ' ') {
          textNodes[0].textContent = '\u00A0';
        }

        dataWrapper.createFragmentItem(elementId, {
          id: generateUUID(),
          title: textContent,
          correct: true
        });

      });

      selection.addRange(range);
    }

    console.log(elementId)

    selectDialog.show({
      url: $(this).attr('href'),
      callback: function() {
        selectCallback.bind(this)(elementId);
      }
    });
  });
})();
