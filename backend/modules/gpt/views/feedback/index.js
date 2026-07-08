(function() {

  $('#feedback-list').on('click', '.gpt-input-run', e => {

    const dialog = new SimpleModal({
      id: 'gpt-input-run-modal',
      title: 'Input run'
    })

    const prompt = e.target
      .closest('button')
      .parentNode
      .parentNode
      .querySelector('pre').innerText

    const body = document.createElement('div')
    body.className = 'gpt-input-run-body'
    body.innerHTML = `
<div style="flex: 1; display: flex; flex-direction: column; gap: 10px">
    <pre class="gpt-output-prompt" contenteditable="plaintext-only" style="overflow-y: auto; flex: 1">${prompt}</pre>
    <div>
        <button class="gpt-input-run-send" type="button">Отправить</button>
    </div>
</div>
<div style="flex: 1">
    <pre class="gpt-output-container" style="overflow-y: auto"></pre>
</div>
`

    function sendRequest() {
      body.classList.add('processing')
      body.querySelector('.gpt-input-run-send').setAttribute('disabled', 'disabled')
      const elem = body.querySelector('.gpt-output-container')
      elem.innerHTML = ''
      window.sendStreamMessage(
        `/admin/index.php?r=gpt/stream/run`,
        {
          prompt: body.querySelector('.gpt-output-prompt').innerText
        },
        message => elem.innerHTML = message,
        () => body.querySelector('.gpt-input-run-send').removeAttribute('disabled'),
        () => body.querySelector('.gpt-input-run-send').removeAttribute('disabled')
      )
    }

    body.querySelector('.gpt-input-run-send')
      .addEventListener('click', sendRequest)

    dialog.on('show', sendRequest)

    dialog.show({body})
  })
})()
