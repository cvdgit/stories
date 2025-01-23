/**
 * @param {VoiceResponse} voiceResponse
 * @param clickHandler
 * @returns {HTMLDivElement}
 * @constructor
 */
export default function TreeVoiceControl(voiceResponse, startClickHandler, stopClickHandler) {
  const elem = document.createElement('div')
  elem.classList.add('question-voice')
  elem.style.bottom = '0'
  elem.style.display = 'flex'
  elem.style.position = 'relative'
  elem.style.width = '60px'
  elem.style.height = '60px'
  elem.innerHTML = `<div class="question-voice__inner">
            <div class="gn" style="width: 100%; height: 100%; margin: 0 auto">
                <div class="mc" style="pointer-events: none"></div>
            </div>
        </div>`

  if (voiceResponse.getStatus()) {
    const ring = document.createElement('div')
    ring.classList.add('pulse-ring')
    elem.querySelector('.question-voice__inner').insertBefore(ring, elem.querySelector('.gn'))
    elem.querySelector('.gn').classList.add('recording')
  }

  elem.querySelector('.gn').addEventListener('click', e => {
    if (voiceResponse.getStatus()) {
      voiceResponse.stop((args) => {
        elem.querySelector('.gn').classList.remove('recording')
        elem.querySelector('.pulse-ring').remove()
        stopClickHandler(e.target)
      })
    } else {
      startClickHandler(e.target)
      setTimeout(() => {
        voiceResponse.start(new Event('voiceResponseStart'), 'ru-RU', function () {
          const ring = document.createElement('div')
          ring.classList.add('pulse-ring')
          elem.querySelector('.question-voice__inner').insertBefore(ring, elem.querySelector('.gn'))
          elem.querySelector('.gn').classList.add('recording')
        });
      }, 500);
    }
  })

  return elem
}
