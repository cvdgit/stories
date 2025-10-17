/**
 *
 * @param voiceResponse
 * @param startClickHandler
 * @param stopClickHandler
 * @constructor
 * @returns {RetellingVoiceControl}
 */
export default function RetellingVoiceControl(
  voiceResponse,
  startClickHandler,
  stopClickHandler
) {

  const elem = document.createElement('div')
  elem.classList.add('retelling-voice-controls')
  elem.innerHTML = `<select id="retelling-voice-lang">
    <option value="ru-RU" selected>rus</option>
    <option value="en-US">eng</option>
</select>
<div data-toggle="tooltip" title="Нажмите, что бы начать запись с микрофона" class="question-voice" style="display: block; position:relative; bottom: 0; margin: 0">
    <div class="question-voice__inner">
        <div class="gn">
            <div class="mc"></div>
        </div>
    </div>
</div>
  `

  elem.querySelector('.gn').addEventListener('click', e => {

    if (voiceResponse.getStatus()) {
      voiceResponse.stop((args) => {
        elem.querySelector('.gn').classList.remove('recording')
        elem.querySelector('.pulse-ring').remove()
        stopClickHandler(e.target)
      })
      return
    }

    if (startClickHandler(e.target) === false) {
      return
    }

    const voiceLang = elem.querySelector('#retelling-voice-lang')
    setTimeout(() => {
      voiceResponse.start(new Event('voiceResponseStart'), voiceLang.value, function () {
        const ring = document.createElement('div')
        ring.classList.add('pulse-ring')
        elem.querySelector('.question-voice__inner').insertBefore(ring, elem.querySelector('.gn'))
        elem.querySelector('.gn').classList.add('recording')
      });
    }, 500);
  })

  this.getElement = () => elem
  this.triggerClick = () => $(elem.querySelector('.gn')).trigger('click')
}
