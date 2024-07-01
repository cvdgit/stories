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

  function mapImageClickHandler(image, texts) {

    const detailImgWrap = document.createElement('div')
    const detailImg = document.createElement('img')
    detailImg.src = image.url
    detailImgWrap.appendChild(detailImg)

    const detailText = document.createElement('div')
    detailText.classList.add('detail-text')

    const text = texts.find(t => t.id === image.id)

    text.words.map(word => {
      const currentSpan = document.createElement('span')
      currentSpan.classList.add('text-item-word')
      currentSpan.textContent = word.word
      if (word.hidden) {
        currentSpan.classList.add('selected')
      }
      detailText.appendChild(currentSpan)
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
    recordingWrap.innerHTML = `<div class="question-voice" style="bottom: 0">
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

        const content = createRetellingContent()
        wrapper.querySelector('.mental-map-detail-container').innerHTML = ''
        wrapper.querySelector('.mental-map-detail-container').appendChild(content)

        startRetelling(userResponse, text.text).then(response => {
          console.log(response)
        })
      })
    })

    dialog.onHide(() => {
      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
      }
    })
  }

  const run = async () => {
    const json = await params.init()

    texts = json.map.images.map(image => {
      return {
        id: image.id,
        text: image.text,
        words: image.text.split(' ').map(word => ({word, hidden: false}))
      }
    })

    const img = document.createElement('img')
    img.src = json.map.url
    img.style.height = '100%'
    container.appendChild(img)

    json.map.images.map(image => {
      const mapImg = document.createElement('img')
      mapImg.classList.add('mental-map-img')
      mapImg.src = image.url
      mapImg.style.position = 'absolute'
      mapImg.style.width = image.width + 'px'
      mapImg.style.height = image.height + 'px'
      mapImg.style.left = '0px'
      mapImg.style.top = '0px'
      mapImg.style.transform = `translate(${image.left}px, ${image.top}px)`
      mapImg.addEventListener('click', () => {
        mapImageClickHandler(image, texts)
      })
      container.appendChild(mapImg)
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
          const span = document.createElement('span')
          span.classList.add('text-item-word')
          if (word.hidden) {
            span.classList.add('selected')
          }
          span.textContent = word.word
          span.addEventListener('click', () => {
            word.hidden = !word.hidden
            span.classList.toggle('selected')
            console.log(texts)
          })
          textItem.appendChild(span)
        })

        item.appendChild(textItem)
        list.appendChild(item)
      })

      const dialog = new InnerDialog($(container), {title: 'Весь текст', content: list});

      dialog.show()
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

    /*
    const targetElement = event.target

    if (targetElement.classList.contains('recording-start')) {
      return
    }

    targetElement.classList.add('recording-start')

    const finalSpan = document.createElement('span')
    finalSpan.classList.add('recording-final')
    finalSpan.setAttribute('contenteditable', 'plaintext-only')
    targetElement.appendChild(finalSpan)
    const resultSpan = document.createElement('span')
    resultSpan.classList.add('recording-result')
    resultSpan.setAttribute('contenteditable', 'plaintext-only')
    targetElement.appendChild(resultSpan)
    const interimSpan = document.createElement('span')
    interimSpan.classList.add('recording-interim')
    targetElement.appendChild(interimSpan)

    setTimeout(function () {
      voiceResponse.start(event, 'ru-RU', function () {
        statusWrap.querySelector('.recording-status').innerHTML = `
        <div><button type="button" class="recording-stop">Остановить запись</button></div>
        `
        statusWrap.querySelector('.recording-stop').addEventListener('click', e => {
          voiceResponse.stop(() => {
            statusWrap.querySelector('.recording-status').innerHTML = `
        <div>Нажмите на фрагмент для начала записи</div>
        <div><button class="start-retelling" type="button">Проверить</button></div>
        `
            statusWrap.querySelector('.start-retelling').addEventListener('click', e => {
              if (voiceResponse.getStatus()) {
                voiceResponse.stop()
              }
              const wrap = startRetelling()
              detailContainer.innerHTML = ''
              detailContainer.appendChild(wrap)
            })
          })
        })
      });
    }, 500);
     */
  }

  function createRetellingContent() {
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

    //$wrap.find("#voice-finish").on("click", hideCallback)

    return wrap
  }

  async function startRetelling(userResponse, targetText) {

    /*
    ["voice-control"].map(id => $elem.find(`#${id}`).hide())
    */
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
    }
    const onEnd = () => {
      console.log('end')
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
