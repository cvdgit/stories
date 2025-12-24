import TreeViewBody from "./TreeViewBody";

/**
 * @typedef {Object} Settings
 * @property {boolean} [planTreeView]
 * @property {string|null} [promptId]
 */

/**
 * @param id
 * @param name
 * @param tree
 * @param history
 * @param {{story_id: number|null, slide_id: number|null, mental_map_id: string, threshold: number}} params
 * @param {Settings} settings
 * @param onMentalMapChange
 * @param {VoiceResponse} voiceResponse
 * @returns {HTMLDivElement}
 * @constructor
 */
export default function TreeView({id, name, tree, history, params, settings, onMentalMapChange}, voiceResponse) {

  const wrap = document.createElement('div')
  wrap.style.display = 'flex'
  wrap.style.height = '100%'
  wrap.style.overflow = 'hidden'
  wrap.style.flexDirection = 'column'

  let title = '';
  if (settings?.recognitionLang === 'en-US') {
    title = ', <span data-toggle="tooltip" title="Установлен английский язык записи микрофона">английский язык</span>';
  }

  const header = document.createElement('div')
  header.style.position = 'relative'
  header.innerHTML = `
<h2 class="h3 text-center">${name} (<span data-toggle="tooltip" title="Точность пересказа">${params.threshold}%</span>${title})</h2>
<div style="position: absolute; right: 0; top: 0"><button id="clear-history" title="Очистить историю" type="button" style="display: none; border: 0 none;background: none">
<svg style="width:24px;height:24px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
</svg>
</button></div>
`

  header.querySelector('#clear-history').addEventListener('click', async () => {
    if (!confirm('Подтверждаете?')) {
      return
    }
    const response = await restartMentalMap(id)
    if (response?.success) {
      wrap.appendChild(body.restart())
      header.querySelector('#clear-history').style.display = 'hidden'
      onMentalMapChange(0)
    }
  })

  const clearHistory = history.some(h => h.done)
  if (clearHistory) {
    header.querySelector('#clear-history').style.display = 'block'
  }

  wrap.appendChild(header)

  $(wrap).find("[data-toggle='tooltip']").tooltip({
    container: 'body'
  })

  const body = TreeViewBody(
    tree,
    voiceResponse,
    history,
    params,
    () => {
      const elem = createFinishContent(async () => {
        const response = await restartMentalMap(id)
        if (response?.success) {
          wrap.appendChild(body.restart())
          elem.destroy()
        }
      })
      wrap.appendChild(elem.getElement())
    },
    Boolean(settings.planTreeView),
    settings.promptId
  )
  wrap.appendChild(body.getElement())

  body.on('historyChange', args => {
    const {currentHistory} = args
    const clearHistory = currentHistory.some(h => h.done)
    header.querySelector('#clear-history').style.display = clearHistory ? 'block' : 'hidden'
    onMentalMapChange(Math.round(currentHistory.filter(h => h.done).length * 100 / currentHistory.length))
  })

  body.init()

  const blurHandler = function() {
    if (voiceResponse.getStatus()) {
      voiceResponse.stop()
      const el = document.querySelector('.gn.recording')
      if (el) {
        $(el).data('abort', true).trigger('click')
      }
    }
  }
  window.addEventListener('blur', blurHandler, false);

  return {
    getElement() {
      return wrap
    },
    destroy() {
      window.removeEventListener('blur', blurHandler)
      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
      }
    }
  }
}

function createFinishContent(restartHandler) {
  const elem = document.createElement('div')
  elem.classList.add('retelling-wrap')
  elem.style.backgroundColor = 'transparent'
  elem.style.padding = '0'
  elem.innerHTML = `
      <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background-color: rgba(255, 255, 255, 0.4); backdrop-filter: blur(4px);">
        <h2 style="margin-bottom: 20px">Ментальная карта пройдена</h2>
        <div>
        <button class="btn mental-map-restart" type="button">Пройти еще раз</button>
</div>
      </div>
    `
  elem.querySelector('.mental-map-restart').addEventListener('click', restartHandler)
  return {
    getElement() {
      return elem
    },
    destroy() {
      elem.remove()
    }
  }
}

async function restartMentalMap(id) {
  const response = await fetch(`/mental-map/restart?id=${id}`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
    },
  })
  if (!response.ok) {
    throw new Error(response.statusText)
  }
  return await response.json()
}
