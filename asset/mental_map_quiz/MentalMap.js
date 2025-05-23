import './MentalMap.css'
import InnerDialog from "./Dialog";
import VoiceResponse from "./lib/VoiceResponse"
import MissingWordsRecognition from "./lib/MissingWordsRecognition"
import {v4 as uuidv4} from "uuid"
import DetailText from "./components/DetailText";
import AllTexts, {appendAllTextWordElements} from "./components/AllTexts";
import MentalMapImage from "./components/MentalMapImage";
import FragmentResultElement from "./components/FragmentResultElement";
import sendEventSourceMessage from "../app/sendEventSourceMessage";
import Panzoom from "../app/panzoom.min"
import RewritePromptBtn from "./components/RewritePromptBtn";
import TreeView from "./TreeView/TreeView";

function decodeHtml(html) {
  const txt = document.createElement("textarea");
  txt.innerHTML = html;
  return txt.value;
}

function processImageText(text) {
  const textFragments = new Map();
  const reg = new RegExp(`<span[^>]*>(.*?)<\\/span>`, 'gm');
  const imageText = decodeHtml(text.replace(/&nbsp;/g, ' ')).replace(reg, (match, p1) => {
    const id = uuidv4()
    textFragments.set(`${id}`, `${p1.trim()}`)
    return `{${id}}`
  })
  return {
    imageText,
    textFragments
  }
}

export function createWordItem(itemText, itemId) {
  const {imageText, textFragments} = processImageText(itemText)
  const paragraphs = imageText.split('\n')
  const words = paragraphs.map(p => {
    if (p === '') {
      return [{type: 'break'}]
    }
    const words = p.split(' ').filter(w => w).map(word => {
      if (word.indexOf('{') > -1) {
        const id = word.toString().replace(/[^\w\-]+/gmui, '')
        if (textFragments.has(id)) {
          const reg = new RegExp(`{${id}}`)
          word = word.replace(reg, textFragments.get(id))
          return word.split(' ').map(w => ({id: uuidv4(), word: w, type: 'word', hidden: false, target: true}))
        }
      }
      return [{id: uuidv4(), word, type: 'word', hidden: false}]
    })
    return [...(words.flat()), {type: 'break'}]
  }).flat()
  return {
    id: itemId,
    text: itemText,
    words
  }
}

export function calcHiddenTextPercent(detailTexts) {
  let totalCounter = 0;
  let hiddenCounter = 0;
  detailTexts.words.map(w => {
    if (w.type === 'word') {
      totalCounter++;
      if (w.hidden) {
        hiddenCounter++
      }
    }
  })
  return totalCounter === 0 || hiddenCounter === 0 ? 0 : Math.round(hiddenCounter * 100 / totalCounter)
}

/**
 * @param element
 * @param {Reveal|undefined} deck
 * @param params
 * @returns {{run: ((function(): Promise<void>)|*)}}
 * @constructor
 */
