import './MentalMap.css'
import InnerDialog from "./Dialog";
import VoiceResponse from "./lib/VoiceResponse"
import MissingWordsRecognition from "./lib/MissingWordsRecognition"
import {v4 as uuidv4} from "uuid"

export default function MentalMap(element, params) {

  this.element = element
  let texts = []

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
    Reveal.configure({keyboard: false});
    $('.reveal .story-controls').hide();
    blockTypes.map(blockType => {
      $(Reveal.getCurrentSlide()).find(`div.sl-block[data-block-type=${blockType}]`).css('zIndex', '-1')
    })
  }

  function hideDialogHandler() {
    if ($(Reveal.getCurrentSlide()).find('.slide-hints-wrapper').length) {
      return
    }
    Reveal.configure({keyboard: true})
    $('.reveal .story-controls').show();
    blockTypes.map(blockType => {
      $(Reveal.getCurrentSlide()).find(`div.sl-block[data-block-type=${blockType}]`).css('zIndex', 'auto')
    })
  }

  function calcHiddenTextPercent(detailTexts) {
    let totalCounter = 0;
    let hiddenCounter = 0;
    detailTexts.words.map(w => {
      if (w.type === 'word') {
        totalCounter++
        if (w.hidden) {
          hiddenCounter++
        }
      }
    })
    return `${totalCounter === 0 || hiddenCounter === 0 ? 0 : Math.round(hiddenCounter * 100 / totalCounter)}%`
  }

  function processOutputAsJson(output) {
    let json = null
    try {
      json = JSON.parse(output.replace(/```json\n?|```/g, ''))
    } catch (ex) {

    }
    return json
  }

  function mapImageClickHandler(image, texts) {
    const detailImgWrap = document.createElement('div')
    const detailImg = document.createElement('img')
    detailImg.src = image.url
    detailImgWrap.appendChild(detailImg)

    const detailText = document.createElement('div')
    detailText.classList.add('detail-text')

    const text = texts.find(t => t.id === image.id)

    text.words.map(word => {
      const {type} = word
      if (type === 'break') {
        const breakElem = document.createElement('div')
        breakElem.classList.add('line-sep')
        detailText.appendChild(breakElem)
      } else {
        const currentSpan = document.createElement('span')
        currentSpan.classList.add('text-item-word')
        currentSpan.innerHTML = word.word
        if (word.hidden) {
          currentSpan.classList.add('selected')
        }
        if (word?.target) {
          currentSpan.classList.add('word-target')
        }
        currentSpan.addEventListener('click', () => {

          if (voiceResponse.getStatus()) {
            voiceResponse.stop()
            startRecording(recordingWrap.querySelector('#start-recording'))
          }
          ['#result_span', '#final_span', '#interim_span'].map(q => {
            detailTextWrap.querySelector(q).innerHTML = ''
            recordingWrap.querySelector('#start-retelling-wrap').style.display = 'none'
          })

          word.hidden = !word.hidden
          currentSpan.classList.toggle('selected')
          recordingWrap.querySelector('#hidden-text-percent').innerText = calcHiddenTextPercent(text)
        })
        detailText.appendChild(currentSpan)
      }
    })

    const detailTextWrap = document.createElement('div')
    detailTextWrap.classList.add('detail-text-wrap')
    detailTextWrap.appendChild(detailText)

    const recordingContainer = document.createElement('div')
    recordingContainer.classList.add('recording-container')
    recordingContainer.innerHTML = `
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
    recordingWrap.innerHTML = `
<div class="mental-map-text-status">
<div style="margin-bottom: 10px">Текст скрыт на: <strong id="hidden-text-percent"></strong></div>
<div>Сходство: <strong id="similarity-percent"></strong></div>
</div>
<div class="question-voice" style="bottom: 0; display: flex; position: relative; left: auto; top: auto">
    <div class="question-voice__inner">
        <div id="start-recording" class="gn">
            <div class="mc" style="pointer-events: none"></div>
        </div>
    </div>
</div>
<div class="retelling-container" id="start-retelling-wrap" style="display: none; text-align: center">
    <button class="btn" type="button" id="start-retelling">Проверить</button>
    <label style="display: block; font-weight: normal; font-size: 2.2rem; margin-top: 10px" for="clear-text"><input style="transform: scale(1.5); margin-right: 10px" type="checkbox" id="clear-text" checked> без знаков</label>
</div>`
    detailContainerInner.appendChild(recordingWrap)

    const detailContainer = document.createElement('div')
    detailContainer.classList.add('mental-map-detail-container')
    detailContainer.appendChild(detailContainerInner)

    const dialog = new InnerDialog($(container), {title: 'Изображение', content: detailContainer});
    dialog.show(wrapper => {

      showDialogHandler()

      wrapper.querySelector('#start-recording').addEventListener('click', e => {
        startRecording(e.target)
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

        startRetelling(clearText ? removePunctuation(userResponse) : userResponse, clearText ? removePunctuation(text.text) : text.text).then(response => {
          const json = processOutputAsJson(wrapper.querySelector('#retelling-response').innerText)
          if (json) {
            const val = Number(json?.overall_similarity)
            recordingWrap.querySelector('#similarity-percent').innerText = `${val}%`
          }
        })
      })

      recordingWrap.querySelector('#hidden-text-percent').innerText = calcHiddenTextPercent(text)

      wrapper.querySelector('#result_span').addEventListener('input', e => {
        const text = e.target.innerText
        const display = text.length > 0 ? 'block' : 'none'
        if (display !== wrapper.querySelector('#start-retelling-wrap').style.display) {
          wrapper.querySelector('#start-retelling-wrap').style.display = display
        }
      })
    })

    dialog.onHide(() => {
      hideDialogHandler()
      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
      }
    })
  }

  function processImageText(text) {
    const textFragments = new Map();
    const reg = new RegExp(`<span[^>]*>(.*?)<\\/span>`, 'gm');
    const imageText = text.replace(reg, (match, p1) => {
      const id = uuidv4()
      textFragments.set(`${id}`, `${p1.trim()}`);
      return `{${id}}`;
    })
    return {
      imageText,
      textFragments
    }
  }

  const run = async () => {
    const json = await params.init()
    texts = json.map.images.map(image => {
      const {imageText, textFragments} = processImageText(image.text)
      const paragraphs = imageText.split('\n')
      const words = paragraphs.map(p => {
        if (p === '') {
          return [{type: 'break'}]
        }
        const words = p.split(' ').map(word => {
          if (word[0] === '{') {
            const id = word.toString().replace(/[^\w\-]+/gmui, '')
            if (textFragments.has(id)) {
              const reg = new RegExp(`{${id}}`)
              word = word.replace(reg, textFragments.get(id))
              return word.split(' ').map(w => ({word: w, type: 'word', hidden: false, target: true}))
            }
          }
          return [{word, type: 'word', hidden: false}]
        })
        return [...(words.flat()), {type: 'break'}]
      }).flat()
      return {
        id: image.id,
        text: image.text,
        words
      }
    })

    const zoomWrap = document.createElement('div')
    zoomWrap.classList.add('zoom-wrap')

    const img = document.createElement('img')
    img.src = json.map.url
    img.style.height = '100%'
    //img.style.width = '100%'
    //img.style.margin = '0 auto'
    zoomWrap.appendChild(img)

    json.map.images.map(image => {
      const mapImgWrap = document.createElement('div')
      mapImgWrap.classList.add('mental-map-img')
      mapImgWrap.style.position = 'absolute'
      mapImgWrap.style.width = image.width + 'px'
      mapImgWrap.style.height = image.height + 'px'
      mapImgWrap.style.left = '0px'
      mapImgWrap.style.top = '0px'
      mapImgWrap.style.transform = `translate(${image.left}px, ${image.top}px)`
      mapImgWrap.addEventListener('click', () => {
        mapImageClickHandler(image, texts)
      })
      const mapImg = document.createElement('img')
      mapImg.setAttribute('title', image.text.replace(/<[^>]*>?/gm, ''))
      mapImg.dataset.trigger = 'hover'
      mapImg.dataset.placement = 'auto'
      mapImg.dataset.container = 'body'
      mapImg.src = image.url
      mapImgWrap.appendChild(mapImg)
      zoomWrap.appendChild(mapImgWrap)
    })

    container.appendChild(zoomWrap)
    this.element.appendChild(container)

    $('.mental-map-img img').tooltip()

    const btn = document.createElement('button')
    btn.classList.add('btn', 'btn-small', 'mental-map-all-text-btn')
    btn.textContent = 'Весь текст'
    btn.addEventListener('click', () => {

      const list = document.createElement('div')
      list.classList.add('mental-map-all-text-container')
      texts.map(textState => {
        const item = document.createElement('div')
        item.classList.add('text-container-row')
        const imageItem = document.createElement('div')
        imageItem.classList.add('image-item')

        const img = document.createElement('img')
        const image = json.map.images.find(i => i.id === textState.id)
        img.src = image.url
        img.style.cursor = 'pointer'
        img.addEventListener('click', e => {
          mapImageClickHandler(image, texts)
        })
        imageItem.appendChild(img)

        item.appendChild(imageItem)
        const textItem = document.createElement('div')
        textItem.classList.add('text-item')

        textState.words.map(word => {
          const {type} = word
          if (type === 'break') {
            const breakElem = document.createElement('div')
            breakElem.classList.add('line-sep')
            textItem.appendChild(breakElem)
          } else {
            const span = document.createElement('span')
            span.classList.add('text-item-word')
            if (word.hidden) {
              span.classList.add('selected')
            }
            span.textContent = word.word
            span.addEventListener('click', () => {
              word.hidden = !word.hidden
              span.classList.toggle('selected')
            })
            textItem.appendChild(span)
          }
        })

        item.appendChild(textItem)
        list.appendChild(item)
      })

      const dialog = new InnerDialog($(container), {title: 'Весь текст', content: list});

      dialog.show(() => {
        showDialogHandler()
      })

      dialog.onHide(() => {
        hideDialogHandler()
      })
    })

    this.element.appendChild(btn)

    const hideBtn = document.createElement('button')
    hideBtn.classList.add('btn', 'btn-small', 'mental-map-hide-btn')
    hideBtn.textContent = 'Скрыть'
    hideBtn.addEventListener('click', e => {
      $(e.target).toggleClass('img-hide')
      if ($(e.target).hasClass('img-hide')) {
        $(this.element).find('.mental-map-img img')
          .each((i, el) => $(el).css({opacity: '0'}))
        $(this.element).find('.mental-map-img').each((i, el) => {
          $(el).append(`<span class="mental-map-point"></span>`)
        })
        $(e.target).text('Показать')
      } else {
        $(this.element).find('.mental-map-img span').remove()
        $(this.element).find('.mental-map-img img')
          .each((i, el) => $(el).css({opacity: '1'}))
        $(e.target).text('Скрыть')
      }
    })
    this.element.appendChild(hideBtn)

    let initialZoom = 0.8
    const containerWidth = container.innerWidth
    const containerHeight = container.innerHeight

    if (json.map.height > containerHeight) {
      initialZoom = containerHeight / json.map.height;
    } else {
      initialZoom = 1;
    }

    if (json.map.width > containerWidth) {
      //initialZoom = containerWidth / imageWidth;
    }

    if (json.map.width < containerWidth) {
      initialZoom = 1 + ((containerWidth - json.map.width) / json.map.width);
    }

    const zoom = Panzoom(zoomWrap, {
      excludeClass: 'mental-map-img',
      bounds: true,
      startScale: initialZoom,
      //initialX: 0,
      //initialY: 0,
      //startX: 0,
      //startY: 0,
      //origin: '0px 0px'
    });
    element.parentElement.addEventListener('wheel', zoom.zoomWithWheel);
  }

  /**
   * @param {HTMLElement} element
   */
  function startRecording(element) {
    const state = element.dataset.state
    if (!state) {
      $(document.getElementById("start-retelling-wrap")).hide()
      setTimeout(function () {
        voiceResponse.start(new Event('voiceResponseStart'), 'ru-RU', function () {
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
      })
    }
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
    return await sendMessage({
      userResponse,
      slideTexts: targetText
    }, onMessage, onError, onEnd)
  }

  async function sendMessage(payload, onMessage, onError, onEnd) {
    let accumulatedMessage = ""

    return sendEventSourceMessage({
      url: `/admin/index.php?r=gpt/stream/retelling`,
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
    run
  }
}
