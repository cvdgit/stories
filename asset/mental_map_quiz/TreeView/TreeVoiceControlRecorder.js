/**
 * @param {VoiceResponse} voiceResponse
 * @param startClickHandler
 * @param stopClickHandler
 * @param processChunksHandler
 * @returns {HTMLDivElement}
 * @constructor
 */
export default function TreeVoiceControl(
  voiceResponse,
  startClickHandler,
  stopClickHandler,
  processChunksHandler
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

  /** @type {MediaRecorder|null} */
  let mediaRecorder
  /** @type {MediaStream|null} */
  let mediaStream
  let chunks = []

  const resetChunks = () => chunks = []

  elem.querySelector('.gn').addEventListener('click', e => {

    if ($(e.target).data('abort')) {
      mediaStream.getTracks().forEach( (track) => track.stop())
      mediaRecorder.stop()
      resetChunks()
      elem.querySelector('.gn').classList.remove('recording')
      elem.querySelector('.pulse-ring').remove()
      stopClickHandler(e.target)
      return
    }

    if (mediaRecorder && mediaRecorder.state === 'recording') {
      mediaStream.getTracks().forEach( (track) => track.stop())
      mediaRecorder.stop()
      elem.querySelector('.gn').classList.remove('recording')
      elem.querySelector('.pulse-ring').remove()
      stopClickHandler(e.target)
      return
    }

    if (startClickHandler(e.target) === false) {
      return
    }

    navigator.mediaDevices.getUserMedia({audio: true})
      .then((stream) => {

        mediaStream = stream

        const ring = document.createElement('div')
        ring.classList.add('pulse-ring')
        elem.querySelector('.question-voice__inner').insertBefore(ring, elem.querySelector('.gn'))
        elem.querySelector('.gn').classList.add('recording')

        mediaRecorder = new MediaRecorder(stream, {mimeType: 'audio/webm'})
        mediaRecorder.start()

        mediaRecorder.ondataavailable = (e) => chunks.push(e.data)

        mediaRecorder.onstop = () => {
          if (typeof processChunksHandler === 'function') {
            const abort = $(e.target).data('abort')
            $(e.target).data('abort', false)
            processChunksHandler(e.target, chunks, resetChunks, abort)
          }
        }

      })
      .catch(error => console.log('error', error))
  })

  return elem
}