export default function MentalMap(element, deck, params) {

  this.element = element
  let texts = []
  let mentalMapHistory = []
  let mentalMapId

  params = params || {}
  params.slide_id = deck ? Number($(deck.getCurrentSlide()).attr('data-id')) : null

  const repetitionMode = Boolean(params?.repetitionMode)
  const getCourseMode = Boolean(params?.getCourseMode)

  const voiceResponse = new VoiceResponse(new MissingWordsRecognition({}))
  voiceResponse.onResult(args => {
    const finalSpan = document.getElementById("final_span")
    if (finalSpan) {
      finalSpan.innerHTML = args.args?.result
    }
    const interimSpan = document.getElementById("interim_span")
    if (interimSpan) {
      interimSpan.innerHTML = args.args?.interim
    }
  })

  const container = document.createElement('div')
  container.classList.add('mental-map-container')

  const blockTypes = ['text', 'image']

  function showDialogHandler() {
    if (!deck) {
      return
    }
    deck.configure({keyboard: false});
    $('.reveal .story-controls').hide();
    blockTypes.map(blockType => {
      $(deck.getCurrentSlide()).find(`div.sl-block[data-block-type=${blockType}]`).css('zIndex', '-1')
    })
  }

  function hideDialogHandler() {
    if (!deck) {
      return
    }
    if ($(deck.getCurrentSlide()).find('.slide-hints-wrapper').length) {
      return
    }
    deck.configure({keyboard: true})
    $('.reveal .story-controls').show();
    blockTypes.map(blockType => {
      $(deck.getCurrentSlide()).find(`div.sl-block[data-block-type=${blockType}]`).css('zIndex', 'auto')
    })
  }

  function getTargetWordsCount(detailTexts) {
    return detailTexts.words.filter(w => w.type === 'word' && w?.target).length
  }

  function calcTargetTextPercent(detailTexts) {
    let targetCounter = 0;
    let targetHiddenCounter = 0;
    detailTexts.words.map(w => {
      if (w.type === 'word' && w?.target) {
        targetCounter++
        if (w.hidden) {
          targetHiddenCounter++
        }
      }
    })
    return targetCounter === 0 || targetHiddenCounter === 0 ? 0 : Math.round(targetHiddenCounter * 100 / targetCounter)
  }

  function canRecording(detailTexts) {
    if (getTargetWordsCount(detailTexts) === 0) {
      return true
    }
    return calcTargetTextPercent(detailTexts) === 100
  }

  function processOutputAsJson(output) {
    let json = null
    try {
      json = JSON.parse(output.replace(/```json\n?|```/g, ''))
    } catch (ex) {

    }
    return json
  }

  function RecordingLangStore(defaultLang) {
    let lang = defaultLang
    return {
      fromStore(langValue) {
        if (langValue !== lang) {
          lang = langValue
        }
        return lang
      },
      get() {
        return lang
      }
    }
  }

  const langStore = new RecordingLangStore('ru-RU')

  function mapImageClickHandler(image, texts, historyItem, rewritePrompt, threshold) {
    const detailImgWrap = document.createElement('div')
    detailImgWrap.classList.add('image-item')

    if (image.url) {
      const detailImg = document.createElement('img')
      detailImg.src = image.url
      detailImg.style.marginBottom = '10px'
      detailImgWrap.appendChild(detailImg)
    } else {
      const div = document.createElement('div')
      div.style.marginBottom = '10px'
      div.style.padding = '20px'
      div.style.cursor = 'pointer'
      div.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
</svg>
`
      detailImgWrap.appendChild(div)
    }

    detailImgWrap.appendChild(FragmentResultElement(historyItem))

    const text = texts.find(t => t.id === image.id)

    const detailTextWrap = document.createElement('div')
    detailTextWrap.classList.add('detail-text-wrap')

    let promptBtn;
    if (rewritePrompt) {
      promptBtn = RewritePromptBtn(() => {
        const content = createUpdatePromptContent(rewritePrompt, (currentPrompt, close) => {
          rewritePrompt = currentPrompt
          saveRewritePrompt(currentPrompt).then(r => close())
        })
        dialog.getWrapper().append(content)
      })
    }

    const detailText = DetailText(text, () => {
      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
        const voiceLang = langStore.fromStore($(recordingWrap).find("#voice-lang option:selected").val())
        startRecording(recordingWrap.querySelector('#start-recording'), voiceLang, stripTags(text.text), false)
      }
      /*['#result_span', '#final_span', '#interim_span'].map(q => {
        detailTextWrap.querySelector(q).innerHTML = ''
        recordingWrap.querySelector('#start-retelling-wrap').style.display = 'none'
      })*/
      recordingWrap.querySelector('#hidden-text-percent').innerText = calcHiddenTextPercent(text) + '%'

      const gm = recordingWrap.querySelector('#start-recording')

      $(gm)
        .removeAttr('title')
        .tooltip('destroy')

      gm.classList.remove('disabled')
      if (!canRecording(text)) {
        gm.classList.add('disabled')
        $(gm)
          .attr('title', 'Нужно закрыть все важные слова')
          .tooltip()
      }
      recordingWrap.querySelector('#target-text-percent').innerText = calcTargetTextPercent(text) + '%'
    }, () => {
      recordingWrap.querySelector('#hidden-text-percent').innerText = calcHiddenTextPercent(text) + '%'
      recordingWrap.querySelector('#target-text-percent').innerText = calcTargetTextPercent(text) + '%'
    }, promptBtn)
    detailTextWrap.appendChild(detailText)

    const recordingContainer = document.createElement('div')
    recordingContainer.classList.add('recording-container')
    recordingContainer.innerHTML = `
      <div style="font-size: 2.2rem; line-height: 2.6rem; margin-bottom: 10px; color: #808080">Ответ ученика:</div>
      <div style="background-color: #eee; font-size: 2.2rem; line-height: 3rem">
            <span contenteditable="plaintext-only" id="result_span"
                  class="recording-final" style="line-height: 3rem"></span>
            <span contenteditable="plaintext-only" id="final_span"
                  class="recording-result" style="line-height: 3rem"></span>
            <span id="interim_span" class="recording-interim"></span>
        </div>
    `

    detailTextWrap.appendChild(recordingContainer)

    const detailContent = document.createElement('div')
    detailContent.classList.add('mental-map-detail-content')
    detailContent.appendChild(detailImgWrap)
    detailContent.appendChild(detailTextWrap)

    const detailContainerInner = document.createElement('div')
    detailContainerInner.classList.add('mental-map-detail-container-inner')
    detailContainerInner.appendChild(detailContent)

    const recordingWrap = document.createElement('div')
    recordingWrap.classList.add('recording-status')
    recordingWrap.innerHTML = `<div class="mental-map-text-status">
    <div style="margin-bottom: 10px">Текст скрыт на: <strong id="hidden-text-percent"></strong></div>
    <div style="margin-bottom: 10px">Важный текст: <strong id="target-text-percent"></strong></div>
    <div>Сходство: <strong id="similarity-percent"></strong></div>
</div>
<div style="display: flex; align-items: center;">
    <select class="form-control" id="voice-lang" style="margin-right: 20px; font-size: 24px; height: auto">
        <option value="ru-RU" selected>rus</option>
        <option value="en-US">eng</option>
    </select>
    <div class="question-voice" style="bottom: 0; display: flex; position: relative;">
        <div class="question-voice__inner">
            <div id="start-recording" class="gn">
                <div class="mc" style="pointer-events: none"></div>
            </div>
        </div>
    </div>
</div>
<div class="retelling-container" id="start-retelling-wrap" style="display: none; text-align: center">
    <button class="btn" type="button" id="start-retelling">Проверить</button>
    <label style="display: block; font-weight: normal; font-size: 2.2rem; margin-top: 10px" for="clear-text"><input
            style="transform: scale(1.5); margin-right: 10px" type="checkbox" id="clear-text" checked> без
        знаков</label>
</div>`
    detailContainerInner.appendChild(recordingWrap)

    const detailContainer = document.createElement('div')
    detailContainer.classList.add('mental-map-detail-container')
    detailContainer.appendChild(detailContainerInner)

    const dialog = new InnerDialog($(container), {title: 'Изображение', content: detailContainer});
    dialog.show(wrapper => {
      showDialogHandler()

      $(wrapper).on('paste', (e) => {
        e.preventDefault()
        return false
      })

      $(wrapper).find('.result-item-value').tooltip()

      $(wrapper).find(`#voice-lang`).val(langStore.get())

      wrapper.querySelector('#start-recording').addEventListener('click', e => {

        if (!canRecording(text)) {
          return
        }

        if (!voiceResponse.getStatus()) {
          ['#result_span', '#final_span', '#interim_span'].map(q => {
            wrapper.querySelector(q).innerHTML = ''
            wrapper.querySelector('#start-retelling-wrap').style.display = 'none'
          });
        }

        const voiceLang = langStore.fromStore($(wrapper).find("#voice-lang option:selected").val());
        startRecording(e.target, voiceLang, stripTags(text.text), true)
      })
      wrapper.querySelector('#start-retelling').addEventListener('click', e => {

        if (voiceResponse.getStatus()) {
          voiceResponse.stop()
        }

        const userResponse = wrapper.querySelector('#result_span').innerText.trim()
        if (!userResponse) {
          alert("Ответ пользователя пуст")
          return
        }

        const content = createRetellingContent(() => {
          ['#result_span', '#final_span', '#interim_span'].map(q => {
            wrapper.querySelector(q).innerHTML = ''
            wrapper.querySelector('#start-retelling-wrap').style.display = 'none'
          })
        })
        wrapper.querySelector('.mental-map-detail-container').appendChild(content)

        const clearText = $(wrapper).find('#clear-text').is(':checked')

        const removePunctuation = text => text.replace(/[!"#$%&'()*+,-./:;<=>?@[\]^_`{|}–«»~]/g, '').replace(/\s{2,}/g, " ")

        startRetelling(clearText ? removePunctuation(userResponse) : userResponse, clearText ? removePunctuation(stripTags(text.text)) : stripTags(text.text)).then(response => {
          const json = processOutputAsJson(wrapper.querySelector('#retelling-response').innerText)
          if (json) {
            const val = Number(json?.overall_similarity)
            recordingWrap.querySelector('#similarity-percent').innerText = `${val}%`

            const textHidingPercentage = calcHiddenTextPercent(text)
            const textTargetPercentage = calcTargetTextPercent(text)

            const detailTextContent = detailText.cloneNode(true)
            detailTextContent.querySelector('.detail-text-actions').remove()

            saveUserResult({
              story_id: params?.story_id,
              slide_id: params?.slide_id,
              mental_map_id: mentalMapId,
              image_fragment_id: image.id,
              overall_similarity: Number(json?.overall_similarity),
              text_hiding_percentage: textHidingPercentage,
              text_target_percentage: textTargetPercentage,
              content: detailTextContent.innerHTML,
              repetition_mode: repetitionMode,
              threshold
            }).then(response => {
              if (response && response?.success) {
                historyItem.all = response.history.all
                historyItem.hiding = response.history.hiding
                historyItem.target = response.history.target
                historyItem.done = response.history.done

                // wrapper.querySelector('.result-item-value').innerHTML = `${val}% (${textHidingPercentage}% / ${textTargetPercentage}%)`
                wrapper.querySelector('.image-item > .result-item').remove()
                wrapper.querySelector('.image-item').appendChild(FragmentResultElement(historyItem))
              }
            })
          }
        })
      })

      recordingWrap.querySelector('#hidden-text-percent').innerText = calcHiddenTextPercent(text) + '%'
      recordingWrap.querySelector('#target-text-percent').innerText = calcTargetTextPercent(text) + '%'

      wrapper.querySelector('#result_span').addEventListener('input', e => {
        const text = e.target.innerText
        const display = text.length > 0 ? 'block' : 'none'
        if (display !== wrapper.querySelector('#start-retelling-wrap').style.display) {
          wrapper.querySelector('#start-retelling-wrap').style.display = display
        }
      })
    })

    return dialog
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

  async function saveRewritePrompt(newPrompt) {
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

  async function finishRepetition(mentalMapId) {
    const response = await fetch(`/mental-map/finish`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      },
      body: JSON.stringify({
        mental_map_id: mentalMapId
      }),
    })
    if (!response.ok) {
      throw new Error(response.statusText)
    }
    return await response.json()
  }

  function stripTags(html) {
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || '';
  }

  function showMentalMapHandler(zoomWrap, closeMentalMapHandler) {
    const zoomContainer = document.createElement('div')
    zoomContainer.classList.add('zoom-container')

    zoomContainer.appendChild(zoomWrap)

    const closeBtn = document.createElement('button')
    closeBtn.classList.add('btn', 'btn-small', 'mental-map-close')
    closeBtn.textContent = 'Закрыть'
    closeBtn.addEventListener('click', closeMentalMapHandler)
    zoomContainer.appendChild(closeBtn)

    const hideBtn = document.createElement('button')
    hideBtn.classList.add('btn', 'btn-small', 'mental-map-hide-btn')
    hideBtn.textContent = 'Скрыть'
    hideBtn.addEventListener('click', e => {
      $(e.target).toggleClass('img-hide')
      if ($(e.target).hasClass('img-hide')) {
        $(zoomWrap).find('.mental-map-img .map-img')
          .each((i, el) => $(el).css({opacity: '0'}))
        $(zoomWrap).find('.mental-map-img').each((i, el) => {
          $(el).append(`<span class="mental-map-point"></span>`)
        })
        $(e.target).text('Показать')
      } else {
        $(zoomWrap).find('.mental-map-img span').remove()
        $(zoomWrap).find('.mental-map-img .map-img')
          .each((i, el) => $(el).css({opacity: '1'}))
        $(e.target).text('Скрыть')
      }
    })
    zoomContainer.appendChild(hideBtn)

    return zoomContainer
  }

  function historyIsDone(history) {
    return history.reduce((all, val) => all && val.done, true)
  }

  function initPanZoom(element, mapWidth, mapHeight) {
    let initialZoom = 0.8
    const containerWidth = container.offsetWidth
    const containerHeight = container.offsetHeight

    if (mapHeight > containerHeight) {
      initialZoom = containerHeight / mapHeight
    } else {
      initialZoom = 1;
    }

    if (mapWidth > containerWidth) {
      initialZoom = containerWidth / mapWidth
    }

    return Panzoom(element, {
      excludeClass: 'mental-map-img',
      startScale: initialZoom,
      minScale: 0.4,
      maxScale: 2,
    })
  }

  const run = async () => {

    let responseJson
    try {
      responseJson = await params.init()
    } catch (ex) {
      container.innerText = ex.message
      this.element.appendChild(container)
      return
    }

    const {mentalMap: json, history, rewritePrompt, threshold} = responseJson

    mentalMapId = json.id
    mentalMapHistory = history

    const {treeView} = json
    if (treeView) {
      this.element.appendChild(TreeView({
        id: json.id,
        name: json.name,
        tree: json.treeData,
        history,
        params: {
          story_id: params?.story_id,
          slide_id: params?.slide_id,
          mental_map_id: params.mentalMapId,
          repetition_mode: repetitionMode,
          threshold
        }
      }, new VoiceResponse(new MissingWordsRecognition({}))))

      $('[data-toggle="tooltip"]', this.element).tooltip({
        container: 'body'
      });

      return
    }

    /*document.addEventListener('visibilitychange', e => {
      console.log('visibilitychange')
        if (voiceResponse.getStatus()) {
          voiceResponse.stop()
          const el = document.querySelector('#start-recording')
          if (el) {
            $(el).trigger('click')
          }
        }
    }, false)*/

    window.addEventListener('blur', function() {
      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
        const el = document.querySelector('#start-recording')
        if (el) {
          $(el).trigger('click')
        }
      }
    }, false);

    texts = json.map.images.map(image => createWordItem(image.text, image.id))

    const imageFirst = Boolean(json.settings?.imageFirst)

    function fragmentDialogHideHandler(image, historyItem) {
      hideDialogHandler()

      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
      }

      const el = container.querySelector(`[data-image-fragment-id='${image.id}']`)
      el.querySelector('.image-item > .result-item').remove()
      el.querySelector('.image-item').appendChild(FragmentResultElement(historyItem))
      $(el.querySelector('.result-item')).tooltip()

      // appendAllTextWordElements(texts.find(t => t.id === image.id).words, el.querySelector('.text-item'))

      if (repetitionMode) {
        const done = mentalMapHistory.reduce((all, val) => all && val.done, true)
        if (done) {
          const content = createFinishRepetitionContent()
          $(container).parents('.mental-map').append(content)
          finishRepetition(params.mentalMapId)
        }
      }

      /*texts = texts.map(t => {
        if (t.id === image.id) {
          return createWordItem(image)
        }
        return t
      })*/

      if (/*getCourseMode &&*/ historyIsDone(history)) {
        const content = createFinishContent(history, texts)
        $(container).parents('.mental-map').append(content)
      }
    }

    container.appendChild(AllTexts(texts, json.map.images, history, (image) => {
      const historyItem = history.find(h => h.id === image.id)
      const dialog = mapImageClickHandler(image, texts, historyItem, rewritePrompt, threshold)
      dialog.onHide(() => fragmentDialogHideHandler(image, historyItem))
    }))

    const toolbar = document.createElement('div')
    toolbar.classList.add('mental-map-toolbar')

    const mentalMapBtn = document.createElement('button')
    mentalMapBtn.classList.add('btn', 'btn-small', 'mental-map-btn')
    mentalMapBtn.textContent = 'Ментальная карта'
    let zoom
    mentalMapBtn.addEventListener('click', (e) => {

      //const zoomContainer = document.createElement('div')
      //zoomContainer.classList.add('zoom-container')

      const zoomWrap = MentalMapImage(
        json.map.url,
        `${json.map.width}px`,
        `${json.map.height}px`,
        json.map.images,
        (image) => {
          element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
          const historyItem = history.find(h => h.id === image.id)
          const dialog = mapImageClickHandler(image, texts, historyItem, rewritePrompt, threshold)
          dialog.onHide(() => {
            element.parentElement.addEventListener('wheel', zoom.zoomWithWheel)

            const imgElem = zoomContainer.querySelector(`[data-img-id='${image.id}']`)
            if (historyItem.done) {
              imgElem.classList.add('fragment-item-done')
            }

            fragmentDialogHideHandler(image, historyItem)
          })
        },
        mentalMapHistory
      )

      /*
      zoomContainer.appendChild(zoomWrap)

      const closeBtn = document.createElement('button')
      closeBtn.classList.add('btn', 'btn-small', 'mental-map-close')
      closeBtn.textContent = 'Закрыть'
      closeBtn.addEventListener('click', () => {
        zoom.destroy()
        zoomContainer.remove()
        element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
      })
      zoomContainer.appendChild(closeBtn)

      const hideBtn = document.createElement('button')
      hideBtn.classList.add('btn', 'btn-small', 'mental-map-hide-btn')
      hideBtn.textContent = 'Скрыть'
      hideBtn.addEventListener('click', e => {
        $(e.target).toggleClass('img-hide')
        if ($(e.target).hasClass('img-hide')) {
          $(this.element).find('.mental-map-img .map-img')
            .each((i, el) => $(el).css({opacity: '0'}))
          $(this.element).find('.mental-map-img').each((i, el) => {
            $(el).append(`<span class="mental-map-point"></span>`)
          })
          $(e.target).text('Показать')
        } else {
          $(this.element).find('.mental-map-img span').remove()
          $(this.element).find('.mental-map-img .map-img')
            .each((i, el) => $(el).css({opacity: '1'}))
          $(e.target).text('Скрыть')
        }
      })
      zoomContainer.appendChild(hideBtn)
       */

      const zoomContainer = showMentalMapHandler(zoomWrap, () => {
        zoom.destroy()
        zoomContainer.remove()
        element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
      })

      this.element.appendChild(zoomContainer)

      $('.mental-map-img .map-img').tooltip()

      let initialZoom = 0.8
      const containerWidth = container.offsetWidth
      const containerHeight = container.offsetHeight

      if (json.map.height > containerHeight) {
        initialZoom = containerHeight / json.map.height;
      } else {
        initialZoom = 1;
      }

      if (json.map.width > containerWidth) {
        initialZoom = containerWidth / json.map.width;
      }

      zoom = Panzoom(zoomWrap, {
        excludeClass: 'mental-map-img',
        startScale: initialZoom,
        minScale: 0.4,
        maxScale: 2,
      });
      element.parentElement.addEventListener('wheel', zoom.zoomWithWheel);
    })

    toolbar.appendChild(mentalMapBtn)

    const header = document.createElement('p')
    header.style.marginLeft = '20px'
    header.style.marginBottom = '0'
    header.style.fontSize = '2.2rem';
    header.style.lineHeight = '3rem';
    header.innerHTML = `Точность пересказа установлена в <strong>${threshold}</strong>%`
    toolbar.appendChild(header)

    this.element.appendChild(toolbar)
    this.element.appendChild(container)

    $('.result-item-value').tooltip()

    if (imageFirst) {

      const zoomWrap = MentalMapImage(
        json.map.url,
        `${json.map.width}px`,
        `${json.map.height}px`,
        json.map.images,
        (image) => {

          element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)

          const historyItem = history.find(h => h.id === image.id)
          const dialog = mapImageClickHandler(image, texts, historyItem, rewritePrompt, threshold)

          dialog.onHide(() => {
            element.parentElement.addEventListener('wheel', zoom.zoomWithWheel)

            const imgElem = zoomContainer.querySelector(`[data-img-id='${image.id}']`)
            if (historyItem.done) {
              imgElem.classList.add('fragment-item-done')
            }

            fragmentDialogHideHandler(image, historyItem)

            /*hideDialogHandler()
            if (voiceResponse.getStatus()) {
              voiceResponse.stop()
            }

            const el = container.querySelector(`[data-image-fragment-id='${image.id}']`)
            el.querySelector('.result-item-value').innerHTML = `${historyItem.all}% (${historyItem.hiding}% / ${historyItem.target}%)`
            el.querySelector('.text-item').innerHTML = ''

            appendAllTextWordElements(texts.find(t => t.id === image.id).words, el.querySelector('.text-item'))*/

          })
        },
        mentalMapHistory
      )
      const zoomContainer = showMentalMapHandler(zoomWrap, () => {
        zoom.destroy()
        zoomContainer.remove()
        element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
      })
      this.element.appendChild(zoomContainer)
      $('.mental-map-img .map-img').tooltip()

      zoom = initPanZoom(zoomWrap, json.map.width, json.map.height)
      element.parentElement.addEventListener('wheel', zoom.zoomWithWheel)
    }

    if (/*getCourseMode &&*/ historyIsDone(history)) {
      const content = createFinishContent(history, texts)
      $(container).parents('.mental-map').append(content)
      if (imageFirst) {
        element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
      }
    }
  }

  /**
   * @param {HTMLElement} element
   * @param {string} lang
   * @param text
   */
  function startRecording(element, lang, text, makeRewrite) {
    const state = element.dataset.state
    if (!state) {
      $(document.getElementById("start-retelling-wrap")).hide()
      setTimeout(function () {
        voiceResponse.start(new Event('voiceResponseStart'), lang, function () {
          element.dataset.state = 'recording'
          element.classList.add('recording')

          const ring = document.createElement('div')
          ring.classList.add('pulse-ring')
          element.parentNode.insertBefore(ring, element)
        });
      }, 500);
    } else {
      voiceResponse.stop(function (args) {

        element.parentNode.querySelector('.pulse-ring').remove()
        element.classList.remove('recording')
        delete element.dataset.state

        const $resultSpan = $(document.getElementById("result_span"))
        const $finalSpan = $(document.getElementById("final_span"))

        if ($finalSpan.text().trim().length) {
          $resultSpan.text(
            $resultSpan.text().trim().length
              ? $resultSpan.text().trim() + "\n" + $finalSpan.text().trim()
              : $finalSpan.text().trim()
          )
          $finalSpan.empty()
        }

        if ($resultSpan.text().trim().length) {
          $(document.getElementById("start-retelling-wrap")).show()
        }

        const userResponse = $resultSpan.text().trim()
        if (userResponse.length && makeRewrite) {
          const content = createRewriteContent(() => {})
          $(element).parents('.mental-map-detail-container').append(content)
          sendMessage(
            `/admin/index.php?r=gpt/stream/retelling-rewrite`,
            {
              userResponse,
              slideTexts: text
            },
            (message) => {
              $resultSpan.text(message)
            },
            () => {
              content.remove()
            },
            () => {
              content.remove()
            }
          )
        }
      })
    }
  }

  function createRewriteContent(hideCallback) {
    const wrap = document.createElement('div')
    wrap.classList.add('retelling-wrap')
    wrap.style.backgroundColor = 'transparent'
    wrap.style.padding = '0'
    wrap.innerHTML = `
      <div style="display: flex; align-items: center; justify-content: center; height: 100%; background-color: rgba(255, 255, 255, 0.4); backdrop-filter: blur(4px);">
        <img src="/img/loading.gif" style="width: 100px" />
      </div>
    `
    return wrap
  }

  function createFinishRepetitionContent() {
    const elem = document.createElement('div')
    elem.classList.add('retelling-wrap')
    elem.style.backgroundColor = 'transparent'
    elem.style.padding = '0'
    elem.innerHTML = `
      <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background-color: rgba(255, 255, 255, 0.4); backdrop-filter: blur(4px);">
        <h2 style="margin-bottom: 20px">Ментальная карта пройдена</h2>
        <a class="btn" href="${params.repetitionBackUrl}">Назад к обучению</a>
      </div>
    `
    return elem
  }

  function createFinishContent(history, texts) {
    const elem = document.createElement('div')
    elem.classList.add('retelling-wrap')
    elem.style.backgroundColor = 'transparent'
    elem.style.padding = '0'
    elem.innerHTML = `
      <div class="mental-map-done-wrap" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background-color: rgba(255, 255, 255, 0.4); backdrop-filter: blur(4px);">
        <h2 style="margin-bottom: 20px">Ментальная карта пройдена</h2>
      </div>
    `

    const historyWrap = document.createElement('div')
    historyWrap.style.width = '800px'
    historyWrap.style.backgroundColor = 'white'
    historyWrap.style.padding = '20px'
    historyWrap.style.border = '1px #ddd solid'
    historyWrap.style.borderRadius = '10px'
    historyWrap.style.maxHeight = '500px'
    historyWrap.style.overflowY = 'auto'
    history.map(h => {

      const el = document.createElement('div')
      el.style.marginBottom = '10px'
      el.style.display = 'flex'
      el.style.flexDirection = 'row'
      el.style.columnGap = '20px'

      el.appendChild(FragmentResultElement(h))

      const textEl = document.createElement('div')
      textEl.classList.add('text-item')
      textEl.style.flex = '1 1 auto'
      textEl.style.textAlign = 'left'
      textEl.innerHTML = texts.find(t => t.id === h.id)?.text
      el.appendChild(textEl)

      historyWrap.appendChild(el)
    })

    elem.querySelector('.mental-map-done-wrap').appendChild(historyWrap)

    return elem
  }

  function createUpdatePromptContent(prompt, saveHandler, closeHandler) {
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

  function createRetellingContent(hideCallback) {
    const wrap = document.createElement('div')
    wrap.classList.add('retelling-wrap')
    wrap.innerHTML = `
        <div contenteditable="plaintext-only" id="retelling-response"
             style="font-size: 2.2rem; text-align: left; line-height: 3rem; overflow-y: scroll; height: 100%; max-height: 100%;"></div>
        <div style="display: flex; margin-top: 10px; flex-direction: row; align-items: center; justify-content: center">
            <img id="voice-loader" height="50px" src="/img/loading.gif" alt="">
            <button style="display: none" id="voice-finish" type="button" class="btn">OK</button>
        </div>
    `
    wrap.querySelector('#voice-finish').addEventListener('click', () => {
      wrap.remove()
      if (typeof hideCallback === 'function') {
        hideCallback()
      }
    })
    return wrap
  }

  async function startRetelling(userResponse, targetText) {
    const onMessage = message => {
      const el = document.getElementById("retelling-response")
      $(el).show()
      el.innerText = message
      el.scrollTop = el.scrollHeight
    }
    const onError = message => {
      const el = document.getElementById("retelling-response")
      $(el).show()
      el.innerText = message
      $(document.getElementById('voice-loader')).hide()
      $(document.getElementById('voice-finish')).show()
    }
    const onEnd = () => {
      $(document.getElementById('voice-loader')).hide()
      $(document.getElementById('voice-finish')).show()
    }
    return await sendMessage(`/admin/index.php?r=gpt/stream/retelling`, {
      userResponse,
      slideTexts: targetText
    }, onMessage, onError, onEnd)
  }

  async function sendMessage(url, payload, onMessage, onError, onEnd) {
    let accumulatedMessage = ""

    return sendEventSourceMessage({
      url,
      headers: {
        Accept: "text/event-stream",
        "X-CSRF-Token": $("meta[name=csrf-token]").attr("content")
      },
      body: JSON.stringify(payload),
      onMessage: (streamedResponse) => {
        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }
        onMessage(accumulatedMessage)
      },
      onError: (streamedResponse) => {
        accumulatedMessage = streamedResponse?.error_text
        onError(accumulatedMessage)
      },
      onEnd
    })
  }

  return {
    run,
    canNext() {
      if (params?.mentalMapRequired) {
        return mentalMapHistory.reduce((all, val) => all && val.done, true)
      }
      return true
    }
  }
}
