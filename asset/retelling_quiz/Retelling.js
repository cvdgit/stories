import './Retelling.css'
import CreateRetelling from "./CreateRetelling";
import RetellingVoiceControl from "./RetellingVoiceControl";
import VoiceResponse from "../mental_map_quiz/lib/VoiceResponse";
import MissingWordsRecognition from "../mental_map_quiz/lib/MissingWordsRecognition";
import RetellingResponse from "./RetellingResponse";
import MentalMapEvents from "../mental_map_quiz/MentalMapEvents";
import {createNotify} from "../mental_map_quiz/components/utils";

export default function Retelling(element, deck, params, microphoneChecker) {

  this.element = element
  params = params || {}
  params.slide_id = deck ? Number($(deck.getCurrentSlide()).attr('data-id')) : null

  const strictModeTracker = window.strictModeTracker

  const container = document.createElement('div')
  container.classList.add('retelling-block')

  const loader = document.createElement('div')
  loader.classList.add('retelling-block-inner')
  loader.innerHTML = `<div><img width="30" src="/img/loading.gif" alt="loading..."> загрузка...</div>`
  this.element.appendChild(loader)

  function createNoMicrophoneElement(message) {
    const noMicroElem = document.createElement('div')
    noMicroElem.classList.add('microphone-error')
    noMicroElem.innerHTML = `<div style="padding: 20px; display: flex; flex-direction: column; row-gap: 10px; border-radius: 20px; background-color: RGBA(220, 53, 69, 1); color: white"><div>Микрофон недоступен:</div><div>${message}</div></div>`
    return noMicroElem
  }

  const createElementNotify = (text, options = {}) => {
    element.querySelectorAll('.mental-map-notify').forEach(el => el.remove())
    element.appendChild(
      createNotify(text, {persist: true, autoRemove: false, ...options})
    )
  }

  const run = async () => {
    let responseJson
    try {
      responseJson = await params.init()
      if (microphoneChecker) {
        microphoneChecker
          .check()
          .catch(error => this.element.appendChild(createNoMicrophoneElement(error.name + ': ' + error.message)));
      }
    } catch (ex) {
      loader.remove()
      container.innerText = ex.message
      this.element.appendChild(container)
      return
    }

    const {
      withQuestions,
      text: slideTexts,
      questions,
      settings,
      retellingSlideId,
      canChangeStrictMode = false
    } = responseJson

    params.completed = Boolean(responseJson?.completed)
    params.all = Number(responseJson?.all)

    if (canChangeStrictMode) {
      strictModeTracker.changeState(false)
    }

    strictModeTracker.on('screen', ({isFullscreenWindow}) => {
      if (isFullscreenWindow === false) {
        createElementNotify('Окно браузера должно быть развернуто на весь экран. Масштаб внутри вкладки должен быть 100%')
        return
      }
      element.querySelectorAll('.mental-map-notify').forEach(el => el.remove())
    })

    try {
      strictModeTracker.checkWindow()
    } catch (ex) {
      createElementNotify('Окно браузера должно быть развернуто на весь экран. Масштаб внутри вкладки должен быть 100%')
    }

    const events = MentalMapEvents(
      () => {
        try {
          strictModeTracker.checkWindow()
          return true
        } catch (ex) {
          createElementNotify('Окно браузера должно быть развернуто на весь экран. Масштаб внутри вкладки должен быть 100%')
        }
        return false
      },
      () => {
        strictModeTracker.startRecording(() => {
          destroy(content, voiceResponse, voiceControl)
        })
        const strictModeToggle = element.querySelector('.strict-mode-wrap input[type=checkbox]')
        if (strictModeToggle) {
          strictModeToggle.setAttribute('disabled', 'disabled')
        }
      },
      () => {
        strictModeTracker.stopRecording()
        const strictModeToggle = element.querySelector('.strict-mode-wrap input[type=checkbox]')
        if (strictModeToggle) {
          strictModeToggle.removeAttribute('disabled')
        }
      }
    )

    const body = document.createElement('div')
    body.classList.add('retelling-dialog-body')

    const {
      beforeStartRecordingEvent,
      startRecordingEvent,
      stopRecordingEvent
    } = events || {}

    const voiceResponse = new VoiceResponse(new MissingWordsRecognition({}));
    const voiceControl = new RetellingVoiceControl(
      voiceResponse,
      () => {
        if (beforeStartRecordingEvent() === false) {
          return false
        }
        startRecordingEvent()
        content.switchRecording();
        content.resetUserInput();
      },
      () => {
        stopRecordingEvent()
        const userResponse = content.processUserResponse();
        if (userResponse.length) {
          content.switchRetelling();
          startRetelling();
        }
      }
    );

    const retellingResponse = new RetellingResponse(() => {
      content.switchStartUp();
      content.resetUserInput();
    });

    const content = new CreateRetelling(
      voiceControl,
      retellingResponse,
      {withQuestions, questions, settings, canChangeStrictMode},
      (enable) => {

        try {
          strictModeTracker.changeState(enable)
        } catch(ex) {
          createElementNotify('Невозможно переключить во время проговаривания', {persist: false, autoRemove: true})
          return false
        }

        element.querySelectorAll('.mental-map-notify').forEach(el => el.remove())
        if (enable === true) {
          try {
            strictModeTracker.checkWindow()
          } catch (ex) {
            createElementNotify('Окно браузера должно быть развернуто на весь экран. Масштаб внутри вкладки должен быть 100%')
          }
        }
      },
    );

    const startRetelling = async () => {
      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
      }

      const userResponse = content.getUserResponse();
      if (!userResponse) {
        alert('Ответ пользователя пуст');
        return;
      }

      await retellingResponse.send(
        userResponse,
        slideTexts,
          json => {
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
              if (deck) {
                if (deck.hasPlugin('stat')) {
                  const statPlugin = deck.getPlugin('stat');
                  statPlugin.sendStat({slideId: params.slide_id});
                }
              }
            })
          },
        (error) => {
          createElementNotify(error, {persist: false, autoRemove: true})
          content.switchStartUp()
          content.resetUserInput()
        }
      )
    }

    body.appendChild(content.render());

    //container.appendChild(header)
    container.appendChild(body)

    function destroy(content, voiceResponse, voiceControl) {
      if (!voiceResponse.getStatus()) {
        return;
      }
      voiceControl.triggerClick();
      content.resetUserInput();
    }

    if (params.completed) {
      container.appendChild(createFinishContent(`${params.all}%`, slideTexts))
    } else {
      content.switchStartUp();
      //window.addEventListener('blur', () => destroy(content, voiceResponse, voiceControl));
    }

    loader.remove();
    this.element.appendChild(container);

    return () => destroy(content, voiceResponse, voiceControl);
  }

  async function saveUserResult(payload) {
    return await Api.post(`/retelling/save`, payload);
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
    destroyHandler: null,
    async run() {
      this.destroyHandler = await run();
    },
    canNext() {
      return !(params.retellingRequired && !params.completed);
    },
    destroy() {
      if (this.destroyHandler) {
        this.destroyHandler();
      }
    }
  }
}
