import './MentalMap.css'
import InnerDialog from "./Dialog";
import VoiceResponse from "./lib/VoiceResponse"
import MissingWordsRecognition from "./lib/MissingWordsRecognition"

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
        currentSpan.addEventListener('click', () => {
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
      <div style="background-color: #eee; font-size: 2.5rem">
            <span contenteditable="plaintext-only" id="result_span"
                  class="recording-final"></span>
            <span contenteditable="plaintext-only" id="final_span"
                  class="recording-result"></span>
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
<div class="question-voice" style="bottom: 0">
    <div class="question-voice__inner">
        <div id="start-recording" class="gn">
            <div class="mc" style="pointer-events: none"></div>
        </div>
    </div>
</div>
<div class="retelling-container">
    <button class="btn" style="display: none" type="button" id="start-retelling">Проверить</button>
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

        const content = createRetellingContent(() => dialog.hide())
        wrapper.querySelector('.mental-map-detail-container').appendChild(content)

        startRetelling(userResponse, text.text).then(response => {
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
        if (display !== wrapper.querySelector('#start-retelling').style.display) {
          wrapper.querySelector('#start-retelling').style.display = display
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

  const run = async () => {
    const json = await params.init()

    texts = json.map.images.map(image => {
      const paragraphs = image.text.split('\n')
      const words = paragraphs.map(p => {
        if (p === '') {
          return [{type: 'break'}]
        }
        const words = p.split(' ').map(word => ({word, type: 'word', hidden: false}))
        return [...words, {type: 'break'}]
      }).flat()
      return {
        id: image.id,
        text: image.text,
        words
      }
    })

    const img = document.createElement('img')
    img.src = json.map.url
    img.style.height = '100%'
    img.style.margin = '0 auto'
    container.appendChild(img)

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
      mapImg.src = image.url
      mapImgWrap.appendChild(mapImg)
      container.appendChild(mapImgWrap)
    })

    this.element.appendChild(container)

    const btn = document.createElement('button')
    btn.classList.add('mental-map-all-text-btn')
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
  }

  /**
   * @param {HTMLElement} element
   */
  function startRecording(element) {
    const state = element.dataset.state
    if (!state) {
      $(document.getElementById("start-retelling")).hide()
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
          $(document.getElementById("start-retelling")).show()
        }
      })
    }
  }

  function createRetellingContent(hideCallback) {
    const wrap = document.createElement('div')
    wrap.classList.add('retelling-wrap')
    wrap.innerHTML = `
        <div contenteditable="plaintext-only" id="retelling-response"
             style="font-size: 2.2rem; text-align: left; line-height: 3.5rem; overflow-y: scroll; height: 100%; max-height: 100%;"></div>
        <div style="display: flex; margin-top: 10px; flex-direction: row; align-items: center; justify-content: center">
            <img id="voice-loader" height="50px" src="/img/loading.gif" alt="">
            <button style="display: none" id="voice-finish" type="button" class="btn">OK</button>
        </div>
    `
    wrap.querySelector('#voice-finish').addEventListener('click', () => {
      wrap.remove()
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
