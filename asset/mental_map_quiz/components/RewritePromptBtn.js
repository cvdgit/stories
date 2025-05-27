export default function RewritePromptBtn(clickHandler) {
  const promptBtn = document.createElement('button')
  promptBtn.setAttribute('type', 'button')
  promptBtn.style.marginRight = '10px'
  promptBtn.textContent = 'Rewrite prompt'
  promptBtn.addEventListener('click', clickHandler)
  return promptBtn
}

export function createUpdatePromptContent(prompt, saveHandler, closeHandler) {
  const elem = document.createElement('div')
  elem.classList.add('retelling-wrap')
  elem.innerHTML = `
<div style="display: flex; flex-direction: column; justify-content: space-between; height: 100%">
    <div class="textarea prompt-text" style="flex: 1" contenteditable="plaintext-only">${prompt}</div>
    <div style="display: flex; flex-direction: row; justify-content: center; padding-top: 20px; align-items: center">
        <button type="button" style="margin-right: 10px;" class="button prompt-save">Сохранить</button>
        <button type="button" class="button prompt-close">Закрыть</button>
    </div>
</div>
    `
  elem.querySelector('.prompt-save').addEventListener('click', () => saveHandler(elem.querySelector('.prompt-text').textContent, close))

  const close = () => {
    elem.remove()
    if (typeof closeHandler === 'function') {
      closeHandler()
    }
  }

  elem.querySelector('.prompt-close').addEventListener('click', close)

  return elem
}

export async function saveRewritePrompt(newPrompt) {
  const response = await fetch(`/mental-map/update-rewrite-prompt`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
    },
    body: JSON.stringify({
      prompt: newPrompt,
    }),
  })
  if (!response.ok) {
    throw new Error(response.statusText)
  }
  return await response.json()
}
