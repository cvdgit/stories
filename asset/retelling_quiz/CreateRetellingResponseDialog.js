import sendEventSourceMessage from "../app/sendEventSourceMessage";
async function sendMessage(payload, onMessage, onError, onEndCallback) {
  let accumulatedMessage = ''
  return sendEventSourceMessage({
    url: '/admin/index.php?r=gpt/stream/retelling',
    headers: {
      Accept: 'text/event-stream',
      'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
    },
    body: JSON.stringify(payload),
    onMessage: (streamedResponse) => {
      if (Array.isArray(streamedResponse?.streamed_output)) {
        accumulatedMessage = streamedResponse.streamed_output.join('');
      }
      onMessage(accumulatedMessage)
    },
    onError: (streamedResponse) => {
      accumulatedMessage = streamedResponse?.error_text
      onError(accumulatedMessage)
    },
    onEnd: onEndCallback
  })
}

export default function CreateRetellingResponseDialog(userResponse, slideTexts, onEndCallback, hideHandler) {
  const elem = document.createElement('div')
  elem.classList.add('retelling-response-dialog')
  elem.innerHTML = `
    <div style="background-color: #fff; padding: 20px; max-width: 800px; height: 500px; display: flex; justify-content: space-between; flex-direction: column; flex: 1 1 auto">
        <div contenteditable="plaintext-only" id="retelling_retelling-response"
             style="font-size: 2.2rem; text-align: left; line-height: 3.5rem; overflow-y: scroll; height: 100%; max-height: 100%;"></div>
        <div style="display: flex; margin-top: 10px; flex-direction: row; align-items: center; justify-content: center">
            <img id="voice-loader" height="50px" src="/img/loading.gif" alt="">
            <button style="display: none" id="voice-finish" type="button" class="btn">OK</button>
        </div>
    </div>
  `

  elem.querySelector('#voice-finish').addEventListener('click', () => {
    elem.remove()
    if (typeof hideHandler === 'function') {
      hideHandler()
    }
  })

  sendMessage(
    {userResponse, slideTexts},
    message => {
      elem.querySelector('#retelling_retelling-response').innerText = message
      elem.querySelector('#retelling_retelling-response').scrollTop = elem.querySelector('#retelling_retelling-response').scrollHeight;
    }, () => {
    }, () => {
      elem.querySelector("#voice-loader").style.display = 'none'
      elem.querySelector("#voice-finish").style.display = 'block'
      if (typeof onEndCallback === 'function') {
        onEndCallback(elem.querySelector('#retelling_retelling-response').innerText)
      }
    })

  return elem
}
