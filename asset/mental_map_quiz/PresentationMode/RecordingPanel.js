import PresentationVoiceControl from "./PresentationVoiceControl";

/**
 * @param {VoiceResponse} voiceResponse
 * @param {function(userResponse: string): Promise} processUserResponse
 * @return {HTMLDivElement}
 * @constructor
 */
function RecordingPanel(voiceResponse, processUserResponse, onStartCallback) {

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

  const voiceControl = new PresentationVoiceControl(
    voiceResponse,
    () => {
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
    async () => {

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

      processUserResponse(userResponse);
    }
  );

  element
    .querySelector('.fragment-recording-recorder')
    .appendChild(
      voiceControl.render()
    );

  voiceControl.start();

  return element;
}

export default RecordingPanel;
