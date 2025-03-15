import RetellingVoiceControl from "./RetellingVoiceControl";

/**
 * @param {RetellingVoiceControl} VoiceControl
 * @returns {HTMLDivElement}
 * @constructor
 */
export default function CreateRetelling(VoiceControl) {
  const elem = document.createElement('div')
  elem.classList.add('retelling-content')
  elem.innerHTML = `
    <div style="max-height: 100%; overflow-y: auto; display: flex; flex-direction: column; flex: 1 1 auto;">
        <h3>Пересказ пользователя:</h3>
        <div style="padding: 20px 40px; margin-bottom: 20px; background-color: #eee; font-size: 2.5rem; border-radius: 2rem">
            <span contenteditable="plaintext-only" id="retelling_result_span"
                  style="outline: 0; background-color: #eee; line-height: 50px; color: black; margin-right: 3px; padding: 10px"></span>
            <span contenteditable="plaintext-only" id="retelling_final_span"
                  style="outline: 0; background-color: #eee; line-height: 50px; color: black; margin-right: 3px; padding: 10px"></span>
            <span id="retelling_interim_span" style="color: gray"></span>
        </div>
        <div style="display: flex; flex-direction: row; align-items: center; justify-content: center">
            <button style="display: none" id="retelling_start-retelling" class="btn" type="button">Проверить</button>
        </div>
    </div>
    <div id="voice-area" style="position: relative; padding: 20px; height: 150px"></div>
`

  elem.querySelector('#voice-area').appendChild(VoiceControl.getElement())

  return elem
}
