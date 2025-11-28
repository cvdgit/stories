import "./TreeViewBody.css"
import TreeVoiceControl from "./TreeVoiceControl";
import sendMessage from "../lib/sendMessage";
import {calcHiddenTextPercent, createWordItem} from "../words";
import {processOutputAsJson, stripTags} from "../common";
import {SimilarityChecker} from "../lib/calcSimilarity";

const nodeStatusSuccessHtml = `
<div class="retelling-status-show"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
     stroke="currentColor" class="retelling-success">
    <path stroke-linecap="round" stroke-linejoin="round"
          d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
</svg></div>
`

const nodeStatusFailedHtml = `
<div class="retelling-status-show"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
     class="retelling-failed">
    <path stroke-linecap="round" stroke-linejoin="round"
          d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
</svg></div>
`

function createRow(node, level = 0, isPlanTreeView = false) {

  const row = document.createElement('div')
  row.dataset.nodeId = node.id
  row.dataset.level = node.level
  row.classList.add('node-row', 'folded')

  if (!isPlanTreeView) {
    if (node.level > 0) {
      row.classList.add('d-none')
    }
    if (node.hasChildren) {
      row.classList.add('node-has-children')
    }
  }

  row.innerHTML = `<div class="node-status"></div>
<div class="node-body">
<div class="node-title">${node.title.replace(/\r?\n/g, "\r\n")}</div>
<div class="node-voice-response">
    <div>
        <span class="final_span"></span>
        <span class="interim_span"></span>
    </div>
    <div class="result_span"></div>
    <div class="retelling-response"></div>
</div>
</div>
<div class="node-control"></div>
  `;

  row.querySelector('.node-title').addEventListener('click', e => {
    if (!e.target.classList.contains('target-text')) {
      return
    }
    if (e.target.classList.contains('selected')) {
      const el = row.querySelector('.node-control .gn')
      $(el).data('abort', true)
      el.click()
    }
  })

  if (!isPlanTreeView) {
    if (node.hasChildren) {
      const childToggle = document.createElement('div')
      childToggle.classList.add('node-children-toggle')
      childToggle.innerHTML = `
<svg style="pointer-events: none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="chevron-down">
  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
</svg>
<svg style="pointer-events: none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="chevron-up">
  <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
</svg>
`
      childToggle.addEventListener('click', e => {
        e.target.classList.toggle('show-children')
        const parentLevel = row.dataset.level
        const nextLevel = Number(parentLevel) + 1
        let childRow = row.nextSibling
        while (childRow) {
          if (childRow.dataset.level === nextLevel.toString()) {
            childRow.classList.toggle('d-none')
          } else {
            break
          }
          childRow = childRow.nextSibling
        }
      })
      row.querySelector('.node-body').appendChild(childToggle)
    }
  }

  return row;
}

function ItemWrapper(listItem, {isPlanTreeView}) {
  return {
    getTargetText() {
      return isPlanTreeView ? listItem.description : listItem.title
    }
  }
}

