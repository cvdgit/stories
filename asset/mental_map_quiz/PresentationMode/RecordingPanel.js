import {createNotify} from "../components/utils";
import PresentationVoiceControl from "../../common/PresentationVoiceControl/PresentationVoiceControl";

/**
 * @param {VoiceResponse} voiceResponse
 * @param {function(userResponse: string): Promise} processUserResponse
 * @param onStartCallback
 * @param events
 * @return {{render: function(): HTMLElement, abort: function(): void}|null}
 * @constructor
 */
function RecordingPanel(voiceResponse, processUserResponse, onStartCallback, events) {

  const element = document.createElement('div');
  element.classList.add('fragment-recording-wrap');
  element.innerHTML = `
<div class="fragment-recording-status">Запуск...</div>
<div class="fragment-recording-recorder node-control"></div>
<div class="node-voice-response" style="display: none;">
    <div>
        <span class="final_span"></span>
        <span class="interim_span"></span>
    </div>
    <div class="result_span"></div>
    <div class="retelling-response"></div>
</div>
`;

  const finalSpan = element.querySelector('.final_span');
  const interimSpan = element.querySelector('.interim_span');
  const resultSpan = element.querySelector('.result_span');

  function blurHandler() {
    voiceControl.stop(true);
  }

  voiceResponse.onError(e => {
    document.querySelector('.mental-map').appendChild(
      createNotify(`${e.args.error} error`)
    )
    blurHandler()
  })

  const {
    beforeStartRecordingEvent,
    startRecordingEvent,
    stopRecordingEvent
  } = events || {}

  const voiceControl = new PresentationVoiceControl(
    voiceResponse,
    () => {

      startRecordingEvent()

      // window.addEventListener('blur', blurHandler);
      element.querySelector('.fragment-recording-status').innerHTML = 'Идет запись';

      finalSpan.innerHTML = '';
      interimSpan.innerHTML = '';
      resultSpan.innerHTML = '';

      voiceResponse.onResult(args => {
        finalSpan.innerHTML = args.args?.result;
        interimSpan.innerHTML = args.args?.interim;
      });

      onStartCallback();
    },
    async (el, abort) => {

      // window.removeEventListener('blur', blurHandler);
      stopRecordingEvent()

      if (finalSpan.innerHTML.trim().length) {
        resultSpan.innerHTML +=
          resultSpan.innerHTML.trim().length
            ? resultSpan.innerHTML.trim() + "\n" + finalSpan.innerHTML.trim()
            : finalSpan.innerHTML.trim();
        finalSpan.innerHTML = '';
      }

      let userResponse = resultSpan.innerHTML.trim();
      element.querySelector('.fragment-recording-status').innerHTML = 'Обработка ответа...';
      element.querySelector('.fragment-recording-recorder').style.display = 'none';

      processUserResponse(abort ? '' : userResponse);
    },
    () => beforeStartRecordingEvent()
  );

  if (beforeStartRecordingEvent() === false) {
    return null
  }

  element
    .querySelector('.fragment-recording-recorder')
    .appendChild(
      voiceControl.render()
    )

  voiceControl.start()

  return {
    render() {
      return element
    },
    abort() {
      voiceControl.stop(true)
    }
  }
}

export default RecordingPanel;
