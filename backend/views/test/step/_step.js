(function() {

  const icons = window.Quill.import('ui/icons');
  icons['math'] = `<svg viewbox="0 0 18 18"><path class="ql-fill" d="M11.759,2.482a2.561,2.561,0,0,0-3.53.607A7.656,7.656,0,0,0,6.8,6.2C6.109,9.188,5.275,14.677,4.15,14.927a1.545,1.545,0,0,0-1.3-.933A0.922,0.922,0,0,0,2,15.036S1.954,16,4.119,16s3.091-2.691,3.7-5.553c0.177-.826.36-1.726,0.554-2.6L8.775,6.2c0.381-1.421.807-2.521,1.306-2.676a1.014,1.014,0,0,0,1.02.56A0.966,0.966,0,0,0,11.759,2.482Z"/><rect class="ql-fill" height="1.6" rx="0.8" ry="0.8" width="5" x="5.15" y="6.2"/><path class="ql-fill" d="M13.663,12.027a1.662,1.662,0,0,1,.266-0.276q0.193,0.069.456,0.138a2.1,2.1,0,0,0,.535.069,1.075,1.075,0,0,0,.767-0.3,1.044,1.044,0,0,0,.314-0.8,0.84,0.84,0,0,0-.238-0.619,0.8,0.8,0,0,0-.594-0.239,1.154,1.154,0,0,0-.781.3,4.607,4.607,0,0,0-.781,1q-0.091.15-.218,0.346l-0.246.38c-0.068-.288-0.137-0.582-0.212-0.885-0.459-1.847-2.494-.984-2.941-0.8-0.482.2-.353,0.647-0.094,0.529a0.869,0.869,0,0,1,1.281.585c0.217,0.751.377,1.436,0.527,2.038a5.688,5.688,0,0,1-.362.467,2.69,2.69,0,0,1-.264.271q-0.221-.08-0.471-0.147a2.029,2.029,0,0,0-.522-0.066,1.079,1.079,0,0,0-.768.3A1.058,1.058,0,0,0,9,15.131a0.82,0.82,0,0,0,.832.852,1.134,1.134,0,0,0,.787-0.3,5.11,5.11,0,0,0,.776-0.993q0.141-.219.215-0.34c0.046-.076.122-0.194,0.223-0.346a2.786,2.786,0,0,0,.918,1.726,2.582,2.582,0,0,0,2.376-.185c0.317-.181.212-0.565,0-0.494A0.807,0.807,0,0,1,14.176,15a5.159,5.159,0,0,1-.913-2.446l0,0Q13.487,12.24,13.663,12.027Z"/></svg>`

  function MathHandlerBuilder(stepId) {
    return function (mathValue){
      const fragmentsManager = stepsManager.getFragmentManager(stepId)
      const id = uuidv4()
      const fragment = fragmentsManager.addFragment(id, '', [])
      createMathEditorFragment(fragmentsManager).show(fragment, (fragmentId, value, placeholders) => {
        if (!value) {
          return
        }
        try {
          fragmentsManager.updateFragment(fragmentId, value, placeholders)
          this.quill.format('math', {id, value}, Quill.sources.USER);
        } catch (ex) {
        }
      })
    }
  }

  function initQuil(element, mathHandler) {
    return new window.Quill(element, {
      theme: 'snow',
      modules: {
        toolbar: {
          handlers: {
            'math': mathHandler
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
  }

  const jobQuill = initQuil('#editor', function(mathValue) {
    createMathEditor().show('', (value) => {
      if (!value) {
        return
      }
      try {
        this.quill.format('math', value, Quill.sources.USER);
      } catch (ex) {
      }
    })
  })

  function MathEditor(container) {
    const element = document.createElement('div')
    element.setAttribute('id', 'math-editor')
    element.innerHTML = `
<div>
    <div style="margin: 10px 0; display: flex; flex-direction: row; justify-content: space-between; align-items: center">
        <label for="">Формула</label>
    </div>
    <math-field id="mathField" contenteditable="true" tabIndex="0"></math-field>
    <div id="latex" class="output" style="margin-bottom: 20px" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false"
          readonly></div>
</div>
<div class="buttons">
    <button type="button" class="mathButton cancelButton">Закрыть</button>
    <button type="button" class="mathButton saveButton">Сохранить</button>
</div>
`

    let saveHandlerFunc;

    element.querySelector('.saveButton').addEventListener('click', e => {
      const mf = element.querySelector('math-field')
      if (typeof saveHandlerFunc === 'function') {
        saveHandlerFunc(mf.value)
      }
      element.remove()
    })

    element.querySelector('.cancelButton')
      .addEventListener('click', () => element.remove())

    return {
      show(value, saveHandler) {
        if (document.getElementById('math-editor')) {
          return
        }
        saveHandlerFunc = saveHandler
        const mf = element.querySelector('math-field')
        mf.setValue(value || '')
        const latex = element.querySelector('#latex')
        latex.innerHTML = mf.value
        mf.addEventListener('input',() => {
          latex.innerHTML = mf.value
        })
        container.appendChild(element)
      }
    }
  }

  function MathEditorFragment(container, fragmentsManager) {

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

        if (document.getElementById('math-editor')) {
          return
        }

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

  function createMathEditorFragment(fragmentsManager) {
    return new MathEditorFragment(
      document.getElementById('step-list'),
      fragmentsManager
    )
  }

  function createMathEditor() {
    return new MathEditor(document.getElementById('step-list'))
  }

  $('#editor').on('click', '.ql-math-element', e => {
    const mf = e.target.querySelector('math-field')
    createMathEditor().show(mf.value, (value) => {
      if (!value) {
        e.target.remove()
        return
      }
      e.target.dataset.value = value
      e.target.querySelector('math-field').setValue(value)
    })
  })

  $('#step-list').on('click', '.ql-math-element', e => {
    const stepId = $(e.target).parents('.grouping-item[data-id]').attr('data-id')
    const fragmentsManager = stepsManager.getFragmentManager(stepId)
    const fragmentId = e.target.querySelector('math-field').dataset.id
    const fragment = fragmentsManager.getFragment(fragmentId)
    createMathEditorFragment(fragmentsManager).show(fragment, (id, value, placeholders) => {
      if (!value) {
        fragmentsManager.deleteFragment(id)
        e.target.remove()
        return
      }
      e.target.dataset.value = value
      e.target.querySelector('math-field').setValue(value)
      fragmentsManager.updateFragment(id, value, placeholders)
    })
  })

  function StepsManager(initSteps) {

    const state = {steps: []}
    const fragmentManagers = new Map()

    this.getFragmentManager = stepId => {
      if (!stepId) {
        throw new Error('No step id')
      }
      return fragmentManagers.get(stepId)
    }

    this.getSteps = () => [...state.steps.map(s => ({
      ...s,
      fragments: this.getFragmentManager(s.id).getFragments()
    }))]

    this.addStep = step => {
      if (!fragmentManagers.has(step.id)) {
        fragmentManagers.set(step.id, new FragmentsManager(step.fragments || []))
      }
      state.steps.push(step)
    }

    this.newStep = (id) => {
      const step = {id, name: '', job: '', answers: [], isAnswerOptions: false, fragments: [], index: state.steps.length + 1}
      this.addStep(step)
      return step
    }

    this.addStepAnswer = (stepId, answer) => {
      state.steps.map(s => {
        if (s.id === stepId) {
          return {...s, answers: s.answers.push(answer)}
        }
        return s
      })
      return answer
    }

    this.newStepAnswer = (id) => {
      return {
        id,
        title: '',
        correct: false
      }
    }

    this.updateStep = (id, step) => {
      state.steps = state.steps.map(s => {
        if (s.id === id) {
          return {...s, ...step}
        }
        return s
      })
    }

    this.updateStepAnswer = (stepId, answerId, answer) => {
      state.steps = state.steps.map(s => {
        if (s.id === stepId) {
          return {
            ...s, answers: s.answers.map(a => {
              if (a.id === answerId) {
                return {...a, ...answer}
              }
              return a
            })
          }
        }
        return s
      })
    }

    /*this.moveGroup = (newIndex, oldIndex) => {
      state.groups.splice(oldIndex, 0, state.groups.splice(newIndex, 1)[0])
    }*/

    this.deleteStep = (id) => {
      state.steps = state.steps.filter(s => s.id !== id)
    }

    this.deleteStepAnswer = (stepId, answerId) => {
      state.steps = state.steps.map(s => {
        if (s.id === stepId) {
          return {...s, answers: s.answers.filter(a => a.id !== answerId)}
        }
        return s
      })
    }

    this.validate = () => {
      const steps = this.getSteps()
      if (steps.length === 0) {
        throw new Error('Нет этапов')
      }
      steps.map(step => {
        if (step.name === '') {
          throw new Error('Есть этапы с пустым названием')
        }
        if (!step.isAnswerOptions && stripTags(step.job) === '') {
          throw new Error('Есть этапы с пустым заданием')
        }
        const haveAnswers = step.answers.filter(a => a.title.trim() !== '').length > 0
        if (step.isAnswerOptions && !haveAnswers) {
          throw new Error('Есть этапы где установлен флаг "Варианты ответов" но список ответов пуст')
        }
        if (step.isAnswerOptions && step.answers.filter(a => a.correct).length === 0) {
          throw new Error('Есть этапы где нет правильного варианта ответа')
        }
      })
    }

    if (initSteps.length) {
      initSteps.map(step => this.addStep(step))
    }
  }

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

  const stepsManager = new StepsManager(window["steps"])

  function StepsRenderer(container) {

    this.container = container

    this.appendStep = ({id, name, job, index, answers, isAnswerOptions}, updateStepHandler, addAnswerHandler, updateAnswerHandler, deleteHandler, deleteItemHandler) => {
      const html = `<div class="grouping-item" data-id="${id}">
    <div class="grouping-item-inner">
        <div style="width: 20px">
            <div class="handle">
                <svg viewBox="0 0 4 24" width="4" height="24" focusable="false">
                    <title>Vertical Dots</title>
                    <desc>Vertical Dots</desc>
                    <path fill-rule="evenodd"
                          d="M2 24a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0-10a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM2 4a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"></path>
                </svg>
            </div>
        </div>
        <div style="min-width: 30px">
            <span class="group-number">${index}</span>
        </div>
        <div style="width: 100%; height: 100%">
            <div style="display: flex; flex-direction: row; justify-content: space-between">
                <div style="width: 100%; height: 100%">
                    <label class="control-label">Название:</label>
                    <input type="text" class="form-control step-name" value="${name}" />
                </div>
                <div style="display: flex; align-items: center; justify-content: center; width: 50px">
                    <a style="width: 20px" href="#" class="delete-group text-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div style="padding: 20px 0">
                <div style="margin-bottom: 20px; display: none" class="step-job-wrap">
                    <label class="control-label">Задание:</label>
                    <div class="job-editor" style="min-height: 100px">${job}</div>
                </div>
                <div style="margin-bottom: 10px">
                    <label><input class="step-answers-check" type="checkbox"> Варианты ответов</label>
                </div>
                <div style="display: none" class="step-answers-wrap">
                    <div>
                        <label for="">Ответы:</label>
                    </div>
                    <div class="grouping-item-list">
                        <p class="no-group-items">Пусто</p>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm add-group-item" type="button">Добавить ответ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
`

      if (this.container.find(".no-groups").length) {
        this.container.empty()
      }

      this.container.append(html)

      const $element = this.container.find(`[data-id="${id}"]`)

      const quill = initQuil($element.find('.job-editor')[0], MathHandlerBuilder(id))
      quill.on('text-change', () => updateStepHandler({job: quill.container.firstChild.innerHTML}))

      const $answersList = $element.find(".step-answers-wrap")
      const $jobWrap = $element.find(".step-job-wrap")

      $element.find('.step-answers-check')
        .prop('checked', isAnswerOptions)
        .on('click', e => {
          updateStepHandler({isAnswerOptions: e.target.checked})
          $answersList.css('display', e.target.checked ? 'block' : 'none')
          $jobWrap.css('display', e.target.checked ? 'none' : 'block')
        })

      $element.find('.step-name').on('input', e => updateStepHandler({name: e.target.value}))

      $element.find(".add-group-item").on("click", () => {
        if ($answersList.find(".no-group-items").length) {
          $answersList.find('.grouping-item-list').empty()
        }
        const answer = addAnswerHandler()
        this.appendAnswer($answersList.find('.grouping-item-list'), answer, updateAnswerHandler, deleteItemHandler)
      })

      $element.find(".delete-group").on("click", (e) => {
        e.preventDefault()
        if (!confirm('Подтверждаете?')) {
          return
        }
        try {
          deleteHandler(id)
          $element.remove()
        } catch (ex) {}
      })

      $answersList.find('.grouping-item-list').empty()
      answers = answers || []
      answers.map(answer => this.appendAnswer(
        $answersList.find('.grouping-item-list'),
        answer,
        updateAnswerHandler,
        deleteItemHandler
      ))

      if (isAnswerOptions) {
        $answersList.css('display', 'block');
      } else {
        $jobWrap.css('display', 'block')
      }

      return $element
    }

    // <math-field class="group-item-title" style="width: 100%">${answer.title || ''}</math-field>

    this.appendAnswer = ($list, answer, updateAnswerHandler, deleteItemHandler) => {
      const $item = $(`<div class="grouping-item-item">
    <div style="flex: 1; display: flex; flex-direction: row; justify-content: space-between; align-items: center; column-gap: 20px">
        <div style="flex: 1">
            <input class="form-control group-item-title" style="width: 100%" type="text" value="${answer.title || ''}">
        </div>
        <div style="width: 100px; display: flex; align-items: center; justify-content: center"><label>Верный <input class="answer-correct" type="checkbox" ${answer.correct ? "checked" : ""}></label></div>
    </div>
    <div style="width: 50px; display: flex; align-items: center; justify-content: center">
        <a style="width: 20px" href="#" class="group-item-delete text-danger">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </a>
    </div>
</div>`)

      function updateHandler() {
        updateAnswerHandler(
          answer.id,
          $item.find('.group-item-title')[0].value,
          $item.find('.answer-correct').is(':checked')
        )
      }

      $item.find(".group-item-title").on('input', updateHandler)
      $item.find('.answer-correct').on('change', updateHandler)

      $item.find(".group-item-delete").on("click", (e) => {
        e.preventDefault()
        $item.remove()
        deleteItemHandler(answer.id)
      })

      $list.append($item)
    }

    this.resetGroupIndex = () => this.container.find(".grouping-item")
      .map((i, element) => $(element).find(".group-number").text(i + 1))
  }

  const renderer = new StepsRenderer($("#step-list"))

  $("#add-step").on("click", function () {
    const step = stepsManager.newStep(uuidv4())
    renderer.appendStep(step,
      update => stepsManager.updateStep(step.id, update),
      () => stepsManager.addStepAnswer(step.id, stepsManager.newStepAnswer(uuidv4())),
      (id, title, correct) => stepsManager.updateStepAnswer(step.id, id, {title, correct}),
      id => stepsManager.deleteStep(id),
      answerId => stepsManager.deleteStepAnswer(step.id, answerId),
    )
  })

  stepsManager.getSteps().map(step => {
    renderer.appendStep(
      step,
      update => stepsManager.updateStep(step.id, update),
      () => stepsManager.addStepAnswer(step.id, stepsManager.newStepAnswer(uuidv4())),
      (id, title, correct) => stepsManager.updateStepAnswer(step.id, id, {title, correct}),
      id => stepsManager.deleteStep(id),
      answerId => stepsManager.deleteStepAnswer(step.id, answerId),
    )
  })

  function stripTags(html) {
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || '';
  }

  attachBeforeSubmit(document.getElementById('step-question-form'), (form) => {

    const formData = new FormData()

    const name = $('.stepQuestionName').val().trim()
    if (!name) {
      toastr.warning('Необходимо указать вопрос')
      return
    }
    formData.append('name', name)

    const textContent = jobQuill.container.firstChild.textContent
    if (!textContent) {
      toastr.warning('Необходимо заполнить задание')
      return
    }
    formData.append('job', jobQuill.container.firstChild.innerHTML)

    try {
      stepsManager.validate()
    } catch (ex) {
      toastr.error(ex.message, 'Необходимо исправить')
      return
    }

    stepsManager.getSteps().map(step => {
      const toDeleteFragmentIds = []
      const fragmentsManager = stepsManager.getFragmentManager(step.id)
      fragmentsManager.getFragments().map(f => {
        if (!$(step.job).find(`math-field[data-id=${f.id}]`).length) {
          toDeleteFragmentIds.push(f.id)
        }
      })
      toDeleteFragmentIds.map(id => fragmentsManager.deleteFragment(id))

      if (step.isAnswerOptions) {
        stepsManager.updateStep(step.id, {job: '', fragments: []})
      } else {
        stepsManager.updateStep(step.id, {answers: []})
      }
    })

    formData.append('steps', JSON.stringify(stepsManager.getSteps()))

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
  })
})()
