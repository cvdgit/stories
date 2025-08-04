/**
 * @param {VoiceResponse} voiceResponse
 * @param startClickHandler
 * @param stopClickHandler
 * @returns {HTMLDivElement}
 * @constructor
 */
export default function TreeVoiceControl(
  voiceResponse,
  startClickHandler,
  stopClickHandler
) {

  console.log('TreeVoiceControl call')

  const elem = document.createElement('div')
  elem.classList.add('question-voice')
  elem.style.bottom = '0'
  elem.style.display = 'flex'
  elem.style.position = 'relative'
  elem.style.width = '60px'
  elem.style.height = '60px'
  elem.innerHTML = `<div class="question-voice__inner">
            <div data-toggle="tooltip" title="Нажмите, что бы начать запись с микрофона" class="gn" style="width: 100%; height: 100%; margin: 0 auto">
                <div class="mc" style="pointer-events: none"></div>
            </div>
        </div>`

  elem.querySelector('.gn').addEventListener('click', e => {

    if ($(e.target).data('abort')) {
      elem.querySelector('.gn').classList.remove('recording')
      elem.querySelector('.pulse-ring').remove()
      delete elem.dataset.state
      stopClickHandler(e.target)
      return
    }

    const state = elem.dataset.state;
    if (!state) {
      if (startClickHandler(e.target) === false) {
        return;
      }
      voiceResponse.start(new Event('voiceResponseStart'), 'ru-RU', function () {
        elem.dataset.state = 'recording';
        const ring = document.createElement('div');
        ring.classList.add('pulse-ring');
        elem.querySelector('.question-voice__inner').insertBefore(ring, elem.querySelector('.gn'));
        elem.querySelector('.gn').classList.add('recording');
      });
      return;
    }

    elem.querySelector('.gn').classList.remove('recording')
    elem.querySelector('.pulse-ring').remove()
    voiceResponse.stop(() => {
      delete elem.dataset.state
      stopClickHandler(e.target)
    })
  })

  return elem
}
