(function() {
  const mf = document.getElementById("formula");
  const latex = document.getElementById("latex");

  mf.addEventListener("input",(ev) => {
    latex.value = mf.value
  });

  latex.value = mf.value;

  latex.addEventListener("input", (ev) => {
    mf.setValue(
      ev.target.value,
      {silenceNotifications: true}
    );
  });

  document.getElementById('add-placeholder').addEventListener('click', e => {
    const id = uuidv4().split('-')[0]
    mf.insert(`\\placeholder[${id}]{}`)
    $list.append(createListRow({
      id: null,
      name: '',
      placeholder: id
    }))
  })

  const $form = $('#math-question-form')
  attachBeforeSubmit($form[0], (form) => {

    const formData = new FormData()
    formData.append('name', $('.mathName').val())

    const job = latex.value
    if (!job) {
      toastr.warning('Необходимо заполнить задание')
      return
    }
    formData.append('job', job)

    const answers = []
    let noValue = false
    $list.find('.gap-row').each((i, el) => {
      const $row = $(el)
      const name = $row.find(`input[name='placeholder']`).val()
      const value = $row.find('math-field')[0].value
      if (!value) {
        noValue = true
        return
      }
      answers.push({
        id: $row.attr('data-id'),
        placeholder: name,
        value,
        correct: true
      })
    })

    if (!answers.length || noValue) {
      toastr.warning('Необходимо добавить варианты ответов')
      return
    }

    formData.append('answers', JSON.stringify(answers))

    sendForm($(form).attr('action'), $(form).attr('method'), formData)
      .done((response) => {
        if (response.url) {
          location.replace(response.url);
        }
        else {
          toastr.success('Успешно');
          location.reload()
        }
      })
  });

  const mathAnswers = [...(window.mathAnswers || [])]
  const $list = $('#gaps-list')

  function createListRow({id, name, placeholder} = {}) {
    return $(`
<div class="gap-row" data-id="${id || ''}" style="display: flex; flex-direction: row; column-gap: 10px">
<div style="flex: 1">
<input name="placeholder" type="text" value="${placeholder || ''}" />
</div>
<div style="flex: 1">
<math-field style="display: block">${name || ''}</math-field>
</div>
<div style="width: 100px; display: flex; align-items: center; justify-content: center">
<a title="Удалить вариант ответа" class="answer-remove" href=""><i class="glyphicon glyphicon-trash"></i></a>
</div>
</div>`)
  }

  /*
  async function removeAnswer({questionId, answerId}) {
    const response = await fetch(`/admin/index.php?r=test/math/remove-answer`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify({
        questionId,
        answerId,
      })
    })
    if (!response.ok) {
      throw new Error('Remove answer error')
    }
    return await response.json()
  }

  $list.on('click', '.answer-remove', e => {
    e.preventDefault()
    const $row = $(e.target).parents('.answer-row')
    const value = $row.find('math-field')[0].value
    if (value && !confirm('Подтверждаете?')) {
      return
    }
    const id = $row.attr('data-id')
    if (id) {
      const answer = mathAnswers.find(a => a.id === id)
      removeAnswer({questionId: answer.questionId, answerId: answer.id})
    }

    $row.remove()
  })

  const isInputAnswer = $('.inputAnswerCheck').is(':checked')

  if (isInputAnswer) {
    $('.inputAnswerCheckValue')
      .attr('data-answer-id', mathAnswers[0].id)
      .val(mathAnswers[0].name)
  } else {

  }

  */

  mathAnswers.map(a => {
    $list.append(createListRow(a))
  })

  $('#add-gap').on('click', e => {
    e.preventDefault()
    $list.append(createListRow())
  })
})()
