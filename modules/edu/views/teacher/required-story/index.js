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
    if (!selectedStudentId) {
      selectedStudentId = $('.required-story-student-id').val();
    }
    if (!selectedStudentId) {
      throw new Error('Student id not found');
    }
    const response = await window.Api.get(`/edu/teacher/required-story/get-story-contents-total?storyId=${storyId}&studentId=${selectedStudentId}`);
    const total = Number(response?.total);
    if (isNaN(total) || total === 0) {
      throw new Error('No story items');
    }
    const days = Number($('.required-story-days').val());
    if (isNaN(days) || days === 0) {
      throw new Error('Days error');
    }

    $('#studentFactAnswersWrap').show();
    const studentFactAnswers = Number(response?.fact);
    $('#studentFactAnswers').text(studentFactAnswers);

    const useStudentAnswers = $('#useStudentFactAnswers').is(':checked');
    let answersTotal = total;
    if (useStudentAnswers) {
      answersTotal -= studentFactAnswers;
    }

    const metadata = {
      total,
      chunks: splitNumber(answersTotal, days).map(n => ({n})),
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

  let selectedStudentId;
  window.requiredSelectStudent = async (studentId) => {
    selectedStudentId = studentId;
  }

  function drawMetadata(metadata) {
    const $elem = $('<div/>')
      .append(`<p>Всего вопросов в истории: <b style="margin-right: 10px">${metadata.total}</b> <button class="reload-meta" title="Обновить" type="button"><i class="glyphicon glyphicon-refresh"></i></button></p>`)
      .append(`
<p>
Учитываются:
<ul style="margin-bottom: 30px">
<li style="margin-bottom: 6px">Слайды с информацией</li>
<li style="margin-bottom: 6px">Вопросы в тестах</li>
<li style="margin-bottom: 6px">Фрагменты ментальных карт</li>
<li style="margin-bottom: 6px">Фрагменты обязательных ментальных карт речевого тренажера</li>
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

  function changeDaysEventHandler($container, useStudentFact, days) {
    const metadata = JSON.parse($container.find('.required-story-metadata').val());
    const studentFactAnswers = Number($container.find('#studentFactAnswers').text());

    let total = metadata.total;
    if (useStudentFact) {
      total -= studentFactAnswers;
    }

    metadata.chunks = splitNumber(total, Number(days)).map(n => ({n}));

    $container.find('.required-story-metadata').val(JSON.stringify(metadata));

    $container.find('#metadata-container')
      .empty()
      .append(
        drawMetadata(metadata)
      );
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

          $(this).find('.required-story-days').on(
            'input',
            e => changeDaysEventHandler(
              $(this),
              $(this).find('#useStudentFactAnswers').is(':checked'),
              e.target.value
            )
          );

          $(this).find('#useStudentFactAnswers').on(
            'change',
              e => changeDaysEventHandler(
                $(this),
                e.target.checked,
                $(this).find('.required-story-days').val()
              )
          );

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

        $(this).find('.required-story-days').on(
          'input',
          e => changeDaysEventHandler(
            $(this),
            $(this).find('#useStudentFactAnswers').is(':checked'),
            e.target.value
          )
        );

        $(this).find('#useStudentFactAnswers').on(
          'change',
          e => changeDaysEventHandler(
            $(this),
            e.target.checked,
            $(this).find('.required-story-days').val()
          )
        );

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
