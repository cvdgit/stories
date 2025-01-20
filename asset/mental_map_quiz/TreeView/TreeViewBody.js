import "./TreeViewBody.css"
import TreeVoiceControl from "./TreeVoiceControl";
import sendMessage from "../lib/sendMessage";

const nodeStatusSuccessHtml = `
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
     stroke="currentColor" class="retelling-success">
    <path stroke-linecap="round" stroke-linejoin="round"
          d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
</svg>
`

const nodeStatusFailedHtml = `
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
     class="retelling-failed">
    <path stroke-linecap="round" stroke-linejoin="round"
          d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
</svg>
`

function createRow(node, level = 0) {

  const row = document.createElement('div')
  row.dataset.nodeId = node.id
  row.dataset.level = node.level
  row.classList.add('node-row')
  if (node.level > 0) {
    row.classList.add('d-none')
  }
  if (node.hasChildren) {
    row.classList.add('node-has-children')
  }

  row.innerHTML = `<div class="node-status"></div>
<div class="node-title">${node.title}</div>
<div class="node-voice-response">
    <div>
        <span class="final_span"></span>
        <span class="interim_span"></span>
    </div>
    <div class="result_span"></div>
    <div class="retelling-response"></div>
</div>
<div class="node-control"></div>
  `;

  node.hasChildren && row.querySelector('.node-title').addEventListener('click', e => {
    const parent = e.target.parentNode
    const parentLevel = parent.dataset.level
    const nextLevel = Number(parentLevel) + 1
    let childRow = parent.nextSibling
    while (childRow) {
      if (childRow.dataset.level === nextLevel.toString()) {
        childRow.classList.toggle('d-none')
      } else {
        break
      }
      childRow = childRow.nextSibling
    }
  })

  return row;
}

function processTreeNodes(list, body, history, voiceResponse) {
  for (const listItem of list) {

    const rowElement = body.querySelector(`.node-row[data-node-id='${listItem.id}']`)
    const nodeId = rowElement.dataset.nodeId
    const historyItem = history.find(i => i.id === nodeId)

    resetNodeRow(rowElement)

    const nodeStatusElement = rowElement.querySelector('.node-status')

    if (historyItem && historyItem.done) {
      nodeStatusElement.innerHTML = nodeStatusSuccessHtml
      continue
    }

    const voiceResponseElem = rowElement.querySelector('.node-voice-response')
    const resultSpan = voiceResponseElem.querySelector('.result_span')
    const retellingResponseSpan = voiceResponseElem.querySelector('.retelling-response')
    const finalSpan = voiceResponseElem.querySelector('.final_span')
    const interimSpan = voiceResponseElem.querySelector('.interim_span')

    const treeVoiceControlElement = TreeVoiceControl(voiceResponse, (action, targetElement) => {

      if (action === 'start') {
        finalSpan.innerHTML = ''
        interimSpan.innerHTML = ''
        resultSpan.innerHTML = ''
        voiceResponse.onResult(args => {
          finalSpan.innerHTML = args.args?.result
          interimSpan.innerHTML = args.args?.interim
        })
      }
      if (action === 'stop') {
        const userResponse = finalSpan.innerHTML
        if (!userResponse) {
          return
        }

        const rootElement = targetElement.closest('.node-row')
        const backdrop = createRewriteContent('Обработка ответа...')
        rootElement.appendChild(backdrop.getElement())

        sendMessage(
          `/admin/index.php?r=gpt/stream/retelling-rewrite`,
          {
            userResponse,
            slideTexts: listItem.title
          },
          (message) => resultSpan.innerText = message,
          (error) => console.log('error', error),
          () => {
            if (resultSpan.innerText.length === 0) {
              backdrop.remove()
              return
            }
            retellingResponseSpan.innerText = ''
            sendMessage(`/admin/index.php?r=gpt/stream/retelling`, {
                userResponse: resultSpan.innerText,
                slideTexts: listItem.title
              },
              (message) => retellingResponseSpan.innerText = message,
              (error) => console.log('error', error),
              () => {
                backdrop.remove()

                const json = processOutputAsJson(retellingResponseSpan.innerText)
                if (json === null) {
                  console.log('no json')
                  return
                }
                const val = Number(json?.overall_similarity)

                const historyItem = history.find(i => i.id === nodeId)
                if (val > 50) {
                  nodeStatusElement.innerHTML = nodeStatusSuccessHtml

                  if (historyItem) {
                    historyItem.done = true
                  } else {
                    history.push({id: nodeId, done: true})
                  }

                  processTreeNodes(list, body, history, voiceResponse)
                } else {
                  if (historyItem) {
                    historyItem.done = false
                  } else {
                    history.push({id: nodeId, done: false})
                  }
                  nodeStatusElement.innerHTML = nodeStatusFailedHtml
                }
              }
            )
          }
        )
      }
    })
    rowElement.querySelector('.node-control').appendChild(treeVoiceControlElement)

    if (!rowElement.checkVisibility()) {
      const level = Number(rowElement.dataset.level)
      if (level > 0) {
        rowElement.parentNode.querySelectorAll(`.node-row[data-level='${level}']`)
          .forEach(children => children.classList.toggle('d-none'))
      }
    }

    break

    /*} else {
      nodeStatusElement.innerHTML = historyItem.done ? nodeStatusSuccessHtml : nodeStatusFailedHtml

      if (!historyItem.done) {

        const treeVoiceControlElement = TreeVoiceControl(voiceResponse, (action, targetElement) => {
          historyItem.done = true
          processTreeNodes(list, body, history, voiceResponse)
        })

        rowElement.querySelector('.node-control').appendChild(treeVoiceControlElement)

        break
      }
    }*/
  }
}

function flatten(nodes, level = 0) {
  return nodes.flatMap(({id, title, children}, index) => [
    {id, title, level, index, hasChildren: (children || []).length > 0},
    ...flatten(children || [], level + 1)
  ])
}

export default function TreeViewBody(tree, voiceResponse, history) {

  const body = document.createElement('div')
  body.classList.add('tree-body')

  const list = flatten(tree)
  list.map(node => {
    body.appendChild(createRow(node))
  })

  const sortedList = [...list].sort((a, b) => a.level - b.level)
  processTreeNodes(sortedList, body, history, voiceResponse)

  return body
}

function createRewriteContent(text, hideCallback) {
  const wrap = document.createElement('div')
  wrap.classList.add('retelling-wrap')
  wrap.style.backgroundColor = 'transparent'
  wrap.style.padding = '0'
  wrap.innerHTML = `
      <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; height: 100%; background-color: rgba(255, 255, 255, 0.5); backdrop-filter: blur(4px);">
        <div style="margin-right: 30px; font-size: 24px" class="retelling-info-text">${text}</div>
        <img src="/img/loading.gif" style="width: 50px" alt="..." />
      </div>
    `
  return {
    getElement() {
      return wrap
    },
    setText(text) {
      wrap.querySelector('.retelling-info-text').textContent = text
    },
    remove() {
      wrap.remove()
    }
  }
}

function processOutputAsJson(output) {
  let json = null
  try {
    json = JSON.parse(output.replace(/```json\n?|```/g, ''))
  } catch (ex) {

  }
  return json
}

function resetNodeRow(row) {
  row.querySelector('.node-status').innerHTML = ''
  const voiceResponseElem = row.querySelector('.node-voice-response');
  ['.result_span', '.retelling-response', '.final_span', '.interim_span']
    .map(s => voiceResponseElem.querySelector(`${s}`).innerHTML = '')
  row.querySelector('.node-control').innerHTML = ''
}
