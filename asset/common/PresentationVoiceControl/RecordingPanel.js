import PresentationVoiceControl from "./PresentationVoiceControl";
import VoiceResponse from "../../mental_map_quiz/lib/VoiceResponse";
import MissingWordsRecognition from "../../mental_map_quiz/lib/MissingWordsRecognition";

/**
 * @param {(string) => Promise} processUserResponse
 * @param {() => void} onStartCallback
 * @constructor
 */
function RecordingPanel(processUserResponse, onStartCallback) {

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

  const voiceResponse = new VoiceResponse(new MissingWordsRecognition({}));
  voiceResponse.onResult(args => {
    if (finalSpan) {
      finalSpan.innerHTML = args.args?.result;
    }
    if (interimSpan) {
      interimSpan.innerHTML = args.args?.interim;
    }
  });

  function blurHandler() {
    voiceControl.stop(true);
  }

  const voiceControl = new PresentationVoiceControl(
    voiceResponse,
    () => {

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

      window.removeEventListener('blur', blurHandler);

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

      await processUserResponse(abort ? '' : userResponse);
    }
  );

  element
    .querySelector('.fragment-recording-recorder')
    .appendChild(
      voiceControl.render()
    );

  return {
    render() {
      return element;
    },
    startRecording() {
      voiceControl.start();
    },
    destroy() {
      element.remove();
    }
  }
}

export default RecordingPanel;
