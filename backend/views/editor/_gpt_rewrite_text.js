function GptRewriteText() {

  async function sendMessage(promptId, content, prompt, $elem, onEndCallback) {
    let accumulatedMessage = ''
    return sendEventSourceMessage({
      url: '/admin/index.php?r=gpt/stream/rewrite-text',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'text/event-stream',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify({
        promptId,
        content,
        prompt
      }),
      onMessage: (streamedResponse) => {
        /*if (streamedResponse?.prompt_text) {
          $promptElem[0].textContent = streamedResponse.prompt_text;
        }*/
        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }
        $elem[0].innerText = accumulatedMessage
        $elem[0].scrollTop = $elem[0].scrollHeight;
      },
      onError: (streamedResponse) => {
        accumulatedMessage = streamedResponse?.error_text
        $elem[0].innerText = accumulatedMessage
      },
      onEnd: onEndCallback
    })
  }

  const $modal = $('#gpt-rewrite-text-modal')

  function rewriteHandler(promptId, content, prompt) {

    if (content.length < 50) {
      toastr.warning("Слишком короткий текст");
      return;
    }

    $modal.find('#gpt-rewrite-text-actions').hide()
    $modal.find('#gpt-rewrite-text-loader').show()
    $modal.find('#gpt-rewrite-text-prompt-wrap').hide()

    $modal.find('#to-rewrite-text').slideUp(() => {
      $modal.find('#gpt-rewrite-text-wrap').slideDown(() => {

        $modal.find('#gpt-rewrite-text-save').hide()

        const response = sendMessage(
          promptId,
          content,
          prompt,
          $modal.find('#gpt-rewrite-text-result'),
          //$modal.find('#gpt-rewrite-text-prompt'),
          (response) => {
            $modal.find('#gpt-rewrite-text-save').show()
            $modal.find('#gpt-rewrite-text-with-prompt')
              .removeAttr('disabled')
              .removeClass('disabled')
          }
        )
        response.then(data => {
          $modal.find('#gpt-rewrite-text-loader').hide()
        });
      })
    })
  }

  $modal.on('hide.bs.modal', () => {
    $modal.find('#to-rewrite-text').text('')
  })

  async function fetchPrompts() {
    const response = await fetch(`/admin/index.php?r=prompt&key=slide_text`, {
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      }
    })
    if (!response.ok) {
      throw new Error('Fetch prompts error')
    }
    return await response.json()
  }

  async function savePrompt(id, prompt) {
    const response = await fetch(`/admin/index.php?r=prompt/save`, {
      method: 'post',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify({
        id,
        prompt
      })
    })
    if (!response.ok) {
      throw new Error(response.statusText)
    }
    return await response.json()
  }

  async function createPrompt(id, name) {
    const response = await fetch(`/admin/index.php?r=prompt/create`, {
      method: 'post',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify({
        id,
        name
      })
    })
    if (!response.ok) {
      throw new Error(response.statusText)
    }
    return await response.json()
  }

  this.prompts = []

  $modal.on('show.bs.modal', () => {
    const $select = $modal.find('#select-prompts')
    $select.empty().prop('disabled', true)
    fetchPrompts().then(response => {
      if (response?.success) {
        this.prompts = [...(response?.prompts || [])];
        (response?.prompts || []).map(item => {
          $('<option/>', {value: item.id, text: item.name})
            .appendTo($select)
        })
        $select.prop('disabled', (response?.prompts || []).length === 0)
        $select.trigger('change')
      }
    })
    $modal.find('#to-rewrite-text').text(this.content)
  });

  $modal.find('#gpt-rewrite-text')
    .on('click', () => {

      const promptId = $modal.find('#select-prompts').val()
      if (!promptId) {
        return
      }

      $modal.find('#gpt-rewrite-text-with-prompt')
       .attr('disabled', 'true')
       .addClass('disabled')

      rewriteHandler(promptId, this.content)
    })

  $modal.find('#gpt-rewrite-text-with-prompt')
    .on('click', (e) => {

      const promptId = $modal.find('#select-prompts').val()

      $(e.target)
        .attr('disabled', 'true')
        .addClass('disabled')

      const prompt = $modal.find("#gpt-rewrite-text-prompt")
        .html()
        .replace(/<br>/g, "\n")

      rewriteHandler(promptId, this.content, prompt)
    })

  $modal.find('#gpt-rewrite-text-save').on('click', () => {
    const text = $modal.find('#gpt-rewrite-text-result').html()
    this.rewriteTextSaveHandler(text)
    $modal.modal('hide')
  })

  $modal.find('#gpt-rewrite-text-prompt-toggle')
    .on('click', () => $modal.find('#gpt-rewrite-text-prompt-wrap').toggle())

  $modal.find('#select-prompts')
    .on('change', (e) => {

      const promptId = e.target.value

      if (!promptId) {
        return
      }

      const prompt = this.prompts.find(p => p.id === promptId)
      console.log(promptId, this.prompts, prompt)

      $modal.find('#gpt-rewrite-text-actions').show()
      $modal.find('#gpt-rewrite-text-with-prompt')
        .removeAttr('disabled')
        .removeClass('disabled')
      $modal.find('#gpt-rewrite-text-prompt').text(prompt.prompt)
    })

  $modal.find('#gpt-rewrite-text-save-prompt').on('click', e => {
    const promptId = $modal.find('#select-prompts').val()
    if (!promptId) {
      return
    }

    const prompt = $modal.find("#gpt-rewrite-text-prompt")
      .html()
      .replace(/<br>/g, "\n")

    if (!prompt) {
      return
    }

    const $btn = $(e.target)
    const $status = $modal.find('#prompt-save-status')

    $status.hide()

    $btn
      .prop('disabled', true)
      .addClass('disabled')

    savePrompt(promptId, prompt).then(response => {
      if (response?.success) {
        $status.text('Успешно').fadeIn()
      } else {
        $status.text(response?.message || 'Произошла ошибка').fadeIn()
      }
      setTimeout(() => $status.fadeOut(), 3000)
      $btn
        .removeAttr('disabled')
        .removeClass('disabled')
    }).catch(response => {
      $status.text(response || 'Ошибка').fadeIn()
      setTimeout(() => $status.fadeOut(), 3000)
      $btn
        .removeAttr('disabled')
        .removeClass('disabled')
    })
  })

  $modal.find('#gpt-prompt-create').on('click', e => {
    const name = $modal.find('#gpt-prompt-name').val().trim()
    if (!name) {
      return
    }
    const id = window.uuidv4()

    createPrompt(id, name).then(response => {

      const $select = $modal.find('#select-prompts')
      $select.empty().prop('disabled', true)
      this.prompts = [...(response?.prompts || [])];
      (response?.prompts || []).map(item => {
        $('<option/>', {value: item.id, text: item.name, selected: item.id === id})
          .appendTo($select)
      })
      $select.prop('disabled', (response?.prompts || []).length === 0)
      $select.trigger('change')

      $modal.find('#gpt-prompt-name').val('')

    })
  })

  this.showModal = ({content, rewriteTextSaveHandler}) => {
    this.content = content
    this.rewriteTextSaveHandler = rewriteTextSaveHandler
    $modal.modal('show')
  }
}
