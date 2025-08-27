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

    if (elem.querySelector('.gn').classList.contains('disabled')) {
      return
    }

    if ($(e.target).data('abort')) {
      elem.querySelector('.gn').classList.remove('recording')
      elem.querySelector('.pulse-ring').remove()
      $(e.target).removeData('abort')
      voiceResponse.stop(() => {
        delete elem.dataset.state
        stopClickHandler(e.target, true)
      })
      return
    }

    const state = elem.dataset.state;
    if (!state) {
      if (startClickHandler(e.target) === false) {
        return;
      }

      voiceResponse.start(new Event('voiceResponseStart'), 'ru-RU', function () {
        elem.querySelector('.gn').classList.add('disabled')
        setTimeout(() => {
          elem.dataset.state = 'recording';
          elem.querySelector('.gn').classList.remove('disabled')
          const ring = document.createElement('div');
          ring.classList.add('pulse-ring');
          elem.querySelector('.question-voice__inner').insertBefore(ring, elem.querySelector('.gn'));
          elem.querySelector('.gn').classList.add('recording');
        }, 500)
      });
      return;
    }

    elem.querySelector('.gn').classList.remove('recording')
    elem.querySelector('.pulse-ring').remove()
    elem.querySelector('.gn').classList.add('disabled')

    setTimeout(() => {
      voiceResponse.stop(() => {
        elem.querySelector('.gn').classList.remove('disabled')
        delete elem.dataset.state
        stopClickHandler(e.target)
      })
    }, 500)
  })

  return elem
}
