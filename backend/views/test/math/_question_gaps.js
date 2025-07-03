(function() {

  /*const mf = document.getElementById("formula");
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
  });*/

  /*document.getElementById('add-placeholder').addEventListener('click', e => {
    const id = uuidv4().split('-')[0]
    mf.insert(`\\placeholder[${id}]{}`)
    $list.append(createListRow({
      id: null,
      name: '',
      placeholder: id
    }))
  })*/

  const $form = $('#math-question-form')
  attachBeforeSubmit($form[0], (form) => {

    const formData = new FormData()

    const name = $('.mathName').val()
    if (!name) {
      toastr.warning('Необходимо указать вопрос')
      return
    }
    formData.append('name', name)

    const content = quill.container.firstChild.innerHTML
    if (!content) {
      toastr.warning('Необходимо заполнить задание')
      return
    }
    formData.append('job', content)

    const toDeleteFragmentIds = []
    fragmentsManager.getFragments().map(f => {
      if (!$(content).find(`math-field[data-id=${f.id}]`).length) {
        toDeleteFragmentIds.push(f.id)
      }
    })
    toDeleteFragmentIds.map(id => fragmentsManager.deleteFragment(id))

    const answers = []
    fragmentsManager.getFragments().map(f => {
      if (!f.placeholders.length || !f.value) {
        return
      }
      f.placeholders.map(p => {
        answers.push({
          answer_id: p.answerId,
          placeholder: p.id,
          value: p.value,
          correct: true
        })
      })
    })

    if (!answers.length) {
      toastr.warning('Необходимо добавить варианты ответов')
      return
    }

    //formData.append('answers', JSON.stringify(answers))
    formData.append('fragments', JSON.stringify(fragmentsManager.getFragments()))

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

  const mathFragments = [...(window.mathFragments || [])]
  const fragmentsManager = new FragmentsManager(mathFragments)

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

  function FragmentsManager(initFragments = []) {

    let fragments = initFragments

    const findFragment = id => fragments.find(f => f.id === id)

    return {
      addFragment(id, value, placeholders) {
        fragments.push({
          id,
          value,
          placeholders
        })
        return {...findFragment(id)}
      },
      updateFragment(id, value, placeholders) {
        let fragment = findFragment(id)
        if (!fragment) {
          throw new Error('Fragment not found')
        }
        fragment.value = value
        fragment.placeholders = placeholders.map(p => ({...fragment.placeholders.find(exists => exists.id === p.id), ...p}))
      },
      addPlaceholder(id, placeholder) {
        let fragment = findFragment(id)
        if (!fragment) {
          throw new Error('Fragment not found')
        }
        fragment.placeholders.push(placeholder)
      },
      getFragments() {
        return [...fragments]
      },
      getFragment(id) {
        return {...findFragment(id)}
      },
      deleteFragment(id) {
        fragments = fragments.filter(f => f.id !== id)
      }
    }
  }

  const icons = window.Quill.import('ui/icons');
  icons['math'] = `<svg viewbox="0 0 18 18"><path class="ql-fill" d="M11.759,2.482a2.561,2.561,0,0,0-3.53.607A7.656,7.656,0,0,0,6.8,6.2C6.109,9.188,5.275,14.677,4.15,14.927a1.545,1.545,0,0,0-1.3-.933A0.922,0.922,0,0,0,2,15.036S1.954,16,4.119,16s3.091-2.691,3.7-5.553c0.177-.826.36-1.726,0.554-2.6L8.775,6.2c0.381-1.421.807-2.521,1.306-2.676a1.014,1.014,0,0,0,1.02.56A0.966,0.966,0,0,0,11.759,2.482Z"/><rect class="ql-fill" height="1.6" rx="0.8" ry="0.8" width="5" x="5.15" y="6.2"/><path class="ql-fill" d="M13.663,12.027a1.662,1.662,0,0,1,.266-0.276q0.193,0.069.456,0.138a2.1,2.1,0,0,0,.535.069,1.075,1.075,0,0,0,.767-0.3,1.044,1.044,0,0,0,.314-0.8,0.84,0.84,0,0,0-.238-0.619,0.8,0.8,0,0,0-.594-0.239,1.154,1.154,0,0,0-.781.3,4.607,4.607,0,0,0-.781,1q-0.091.15-.218,0.346l-0.246.38c-0.068-.288-0.137-0.582-0.212-0.885-0.459-1.847-2.494-.984-2.941-0.8-0.482.2-.353,0.647-0.094,0.529a0.869,0.869,0,0,1,1.281.585c0.217,0.751.377,1.436,0.527,2.038a5.688,5.688,0,0,1-.362.467,2.69,2.69,0,0,1-.264.271q-0.221-.08-0.471-0.147a2.029,2.029,0,0,0-.522-0.066,1.079,1.079,0,0,0-.768.3A1.058,1.058,0,0,0,9,15.131a0.82,0.82,0,0,0,.832.852,1.134,1.134,0,0,0,.787-0.3,5.11,5.11,0,0,0,.776-0.993q0.141-.219.215-0.34c0.046-.076.122-0.194,0.223-0.346a2.786,2.786,0,0,0,.918,1.726,2.582,2.582,0,0,0,2.376-.185c0.317-.181.212-0.565,0-0.494A0.807,0.807,0,0,1,14.176,15a5.159,5.159,0,0,1-.913-2.446l0,0Q13.487,12.24,13.663,12.027Z"/></svg>`

  const quill = new window.Quill('#editor', {
    theme: 'snow',
    modules: {
      toolbar: {
        handlers: {
          'math': function (mathValue) {
            const id = uuidv4()
            const fragment = fragmentsManager.addFragment(id, '', [])
            createMathEditor().show(fragment, (fragmentId, value, placeholders) => {
              try {
                fragmentsManager.updateFragment(fragmentId, value, placeholders)
                this.quill.format('math', {id, value}, Quill.sources.USER);
              } catch(ex) {}
            })
          }
        },
        container: [
          [{header: [1, 2, 3, false]}],
          ["bold", "italic", "underline", "strike"],
          ["blockquote", "code-block"],
          [{list: "ordered"}, {list: "bullet"}],
          [{script: "sub"}, {script: "super"}],
          [{color: []}, {background: []}],
          [{align: []}],
          ["math"],
        ]
      },
      clipboard: {
        matchVisual: false,
      },
    }
  })

  function MathEditor(container, fragmentsManager) {

    const element = document.createElement('div')
    element.setAttribute('id', 'math-editor')
    element.innerHTML = `
<div style="display: flex; flex-direction: row; column-gap: 20px; height: 100%; overflow-y: auto;">
<div style="flex: 1">
    <div style="margin: 10px 0; display: flex; flex-direction: row; justify-content: space-between; align-items: center">
        <label for="">Формула</label>
        <button class="btn btn-primary btn-sm" id="add-placeholder" type="button">Вставить пропуск</button>
    </div>
    <math-field id="mathField" contenteditable="true" tabIndex="0"></math-field>
    <div id="latex" class="output" style="margin-bottom: 20px" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false"
          readonly></div>
</div>
<div style="flex: 1">
    <div><label for="">Пропуски</label></div>
    <div style="margin-bottom: 10px">
        <table class="table table-sm">
        <thead>
        <tr>
        <th>Имя</th><th style="width: 70%">Значение</th><th></th>
        </tr>
        </thead>
        <tbody id="math-gaps-list"></tbody>
        </table>
    </div>
</div>
</div>
<div class="buttons">
    <button type="button" class="mathButton cancelButton">Закрыть</button>
    <button type="button" class="mathButton saveButton">Сохранить</button>
</div>
`

    function createListRow({id, value} = {}) {
      return $(`
<tr class="gap-row">
<td>
<input readonly name="placeholder" type="text" value="${id || ''}" style="border: 1px solid #eee; width: 100px; border-radius: 8px; padding: 5px; background: #fff;" />
</td>
<td>
<math-field style="display: block">${value || ''}</math-field>
</td>
<td>
<a title="Удалить вариант ответа" class="answer-remove" href=""><i class="glyphicon glyphicon-trash"></i></a>
</td>
</tr>`)
    }

    let saveHandlerFunc;
    let fragmentId;

    element.querySelector('.saveButton').addEventListener('click', e => {
      const mf = element.querySelector('math-field')
      if (typeof saveHandlerFunc === 'function') {
        let noValue = false
        const placeholders = $list.find('.gap-row').map((i, el) => {
          const $row = $(el)
          const id = $row.find(`input[name='placeholder']`).val()
          const value = $row.find('math-field')[0].value
          if (!value) {
            noValue = true
          }
          return {
            id,
            value
          }
        }).get()

        if (noValue) {
          toastr.warning('Необходимо заполнить все значения для пропусков')
          return
        }

        saveHandlerFunc(fragmentId, mf.value, placeholders)
      }
      element.remove()
    })

    element.querySelector('.cancelButton').addEventListener('click', e => {
      element.remove()
    })

    const $list = $('#math-gaps-list', element)

    element.querySelector('#add-placeholder').addEventListener('click', e => {
      const id = uuidv4().split('-')[0]
      const mf = element.querySelector('math-field')
      mf.insert(`\\placeholder[${id}]{}`)

      fragmentsManager.addPlaceholder(fragmentId, {
        id,
        value: '',
        new: true
      })

      $list.append(createListRow({
        id,
        value: null
      }))
    })

    return {
      show(fragment, saveHandler) {
        saveHandlerFunc = saveHandler
        fragmentId = fragment.id
        const mf = element.querySelector('math-field')
        mf.setValue(fragment.value || '')
        const latex = element.querySelector('#latex')
        latex.innerHTML = mf.value
        mf.addEventListener('input',() => {
          latex.innerHTML = mf.value
        })

        fragment.placeholders.map(p => {
          $list.append(createListRow(p))
        })

        container.appendChild(element)
      }
    }
  }

  function createMathEditor() {
    return new MathEditor(
      document.getElementById('math-wrap'),
      fragmentsManager
    )
  }

  $('#editor').on('click', '.ql-math-element', e => {
    const fragmentId = e.target.querySelector('math-field').dataset.id
    const fragment = fragmentsManager.getFragment(fragmentId)
    createMathEditor().show(fragment, (id, value, placeholders) => {
      e.target.dataset.value = value
      e.target.querySelector('math-field').setValue(value)
      fragmentsManager.updateFragment(id, value, placeholders)
    })
  })
})()
