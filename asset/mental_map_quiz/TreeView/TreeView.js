import TreeViewBody from "./TreeViewBody";

/**
 * @param id
 * @param name
 * @param tree
 * @param history
 * @param {{story_id: number|null, slide_id: number|null, mental_map_id: string}} params
 * @param {VoiceResponse} voiceResponse
 * @returns {HTMLDivElement}
 * @constructor
 */
export default function TreeView({id, name, tree, history, params}, voiceResponse) {

  const wrap = document.createElement('div')
  wrap.style.display = 'flex'
  wrap.style.height = '100%'
  wrap.style.overflow = 'hidden'
  wrap.style.flexDirection = 'column'

  const header = document.createElement('div')
  header.innerHTML = `<h2 class="h3 text-center">${name}</h2>`
  wrap.appendChild(header)

  const body = TreeViewBody(tree, voiceResponse, history, params)
  wrap.appendChild(body.getElement())

  const allIsDone = history.reduce((all, val) => all && val.done, true)
  if (allIsDone) {
    const elem = createFinishContent(async () => {
      const response = await restartMentalMap(id)
      if (response?.success) {
        wrap.appendChild(body.restart())
        elem.destroy()
      }
    })
    wrap.appendChild(elem.getElement())
  }

  return wrap
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
