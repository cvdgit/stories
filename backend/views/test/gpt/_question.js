(function () {

  async function fetchPrompt(id) {
    const response = await fetch(`/admin/index.php?r=prompt/get&id=${id}`, {
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      }
    })
    if (!response.ok) {
      throw new Error('Fetch prompt error')
    }
    return await response.json()
  }

  async function savePrompt(id, name, prompt) {
    const response = await fetch(`/admin/index.php?r=prompt/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify({
        id,
        name,
        prompt
      })
    })
    if (!response.ok) {
      throw new Error('Save prompt error')
    }
    return await response.json()
  }

  async function createPrompt(id, name, prompt, key) {
    const response = await fetch(`/admin/index.php?r=prompt/create`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify({
        id,
        name,
        prompt,
        key
      })
    })
    if (!response.ok) {
      throw new Error('Create prompt error')
    }
    return await response.json()
  }

  $('#prompt-create-modal').find('#gpt-prompt-create').on('click', e => {

    const name = $('#prompt-create-modal').find('#gpt-create-prompt-name').val()
    if (!name) {
      return
    }

    const prompt = $('#prompt-create-modal').find('#gpt-create-prompt').text()
    if (!prompt) {
      return
    }

    const newPromptId = window.uuidv4()
    createPrompt(newPromptId, name, prompt, 'question').then(r => {

      if (r.success) {
        const select = $('.gptPromptId')
        select.find('option').map((i, el) => {
          if (el.value !== '') {
            el.remove()
          }
        })
        r.prompts.map(p => $('<option/>', {value: p.id, text: p.name, selected: p.id === newPromptId}).appendTo(select))
        select.blur()
      }

      $('#prompt-create-modal').modal('hide')
    })
  })

  $('#prompt-update-modal').find('#gpt-prompt-save').on('click', e => {
    const id = $('.gptPromptId').val()

    const name = $('#prompt-update-modal').find('#gpt-prompt-name').val()
    if (!name) {
      return
    }

    const prompt = $('#prompt-update-modal').find('#gpt-prompt').text()
    if (!prompt) {
      return
    }
    savePrompt(id, name, prompt).then(r => {
      $('.gptPromptId').find('option:selected').text(name)
      $('#prompt-update-modal').modal('hide')
    })
  })

  $('#prompt-update').on('click', e => {
    const id = $('.gptPromptId').val()
    fetchPrompt(id).then(r => {
      const modal = $('#prompt-update-modal')
      modal.find('#gpt-prompt').html(r.prompt)
      modal.find('#gpt-prompt-name').val(r.name)
      modal.modal('show')
    })
  })

  $('#prompt-create').on('click', e => {
    $('#prompt-create-modal').modal('show')
  })

  async function sendMessage(promptId, job, userResponse, elem, onEndCallback) {
    let accumulatedMessage = ''
    return sendEventSourceMessage({
      url: '/admin/index.php?r=gpt/stream/question',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'text/event-stream',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify({
        promptId,
        job,
        userResponse
      }),
      onMessage: (streamedResponse) => {
        /*if (streamedResponse?.prompt_text) {
          $promptElem[0].textContent = streamedResponse.prompt_text;
        }*/
        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }
        elem.innerText = accumulatedMessage
        elem.parentElement.scrollTop = elem.parentElement.scrollHeight;
      },
      onError: (streamedResponse) => {
        accumulatedMessage = streamedResponse?.error_text
        elem.innerText = accumulatedMessage
      },
      onEnd: onEndCallback
    })
  }

  $('#run-job-modal').find('#job-send').on('click', e => {
    const promptId = $('.gptPromptId').val()
    const job = $('.gptJob').html()
    const userResponse = $('#run-job-modal').find('#gpt-user-response').val()

    if (!job || !userResponse) {
      return
    }

    const elem = document.createElement('div')
    elem.classList.add('run-job-message-item')
    elem.innerText = '...'

    document.getElementById('gpt-message-list').appendChild(elem)

    $('#job-send-loader').css('display', 'flex')

    sendMessage(promptId, job, userResponse, elem, () => {
      $('#job-send-loader').css('display', 'none')
    })
  })

  $('#run-job').on('click', e => {
    const job = $('.gptJob').html()
    $('#run-job-modal')
      .find('#run-job-text')
      .html(job)
      .end()
      .modal('show')
  })

  $('.gptPromptId').on('change', e => {
    const value = e.target.value
    const ids = ['#prompt-update', '#run-job']
    ids.map(id => $(id).hide())
    if (value) {
      ids.map(id => $(id).show())
    }
  })
})();
