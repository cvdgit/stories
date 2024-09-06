function GptRewriteText() {

  async function sendMessage(content, prompt, $elem, $promptElem, onEndCallback) {
    let accumulatedMessage = ''
    return sendEventSourceMessage({
      url: '/admin/index.php?r=gpt/stream/rewrite-text',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'text/event-stream',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify({
        content,
        prompt
      }),
      onMessage: (streamedResponse) => {
        if (streamedResponse?.prompt_text) {
          $promptElem[0].textContent = streamedResponse.prompt_text;
        }
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

  function rewriteHandler(content, prompt) {

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
          content,
          prompt,
          $modal.find('#gpt-rewrite-text-result'),
          $modal.find('#gpt-rewrite-text-prompt'),
          (response) => {
            $modal.find('#gpt-rewrite-text-save').show()
            $modal.find('#gpt-rewrite-text-with-prompt')
              .removeAttr('disabled')
              .removeClass('disabled')
          }
        )
        response.then(data => {
          $modal.find('#gpt-rewrite-text-loader').hide()
          $modal.find('#gpt-rewrite-text-actions').show()
          $modal.find('#gpt-rewrite-text-with-prompt')
            .removeAttr('disabled')
            .removeClass('disabled')
        });
      })
    })
  }

  $modal.on('hide.bs.modal', () => {
    $modal.find('#to-rewrite-text').text('')
  });

  $modal.on('show.bs.modal', () => {
    $modal.find('#to-rewrite-text').text(this.content)
  });

  $modal.find('#gpt-rewrite-text')
    .on('click', () => {

      $modal.find('#gpt-rewrite-text-with-prompt')
        .attr('disabled', 'true')
        .addClass('disabled')

      rewriteHandler(this.content)
    })

  $modal.find('#gpt-rewrite-text-with-prompt')
    .on('click', (e) => {

      $(e.target)
        .attr('disabled', 'true')
        .addClass('disabled')

      const prompt = $modal.find("#gpt-rewrite-text-prompt")
        .html()
        .replace(/<br>/g, "\n")

      rewriteHandler(this.content, prompt)
    })

  $modal.find('#gpt-rewrite-text-save').on('click', () => {
    const text = $modal.find('#gpt-rewrite-text-result').html()
    this.rewriteTextSaveHandler(text)
    $modal.modal('hide')
  })

  $modal.find('#gpt-rewrite-text-prompt-toggle')
    .on('click', () => $modal.find('#gpt-rewrite-text-prompt-wrap').toggle())

  this.showModal = ({content, rewriteTextSaveHandler}) => {
    this.content = content
    this.rewriteTextSaveHandler = rewriteTextSaveHandler
    $modal.modal('show')
  }
}
