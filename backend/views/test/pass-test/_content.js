
(function() {

  function Fragments() {

    let values = {
      'content' : '',
      'fragments': []
    };

    this.loadData = data => {
      if (data) {
        values = data;
      }
    };

    this.createFragment = (id) => {
      values.fragments.push({id, items: []});
      return id;
    }

    this.findFragment = (id) => {
      return values.fragments.filter((fragment) => fragment.id === id)[0];
    };
    this.findFragmentItem = (fragment_id, item_id) => {
      const fragment = this.findFragment(fragment_id);
      return fragment.items.filter(item => {
        return item.id === item_id;
      })[0];
    };

    this.createFragmentItem = (id, newItem) => {
      const fragment = this.findFragment(id);
      const item = {
        ...newItem,
        order: values.fragments.length + 1
      };
      fragment.items.push(item);
      return item;
    }

    this.getFragments = () => values.fragments;
    this.getFragmentItems = (id) => {
      const fragment = this.findFragment(id);
      return fragment.items;
    };

    this.getFragmentCorrectItem = (id) => {
      const fragment = this.findFragment(id);
      return fragment.items.filter(item => {
        return item.correct;
      })[0];
    };

    this.setFragmentCorrectItem = (fragment_id, item_id) => {
      const fragment = this.findFragment(fragment_id);
      fragment.items.map((item) => {
        item.correct = false;
      });
      const item = fragment.items.filter(item => {
        return item.id === item_id;
      })[0];
      item.correct = true;
      return item;
    };

    this.removeFragmentItem = (fragment_id, item_id) => {
      const fragment = this.findFragment(fragment_id);
      fragment.items = fragment.items.filter(item => {
        return item.id !== item_id;
      });
    }

    this.getContent = () => values.content;
    this.setContent = (content) => values.content = content;

    this.getPayload = () => values;
  }

  Fragments.prototype.initFragments = function() {

    let content = this.getContent();

    this.getFragments().forEach(fragment => {

      const code = $('<span/>', {
        'class': 'dropdown',
        'contenteditable': false,
        'data-fragment-id': fragment.id
      });

      const correctItem = this.getFragmentCorrectItem(fragment.id);

      let title = 'ПУСТО';
      if (correctItem) {
        title = correctItem.title;
      }
      code.append('<button class="btn btn-default dropdown-toggle highlight" data-toggle="dropdown">' + title + '</button><ul class="dropdown-menu"></ul>');

      const reg = new RegExp('{' + fragment.id + '}');
      content = content.replace(reg, code[0].outerHTML);
    });

    if (content.length === 0) {
      content = '<div><br></div>';
    }

    return content;
  }

  window['dataWrapper'] = (function(dataWrapper) {
    return dataWrapper;
  })(new Fragments());

  const dataWrapper = window.dataWrapper;

  const questionId = parseInt($('#content').attr('data-question-id'));
  if (questionId) {
    $.getJSON(`/admin/index.php?r=test/pass-test/payload&id=${questionId}`)
      .done(response => {
        dataWrapper.loadData(response);
        let content = dataWrapper.initFragments();
        $('#content').html(content);
      });
  }

  document.getElementById('content').addEventListener('paste', function(e) {
    e.preventDefault();
    var text = (e.originalEvent || e).clipboardData.getData('text/plain');
    document.execCommand("insertHTML", false, text);
  });

  const observer = new MutationObserver((mutationRecords) => {
    const content = $('#content').html();
    const el = $('<div>' + content + '</div>');
    el.find('span[data-fragment-id]').replaceWith(function() {
      return '{' + $(this).attr('data-fragment-id') + '}';
    });
    dataWrapper.setContent(el[0].innerHTML);
  });
  observer.observe($('#content')[0], {
    subtree: true,
    characterData: true
  });

  function fragmentItemTextChanged(fragment_id, item_id, text) {
    const item = dataWrapper.findFragmentItem(fragment_id, item_id);
    if (item.correct) {
      $('#content').find('span[data-fragment-id=' + fragment_id + '] > .highlight').text(text);
    }
    item.title = text;
  }

  function fragmentItemSetCorrect(fragment_id, item_id) {
    const item = dataWrapper.setFragmentCorrectItem(fragment_id, item_id);
    $('#content').find('span[data-fragment-id=' + fragment_id + '] > .highlight').text(item.title);
  }

  function fragmentItemRemove(elem, fragment_id, item_id) {

    const item = dataWrapper.findFragmentItem(fragment_id, item_id);
    if (item.correct) {
      $('#content').find('span[data-fragment-id=' + fragment_id + '] > .highlight').text('ПУСТО');
    }

    dataWrapper.removeFragmentItem(fragment_id, item_id);
    $(elem).parent().parent().hide().remove();
  }

  function addFragmentItem(fragment_id) {

    const item = dataWrapper.createFragmentItem(fragment_id, {
      id: generateUUID(),
      correct: false,
      title: ''
    });

    const elem = createFragmentItemElement(fragment_id, item);

    const createElem = $('#content')
      .find('span[data-fragment-id=' + fragment_id + '] > .dropdown-menu .divider');

    elem.insertBefore(createElem)
      .find('.fragment-title__edit')
      .focus();
  }

  function createFragmentItemElement(fragment_id, item) {
    return $('<li/>', {class: 'fragment-item'})
      .append(
        $('<span/>', {class: 'fragment-input'})
          .append(
            $('<input/>', {
              name: fragment_id,
              type: 'radio',
              checked: item.correct
            })
              .on('click', function() {
                fragmentItemSetCorrect(fragment_id, item.id);
              })
          )
      )
      .append(
        $('<span/>', {'class': 'fragment-title'})
          .append(
            $('<a/>', {'href': '#', 'contenteditable': true, class: 'fragment-title__edit'})
              .on('input', function() {
                fragmentItemTextChanged(fragment_id, item.id, $(this).text());
              })
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
              .text(item.title)
          )
      )
      .append(
        $('<span/>', {class: 'fragment-action'})
          .append(
            $('<a/>', {'href': '#'})
              .html('<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>')
              .on('click', function(e) {
                e.preventDefault();
                fragmentItemRemove(this, fragment_id, item.id);
                return false;
              })
          )
      );
  }

  $('#content')
    .on('hide.bs.dropdown', '.dropdown', function() {
      $(this).find('.dropdown-menu').empty();
    })
    .on('show.bs.dropdown', '.dropdown', function() {

      const fragment_id = $(this).attr('data-fragment-id');
      const menu = $(this).find('.dropdown-menu');
      menu.empty();

      dataWrapper.getFragmentItems(fragment_id).forEach(function(item) {
        const elem = createFragmentItemElement(fragment_id, item);
        elem.appendTo(menu);
      });

      $('<li/>', {class: 'divider', role: 'separator'})
        .appendTo(menu);

      $('<li/>').append(
        $('<a/>', {'href': '#', 'class': 'add-word'})
          .text('Добавить слово')
          .on('click', function(e) {
            e.preventDefault();
            addFragmentItem(fragment_id);
          })
      ).appendTo(menu);

      $('<li/>').append(
        $('<a/>', {'href': '#', 'class': 'add-word'})
          .text('Удалить пропуск')
          .on('click', function(e) {
            e.preventDefault();

          })
      ).appendTo(menu);
    });

  $('#content').on('click', '.dropdown-menu', function(e) {
    e.stopPropagation();
  });

  const form = $('#pass-test-form');

  form.on('beforeValidate', function() {

    $('#passtestform-content').val($('#content').text());

    const el = $('<div>' + $('#content').html() + '</div>');

    el.find('span[data-fragment-id]').replaceWith(function() {
      return '{' + $(this).attr('data-fragment-id') + '}';
    });

    el.find('span.search-fragment').replaceWith(function() {
      return $(this).text();
    });

    const content = el[0].outerHTML;

    const fragments = [];
    $('#content').find('[data-fragment-id]').each(function(index, elem) {
      const fragmentId = elem.getAttribute('data-fragment-id');
      const fragment = dataWrapper.findFragment(fragmentId);
      if (fragment) {
        fragment.items = fragment.items.map(item => {
          item.title = item.title.toString().trim();
          return item;
        });
        fragments.push(fragment);
      }
    });

    const payload = dataWrapper.getPayload();
    payload.content = content;
    payload.fragments = fragments;
    $('#passtestform-payload').val(JSON.stringify(payload));
  });

  function yiiFormSubmit(form, beforeCallback) {

    function btnLoading(elem) {
      $(elem).attr("data-original-text", $(elem).html());
      $(elem).prop("disabled", true);
      $(elem).html('<i class="spinner-border spinner-border-sm"></i> Loading...');
    }

    function btnReset(elem) {
      $(elem).prop("disabled", false);
      $(elem).html($(elem).attr("data-original-text"));
    }

    form.on('beforeSubmit', function(e) {
      e.preventDefault();
      var btn = $(this).find('button[type=submit]');
      btnLoading(btn);
      if (typeof beforeCallback === 'function') {
        beforeCallback(this).always(function () {
          btnReset(btn);
        });
      }
      return false;
    })
      .on('submit', function(e) {
        e.preventDefault();
      });
  }

  const submitCallback = function(form) {
    const formData = new FormData(form);
    return $.ajax({
      url: $(form).attr('action'),
      type: $(form).attr('method'),
      data: formData,
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false
    })
      .done(doneCallback)
      .fail(failCallback);
  }

  const doneCallback = function(response) {
    if (response && response.success) {
      if (response.url) {
        location.replace(response.url);
      }
      else {
        toastr.success('Успешно');
      }
    }
    else {
      toastr.error(response['message'] || 'Неизвестная ошибка');
    }
  };

  const failCallback = function(response) {
    toastr.error(response.responseJSON.message);
  }

  yiiFormSubmit(form, submitCallback);
})();

