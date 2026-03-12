/**
 * @param {RetellingVoiceControl} voiceControl
 * @param {{render(): HTMLDivElement, send(*, *, *): Promise<void>}} retellingResponse
 * @param questionParams
 * @constructor
 */
export default function CreateRetelling(voiceControl, retellingResponse, questionParams) {

  const {withQuestions, questions} = questionParams;

  const elem = document.createElement('div');
  elem.classList.add('retelling-content');

  elem.innerHTML = `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center">
    <div class="retelling-main-wrap">
        <div class="retelling-startup">
            <p style="line-height: 34px">Начните запись и перескажите текст с предыдущего слайда.</p>
            <p style="line-height: 34px">Что бы пройти задание, точность пересказа должна быть 90% и выше.</p>
        </div>
        <div class="retelling-user-response">
            <div class="retelling-user-response-inner">
                <div style="padding: 20px 40px; display: flex; flex-direction: column; align-items: start; justify-content: center; margin-bottom: 20px; background-color: #eee; font-size: 2.5rem; border-radius: 2rem">
                    <span contenteditable="plaintext-only" id="retelling_result_span"></span>
                    <span contenteditable="plaintext-only" id="retelling_final_span"></span>
                    <span id="retelling_interim_span"></span>
                </div>
            </div>
        </div>
        <div id="voice-area"></div>
        <div id="retelling-area"></div>
    </div>
</div>`;

  if (withQuestions) {
    elem.querySelector('.retelling-user-response').classList.add('retelling-two-cols')
    const questionsCol = document.createElement('div');
    questionsCol.classList.add('retelling-answers-col');
    questionsCol.innerHTML = `<h2 class="h3">Вопросы</h2><div class="retelling-answers">${questions.replace(/(\r\n|\n|\r)/g, "<br>")}</div>`;
    elem.querySelector('.retelling-user-response').prepend(questionsCol);
  }

  const finalSpan = elem.querySelector('#retelling_final_span');
  const interimSpan = elem.querySelector('#retelling_interim_span');
  voiceControl.voiceResponseOnResult(args => {
    finalSpan.innerHTML = args.args?.result;
    interimSpan.innerHTML = args.args?.interim;
  });

  elem.querySelector('#voice-area')
    .appendChild(voiceControl.getElement());

  elem.querySelector('#retelling-area')
    .appendChild(retellingResponse.render());

  const stateClassNames = ['startup', 'recording', 'retelling'];

  function switchClassTo(cn) {
    const classList = elem.classList;
    stateClassNames.map(cn => classList.remove(cn));
    classList.add(cn);
  }

  const resultSpan = elem.querySelector('#retelling_result_span');

  const disablePasteHandler = e => {
    e.preventDefault();
    return false;
  }
  finalSpan.addEventListener('paste', disablePasteHandler);
  interimSpan.addEventListener('paste', disablePasteHandler);
  resultSpan.addEventListener('paste', disablePasteHandler);

  return {
    render() {
      return elem;
    },
    switchStartUp() {
      switchClassTo('startup');
    },
    switchRecording() {
      switchClassTo('recording');
    },
    switchRetelling() {
      switchClassTo('retelling');
    },
    getUserResponse() {
      return resultSpan.innerText.trim();
    },
    processUserResponse() {
      const finalText = finalSpan.innerText.trim();
      const resultText = resultSpan.innerText.trim();
      if (finalText.length) {
        resultSpan.innerText = resultText.length
          ? resultText + "\n" + finalText
          : finalText;
        finalSpan.innerText = '';
      }
      return this.getUserResponse();
    },
    resetUserInput() {
      resultSpan.innerHTML = '';
      finalSpan.innerHTML = '';
      interimSpan.innerHTML = '';
      resultSpan.dispatchEvent(new Event('input', {bubbles: true}));
      finalSpan.dispatchEvent(new Event('input', {bubbles: true}));
    }
  };
}