function processTreeNodes(list, body, history, voiceResponse, params, onEndHandler, dispatchEvent) {
  console.log('processTreeNodes')

  let showVoiceControl = false

  for (const listItem of list) {
    const listItemWrapper = new ItemWrapper(listItem, {isPlanTreeView: params.isPlanTreeView});

    const rowElement = body.querySelector(`.node-row[data-node-id='${listItem.id}']`)
    const nodeId = rowElement.dataset.nodeId
    const historyItem = history.find(i => i.id === nodeId)

    if (historyItem && historyItem.pending) {
      continue
    }

    resetNodeRow(rowElement)

    const nodeStatusElement = rowElement.querySelector('.node-status')

    if (historyItem && historyItem.done) {
      nodeStatusElement.innerHTML = nodeStatusSuccessHtml
      continue
    }

    if (historyItem && historyItem.repeat) {
      nodeStatusElement.innerHTML = nodeStatusFailedHtml
    }

    const statusElem = nodeStatusElement.querySelector('.retelling-status-show')
    if (statusElem) {
      statusElem.addEventListener('click', e => {
        const nodeId = e.target.closest('.node-row').dataset.nodeId
        const item = history.find(i => i.id === nodeId)
        const content = createRetellingFeedbackContent(listItemWrapper.getTargetText(), item.user_response, item.json)
        rowElement.closest('.mental-map').appendChild(content)
      })
    }

    if (historyItem.done) {
      continue
    }

    if (showVoiceControl) {
      continue
    }

    rowElement.classList.remove('folded')

    const voiceResponseElem = rowElement.querySelector('.node-voice-response')
    const resultSpan = voiceResponseElem.querySelector('.result_span')
    const retellingResponseSpan = voiceResponseElem.querySelector('.retelling-response')
    const finalSpan = voiceResponseElem.querySelector('.final_span')
    const interimSpan = voiceResponseElem.querySelector('.interim_span')

    const startClickHandler = targetElement => {

      targetElement.closest('.node-row').classList.add('pending')
      historyItem.pending = true

      $(rowElement.querySelector('.gn'))
        .tooltip('hide')
        .attr('title', 'Остановить запись и запустить проверку')
        .tooltip('fixTitle')
      finalSpan.innerHTML = ''
      interimSpan.innerHTML = ''
      resultSpan.innerHTML = ''
      rowElement.querySelectorAll('.target-text').forEach(el => el.classList.add('selected'))
      rowElement.querySelector('.node-status').innerHTML = ''
      rowElement.classList.add('current-row')
      rowElement.parentNode.classList.add('do-recording')

      voiceResponse.onResult(args => {
        finalSpan.innerHTML = args.args?.result
        interimSpan.innerHTML = args.args?.interim
      })

      // voiceResponse.onEnd(args => {})
    }

    const stopClickHandler = async (targetElement, abort) => {

      $(rowElement.querySelector('.gn'))
        .tooltip('hide')
        .attr('title', 'Нажмите, что бы начать запись с микрофона')
        .tooltip('fixTitle')
      rowElement.classList.remove('current-row')
      rowElement.parentNode.classList.remove('do-recording')
      rowElement.querySelectorAll('.target-text').forEach(el => el.classList.remove('selected'))

      const historyItem = history.find(i => i.id === nodeId)

      if (abort) {
        targetElement.closest('.node-row').classList.remove('pending')
        historyItem.pending = false
        return
      }

      if (finalSpan.innerHTML.trim().length) {
        resultSpan.innerHTML +=
          resultSpan.innerHTML.trim().length
            ? resultSpan.innerHTML.trim() + "\n" + finalSpan.innerHTML.trim()
            : finalSpan.innerHTML.trim()

        finalSpan.innerHTML = ''
      }

      let userResponse = resultSpan.innerHTML.trim();
      if (!userResponse.length) {
        return
      }

      processTreeNodes(list, body, history, voiceResponse, params, onEndHandler, dispatchEvent)

      const rootElement = targetElement.closest('.node-row')
      const backdrop = createRewriteContent('Обработка ответа...')
      rootElement.appendChild(backdrop.getElement())

      const similarityChecker = new SimilarityChecker(params.threshold)

      let responseIsSuccess = similarityChecker.check(listItemWrapper.getTargetText(), userResponse)
      if (responseIsSuccess) {
        retellingResponseSpan.innerText = `{"similarity_percentage": ${similarityChecker.getSimilarityPercentage()}, "all_important_words_included": true, "user_response": ${JSON.stringify(userResponse)}}`
      }

      //const rewriteByPass = similarityChecker.getSimilarityPercentage() >= 90
      //console.log('rewriteByPass', rewriteByPass)

      if (!responseIsSuccess) {
        //const removePunctuation = text => text.replace(/[!"#$%&'()*+,-./:;<=>?@[\]^_`{|}–«»~]/g, '').replace(/\s{2,}/g, " ")
        await sendMessage(
          `/admin/index.php?r=gpt/stream/retelling-rewrite`,
          {
            userResponse: userResponse,
            slideTexts: stripTags(listItemWrapper.getTargetText())
          },
          (message) => resultSpan.innerHTML = message,
          () => console.log('rewrite error'),
          () => console.log('rewrite end')
        )
        userResponse = resultSpan.innerHTML.trim();
        responseIsSuccess = similarityChecker.check(listItemWrapper.getTargetText(), userResponse)
        if (responseIsSuccess) {
          retellingResponseSpan.innerText = `{"similarity_percentage": ${similarityChecker.getSimilarityPercentage()}, "all_important_words_included": true, "user_response": ${JSON.stringify(userResponse)}}`
        }
      }

      historyItem.user_response = userResponse;

      if (!responseIsSuccess) {
        retellingResponseSpan.innerText = ''
        const remPunctuation = text => text.replace(/[!"#$%&'()*+,./:;<=>?@[\]^`{|}«»~]/g, '').replace(/\s{2,}/g, " ")
        await sendMessage(`/admin/index.php?r=gpt/stream/retelling-tree`, {
            userResponse,
            slideTexts: stripTags(listItemWrapper.getTargetText()).replaceAll('_', ' '),
            importantWords: $(`<div>${listItemWrapper.getTargetText()}</div>`)
              .find('span.target-text')
              .map((i, el) => remPunctuation($(el).text()).replaceAll('_', ' '))
              .get()
              .join(', '),
            promptId: params.settingsPromptId || listItem.promptId
          },
          (message) => retellingResponseSpan.innerText = message,
          (error) => {
            backdrop.setErrorText(error, () => {
              backdrop.remove()
              //stopClickHandler(targetElement)
            })
          },
          () => {}
        )
      }

      const content = rowElement.querySelector('.node-title').innerHTML

      const json = processOutputAsJson(retellingResponseSpan.innerText)
      if (json === null) {
        backdrop.setErrorText('Json error', () => backdrop.remove())
        return
      }

      backdrop.remove()

      const val = Number(json.similarity_percentage)
      let importantWordsPassed = true

      if (json.all_important_words_included !== undefined) {
        importantWordsPassed = Boolean(json.all_important_words_included)
      }

      historyItem.json = retellingResponseSpan.innerHTML

      if (val >= params.threshold && importantWordsPassed) {
        nodeStatusElement.innerHTML = nodeStatusSuccessHtml
        historyItem.done = true
        historyItem.repeat = false
      } else {
        nodeStatusElement.innerHTML = nodeStatusFailedHtml
        historyItem.done = false
        historyItem.repeat = true
      }

      historyItem.pending = false
      processTreeNodes(list, body, history, voiceResponse, params, onEndHandler, dispatchEvent)
      console.log('after processTreeNodes', json, historyItem)

      dispatchEvent('historyChange', {
        currentHistory: history
      })

      const wordItems = createWordItem(listItemWrapper.getTargetText(), listItem.id)
      wordItems.words = [...wordItems.words].map(w => {
        if (w.type === 'word' && w.target === true) {
          w.hidden = true
        }
        return w
      })
      const textHidingPercentage = calcHiddenTextPercent(wordItems)

      saveUserResult({
        ...params,
        image_fragment_id: nodeId,
        overall_similarity: Number(json.similarity_percentage),
        text_hiding_percentage: textHidingPercentage,
        text_target_percentage: textHidingPercentage > 0 ? 100 : 0, // textTargetPercentage,
        content,
        user_response: resultSpan.innerText,
        api_response: JSON.stringify(json),
        payload: json,
        all_important_words_included: importantWordsPassed
      })/*.then(response => {
        if (response && response.success) {
          historyItem.all = response.history.all
          historyItem.hiding = response.history.hiding
          historyItem.target = response.history.target
        }
        //resetNodeRow(rowElement)
      })*/

      historyItem.all = Number(json.similarity_percentage)
      historyItem.hiding = textHidingPercentage
      historyItem.target = textHidingPercentage > 0 ? 100 : 0

      const allIsDone = history.reduce((all, val) => all && val.done, true)
      if (allIsDone && typeof onEndHandler === "function") {
        console.log('all is done')
        onEndHandler()
      }
    }

    const treeVoiceControlElement = TreeVoiceControl(
      voiceResponse,
      startClickHandler,
      stopClickHandler
    )

    rowElement.querySelector('.node-control').appendChild(treeVoiceControlElement)

    if (!rowElement.checkVisibility()) {
      const level = Number(rowElement.dataset.level)
      if (level > 0) {
        rowElement.parentNode.querySelectorAll(`.node-row[data-level='${level}']`)
          .forEach(children => children.classList.toggle('d-none'))
      }
    }

    //rowElement.scrollIntoView({block: 'start', behavior: 'smooth'});

    showVoiceControl = true
    //break
  }

  /*const allIsDone = history.reduce((all, val) => all && val.done, true)
  if (allIsDone && typeof onEndHandler === "function") {
    onEndHandler()
  }*/
}

function flatten(nodes, level = 0) {
  return nodes.flatMap((node, index) => [
    {...node, level, index, hasChildren: (node.children || []).length > 0},
    ...flatten(node.children || [], level + 1)
  ])
}

export default function TreeViewBody(tree, voiceResponse, history, params, onEndHandler, isPlanTreeView, settingsPromptId) {

  const init = () => {
    const body = document.createElement('div')
    body.classList.add('tree-body')
    return body
  }

  function dispatchEvent(type, args) {
    const event = document.createEvent('HTMLEvents', 1, 2);
    event.initEvent(type, true, true);
    extend(event, args);
    body.dispatchEvent(event);
  }

  let body = init()

  params.isPlanTreeView = isPlanTreeView
  params.settingsPromptId = settingsPromptId

  voiceResponse.onError(({args}) => {
    const row = body.querySelector('.node-row.current-row.pending')
    if (row) {
      body.classList.remove('do-recording')
      const id = row.getAttribute('data-node-id')
      const historyItem = history.find(h => h.id === id)
      historyItem.pending = false
      body.innerHTML = ''
      API.init()
    }
  })

  const API = {
    getElement() {
      return body
    },
    init() {
      const list = flatten(tree)
      list.map(node => body.appendChild(createRow(node, 0, isPlanTreeView)))
      processTreeNodes(list, body, history, voiceResponse, params, onEndHandler, dispatchEvent)
    },
    restart() {
      body.remove()
      history = history.map(h => ({...h, done: false, all: 0, pending: false, repeat: false}))
      body = init()
      this.init()
      return body
    },
    on(type, listener, useCapture) {
      body.addEventListener(type, listener, useCapture)
    }
  }

  return API
}

function createRewriteContent(text, hideCallback) {
  const wrap = document.createElement('div')
  wrap.classList.add('retelling-wrap')
  wrap.style.backgroundColor = 'transparent'
  wrap.style.padding = '0'
  wrap.innerHTML = `
      <div class="retelling-status">
        <div class="retelling-info-text">${text}</div>
        <img class="retelling-loader" src="/img/loading.gif" alt="..." />
        <button class="btn retelling-resend" type="button">Повторить</button>
      </div>
    `
  return {
    getElement() {
      return wrap
    },
    setText(text) {
      wrap.querySelector('.retelling-info-text').textContent = text
    },
    setErrorText(text, resendHandler) {
      wrap.querySelector('.retelling-status').classList.add('retelling-status-error')
      wrap.querySelector('.retelling-info-text').textContent = text
      if (resendHandler !== undefined) {
        wrap.querySelector('.retelling-resend').addEventListener('click', resendHandler)
      }
    },
    remove() {
      wrap.remove()
    }
  }
}

function resetNodeRow(row) {
  if (!row.classList.contains('folded')) {
    row.classList.add('folded')
  }
  row.querySelector('.node-status').innerHTML = ''
  const voiceResponseElem = row.querySelector('.node-voice-response');
  ['.result_span', '.retelling-response', '.final_span', '.interim_span']
    .map(s => voiceResponseElem.querySelector(`${s}`).innerHTML = '')
  row.querySelector('.node-control').innerHTML = ''
}

async function saveUserResult(payload) {
  const response = await fetch(`/mental-map/save`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
    },
    body: JSON.stringify(payload),
  })
  if (!response.ok) {
    throw new Error(response.statusText)
  }
  return await response.json()
}

