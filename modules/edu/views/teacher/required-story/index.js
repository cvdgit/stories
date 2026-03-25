(function () {

  function splitNumber(total, parts) {
    if (!Number.isInteger(total) || !Number.isInteger(parts)) {
      throw new TypeError('total and parts must be integers');
    }
    if (parts <= 0) {
      throw new RangeError('parts must be > 0');
    }
    const base = Math.floor(total / parts);
    const remainder = total % parts;
    const result = [];
    for (let i = 0; i < parts; i++) {
      result.push(base + (i < remainder ? 1 : 0));
    }
    return result;
  }

  async function reloadMetadata(storyId) {
    const response = await window.Api.get(`/edu/teacher/required-story/get-story-contents-total?storyId=${storyId}`);
    const total = Number(response?.total);
    if (isNaN(total) || total === 0) {
      throw new Error('No story items');
    }
    const days = Number($('.required-story-days').val());
    if (isNaN(days) || days === 0) {
      throw new Error('Days error');
    }
    const metadata = {
      total: Number(response?.total),
      chunks: splitNumber(total, days).map(n => ({n})),
    };
    $('.required-story-metadata').val(JSON.stringify(metadata));
    $('#metadata-container')
      .empty()
      .append(drawMetadata(metadata));
  }

  window.requiredSelectStory = async (storyId) => {
    if (!storyId) {
      return;
    }
    await reloadMetadata(storyId);
  }

  function drawMetadata(metadata) {
    const $elem = $('<div/>')
      .append(`<p>Всего вопросов в истории: <b>${metadata.total}</b> <button class="reload-meta" title="Обновить" type="button"><i class="glyphicon glyphicon-refresh"></i></button></p>`)
      .append(`
<p>
Учитываются:
<ul>
<li>Слайды с информацией</li>
<li>Вопросы в тестах</li>
<li>Фрагменты ментальных карт</li>
<li>Фрагменты обязательных ментальных карт речевого тренажера</li>
<li>Пересказы</li>
</ul>
</p>
`);

    $elem.append('<p>Распределение по дням:</p>');

    metadata.chunks.map((chunk, i) => {
      $elem.append(
        `<p>День #${++i} - <strong>${chunk.n}</strong> вопросов</p>`
      );
    });

    $elem.find('.reload-meta').on('click', async () => {
      try {
        await reloadMetadata($('.storyIdValue').find('option:selected').val());
        toastr.success('Успешно');
      } catch (ex) {
        toastr.error(ex.message);
      }
    })

    return $elem;
  }

  $('#required-stories-wrap')
    .on('click', '.required-story-edit', e => {
      e.preventDefault();
      const dialog = new RemoteModal({
        id: 'required-story-edit-modal',
        title: 'Редактировать',
        dialogClassName: 'modal-lg'
      });
      dialog.show({
        url: $(e.target).attr('href'),
        callback: function() {

          const metadata = window.requiredStoryMetadata;
          $(this).find('#metadata-container').append(
            drawMetadata(metadata)
          );

          $(this).find('.required-story-days').on('input', e => {
            const metadata = JSON.parse($('.required-story-metadata').val());
            metadata.chunks = splitNumber(metadata.total, Number(e.target.value)).map(n => ({n}));
            $('.required-story-metadata').val(JSON.stringify(metadata));
            $(this).find('#metadata-container')
              .empty()
              .append(
                drawMetadata(metadata)
              );
          });

          const $form = $(this).find('form');
          attachBeforeSubmit($form[0], async (form) => {
            const formData = new FormData(form);
            const response = await sendForm(formData, $form.attr('action'), $form.attr('method'));
            if (!response.success) {
              toastr.error(response.message);
              return;
            }
            dialog.hide();
            $.pjax.reload('#pjax-required-stories', {timeout: 3000});
          });
        }
      })
    })
    .on('click', '.required-story-delete', async (e) => {
      e.preventDefault();
      if (!confirm('Подтверждаете?')) {
        return;
      }
      const response = await window.Api.get($(e.target).attr('href'));
      if (response.success) {
        toastr.success('Успешно');
        $.pjax.reload('#pjax-required-stories', {timeout: 3000});
        return;
      }
      toastr.error(response.message || 'Ошибка');
    })
    .on('click', '.required-story-stat', e => {
      const dialog = new RemoteModal({
        id: 'required-story-sessions-modal',
        title: 'Сессии ученика',
        dialogClassName: 'modal-lg'
      });
      dialog.show({
        url: $(e.target).attr('data-required-story-url'),
        callback: function() {}
      });
    });

  $('.required-story-create').on('click', e => {
    e.preventDefault();
    const dialog = new RemoteModal({
      id: 'required-story-create-modal',
      title: 'Создать обязательную историю',
      dialogClassName: 'modal-lg'
    });
    dialog.show({
      url: $(e.target).attr('href'),
      callback: function() {

        $(this).find('.required-story-days').on('input', e => {
          const metadata = JSON.parse($('.required-story-metadata').val());
          metadata.chunks = splitNumber(metadata.total, Number(e.target.value)).map(n => ({n}));
          $('.required-story-metadata').val(JSON.stringify(metadata));
          $(this).find('#metadata-container')
            .empty()
            .append(
              drawMetadata(metadata)
            );
        });

        const $form = $(this).find('form');
        attachBeforeSubmit($form[0], async (form) => {
          const formData = new FormData(form);
          const response = await sendForm(formData, $form.attr('action'), $form.attr('method'));
          if (!response.success) {
            toastr.error(response.message);
            return;
          }
          dialog.hide();
          $.pjax.reload('#pjax-required-stories', {timeout: 3000});
        });
      }
    });
  });
})();
