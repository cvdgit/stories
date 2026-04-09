/**
 * @param {VoiceResponse} voiceResponse
 * @param startClickHandler
 * @param stopClickHandler
 * @constructor
 */
export default function PresentationVoiceControl(
  voiceResponse,
  startClickHandler,
  stopClickHandler
) {

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

  const start = () => {
    elem.querySelector('.gn').classList.add('disabled')
    voiceResponse.start(new Event('voiceResponseStart'), null, function () {
      elem.dataset.state = 'recording';
      elem.querySelector('.gn').classList.remove('disabled');
      const ring = document.createElement('div');
      ring.classList.add('pulse-ring');
      elem.querySelector('.question-voice__inner').insertBefore(ring, elem.querySelector('.gn'));
      elem.querySelector('.gn').classList.add('recording');
      startClickHandler();
    });
  };

  /**
   * @param {boolean} abort
   */
  const stop = (abort = false) => {
    elem.querySelector('.gn').classList.remove('recording');
    elem.querySelector('.pulse-ring').remove();
    elem.querySelector('.gn').classList.add('disabled');
    voiceResponse.stop(() => {
      elem.querySelector('.gn').classList.remove('disabled');
      delete elem.dataset.state;
      stopClickHandler(elem.querySelector('.gn'));
    });
  }

  elem.querySelector('.gn').addEventListener('click', e => {

    if (elem.querySelector('.gn').classList.contains('disabled')) {
      return
    }

    if ($(e.target).data('abort')) {
      stop(true);
      return
    }

    const state = elem.dataset.state;
    if (!state) {
      start();
      return;
    }

    stop();
  })

  return {
    render() {
      return elem;
    },
    start,
    stop
  };
}
