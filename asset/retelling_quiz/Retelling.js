import './Retelling.css'
import CreateRetelling from "./CreateRetelling";
import CreateRetellingAnswers from "./CreateRetellingAnswers";
import RetellingVoiceControl from "./RetellingVoiceControl";
import VoiceResponse from "../mental_map_quiz/lib/VoiceResponse";
import MissingWordsRecognition from "../mental_map_quiz/lib/MissingWordsRecognition";
import CreateRetellingResponseDialog from "./CreateRetellingResponseDialog";

export default function Retelling(element, deck, params) {

  this.element = element
  params = params || {}
  params.slide_id = deck ? Number($(deck.getCurrentSlide()).attr('data-id')) : null

  const container = document.createElement('div')
  container.classList.add('retelling-block')

  const run = async () => {
    let responseJson
    try {
      responseJson = await params.init()
    } catch (ex) {
      container.innerText = ex.message
      this.element.appendChild(container)
      return
    }

    const {withQuestions, text: slideTexts, questions} = responseJson
    params.completed = Boolean(responseJson?.completed)
    params.all = Number(responseJson?.all)

    const header = document.createElement('div')
    header.classList.add('retelling-dialog-header')
    header.innerHTML = 'Пересказ'

    this.element.appendChild(header)

    const body = document.createElement('div')
    body.classList.add('retelling-dialog-body')

    const voiceResponse = new VoiceResponse(new MissingWordsRecognition({}))
    const voiceControl = new RetellingVoiceControl(
      voiceResponse,
      (targetElement) => {},
      (targetElement) => {

        const resultSpan = content.querySelector('#retelling_result_span')
        const finalSpan = content.querySelector('#retelling_final_span')

        const finalText = finalSpan.innerText.trim()
        const resultText = resultSpan.innerText.trim()

        if (finalText.length) {
          resultSpan.innerText = resultText.length
            ? resultText + "\n" + finalText
            : finalText
          finalSpan.innerText = ''
        }

        if (resultText.length) {
          content.querySelector('#retelling_start-retelling').style.display = 'block'
        }
      }
    )

    const content = withQuestions
      ? CreateRetellingAnswers(voiceControl, questions)
      : CreateRetelling(voiceControl)

    voiceResponse.onResult((args) => {
      const finalSpan = content.querySelector('#retelling_final_span')
      if (finalSpan) {
        finalSpan.innerHTML = args.args?.result
      }
      const interimSpan = content.querySelector('#retelling_interim_span')
      if (interimSpan) {
        interimSpan.innerHTML = args.args?.interim
      }
    })

    content.querySelector('#retelling_result_span').addEventListener('input', e => {
      const text = e.target.innerText.trim()
      const display = text.length > 0 ? 'block' : 'none'
      const btn = content.querySelector('#retelling_start-retelling')
      if (display !== btn.style.display) {
        btn.style.display = display
      }
    })

    content.querySelector('#retelling_final_span').addEventListener('input', e => {
      const display = content.querySelector('#retelling_result_span').innerText.trim().length > 0 ? 'block' : 'none'
      const btn = content.querySelector('#retelling_start-retelling')
      if (display !== btn.style.display) {
        btn.style.display = display
      }
    })

    content.querySelector('#retelling_start-retelling').addEventListener('click', e => {
      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
      }

      const userResponse = content.querySelector("#retelling_result_span").innerText.trim()
      if (!userResponse) {
        alert('Ответ пользователя пуст')
        return
      }

      const responseDialog = CreateRetellingResponseDialog(
        userResponse,
        slideTexts,
        (response) => {
          const json = processOutputAsJson(response)
          if (json) {
            saveUserResult({
              overall_similarity: Number(json?.overall_similarity),
              content: slideTexts,
              story_id: params.story_id,
              slide_id: params.slide_id,
            }).then(response => {
              params.completed = Boolean(response?.completed)
              params.all = Number(response?.all)
              if (params.completed) {
                container.appendChild(createFinishContent(`${params.all}%`, slideTexts))
              }
            })
          }
        },
        () => {
          if (params.completed) {
            container.appendChild(createFinishContent(`${params.all}%`, slideTexts))
          }
        }
      )
      content.appendChild(responseDialog)
    })

    body.appendChild(content)

    container.appendChild(header)
    container.appendChild(body)

    if (params.completed) {
      container.appendChild(createFinishContent(`${params.all}%`, slideTexts))
    }

    this.element.appendChild(container)
  }

  function processOutputAsJson(output) {
    let json = null
    try {
      json = JSON.parse(output.replace(/```json\n?|```/g, ''))
    } catch (ex) {

    }
    return json
  }

  async function saveUserResult(payload) {
    const response = await fetch(`/retelling/save`, {
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

  function createFinishContent(all, texts) {
    const elem = document.createElement('div')
    elem.classList.add('retelling-wrap')
    elem.style.backgroundColor = 'transparent'
    elem.style.padding = '0'
    elem.innerHTML = `
      <div class="mental-map-done-wrap" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background-color: rgba(255, 255, 255, 0.4); backdrop-filter: blur(4px);">
        <h2 style="margin-bottom: 20px">Пересказ пройден</h2>
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

    const el = document.createElement('div')
    el.style.marginBottom = '10px'
    el.style.display = 'flex'
    el.style.flexDirection = 'row'
    el.style.columnGap = '20px'
    el.innerHTML = all
    // el.appendChild(FragmentResultElement(h))

    const textEl = document.createElement('div')
    textEl.classList.add('text-item')
    textEl.style.flex = '1 1 auto'
    textEl.style.textAlign = 'left'
    textEl.innerText = texts
    el.appendChild(textEl)

    historyWrap.appendChild(el)

    elem.querySelector('.mental-map-done-wrap').appendChild(historyWrap)

    return elem
  }

  return {
    run,
    canNext() {
      return params.completed
    }
  }
}