function createNotify(text) {
  const div = document.createElement('div')
  div.classList.add('mental-map-notify')
  div.innerHTML = `
<div style="display: flex">
    <div class="mental-map-notify-text">${text}</div>
    <button class="mental-map-notify-button" type="button"></button>
</div>
`

  div.querySelector('.mental-map-notify-button').addEventListener('click', e => div.remove())

  setTimeout(() => div.remove(), 5000)

  return div
}

const removePunctuation = text => text.replace(/[!"#$%&'()*+,-./:;<=>?@[\]^`{|}–«»~]/g, '').replace(/\s{2,}/g, " ")

function createRetellingFeedbackContent(text, userResponse, apiResponse) {
  const wrap = document.createElement('div')
  wrap.classList.add('feedback-content-wrap')
  wrap.innerHTML = `<div class="feedback-content-backdrop"></div>
<div class="feedback-content">
    <div style="height: 100%; max-height: 100%; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden">
        <div><div style="margin-bottom: 20px">
                    <p style="font-weight: 500;color: black !important;">Исходный текст:</p>
                    <p style="color: black !important">${text}</p>
                </div>
                <div style="margin-bottom: 20px">
                    <p style="font-weight: 500; color: black !important">После обработки:</p>
                    <p style="color: black !important">${userResponse}</p>
                </div></div>

        <div style="overflow-y: scroll">
            <p style="font-weight: 500;">Результат сравнения:</p>
            <div style="font-size: 2.2rem; text-align: left; line-height: 3rem">${apiResponse}</div>
        </div>
    </div>
    <div style="display: flex; margin-top: 10px; flex-direction: row; align-items: center; justify-content: center">
        <button type="button" class="btn close-dialog">OK</button>
    </div>
</div>
    `

  wrap.querySelector('.close-dialog')
    .addEventListener('click', () => wrap.remove())

  return wrap
}

function extend(a, b) {
  for (let i in b) {
    a[i] = b[i];
  }
  return a;
}
