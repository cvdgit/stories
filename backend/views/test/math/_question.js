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

    const isInputAnswer = $('.inputAnswerCheck').is(':checked')
    formData.append('inputAnswer', isInputAnswer ? 1 : 0)

    const answers = []
    if (isInputAnswer) {
      const inputAnswerValue = $('.inputAnswerCheckValue').val().trim()
      if (inputAnswerValue === '') {
        toastr.warning('Необходимо указать ответ')
        return
      }
      //formData.append('inputAnswerValue', inputAnswerValue)
      answers.push({
        id: $('.inputAnswerCheckValue').attr('data-answer-id'),
        value: inputAnswerValue,
        correct: true
      })
    } else {
      $list.find('math-field').each((i, el) => {
        const value = el.value
        const $row = $(el).parent()
        if (value) {
          answers.push({
            id: $row.attr('data-id'),
            value,
            correct: $row.find('.answer-correct').is(':checked')
          })
        }
      })
    }

    if (!answers.length) {
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
          //location.reload()
        }
      })
  });

  const mathAnswers = [...(window.mathAnswers || [])]
  const $list = $('#answer-list')

  function createListRow({id, name, correct} = {}) {
    return $(`
<div class="answer-row" data-id="${id || ''}" style="display: flex; flex-direction: row; column-gap: 10px">
<math-field style="flex: 1">${name || ''}</math-field>
<div style="width: 100px; display: flex; align-items: center; justify-content: center"><label>Верный <input class="answer-correct" type="checkbox" ${correct ? "checked" : ""}></label></div>
<div style="width: 100px; display: flex; align-items: center; justify-content: center">
<a title="Удалить вариант ответа" class="answer-remove" href=""><i class="glyphicon glyphicon-trash"></i></a>
</div>
</div>`)
  }

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
    mathAnswers.map(a => {
      $list.append(createListRow(a))
    })
  }

  $('#add-answer').on('click', e => {
    e.preventDefault()
    $list.append(createListRow())
  })

  const $inputAnswerBlock = $('#input-answer-block')
  const $answersBlock = $('#answers-block')
  $('.inputAnswerCheck').on('click', e => {
    $inputAnswerBlock.hide()
    $answersBlock.hide()
    if (e.target.checked) {
      $inputAnswerBlock.show()
      return
    }
    $answersBlock.show()
  })
})()
