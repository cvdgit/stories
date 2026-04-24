import {SimilarityChecker} from "../lib/calcSimilarity";
import sendMessage from "../lib/sendMessage";

/**
 * @param {VoiceResponse} voiceResponse
 * @param {HTMLElement} element
 * @param {string} lang
 * @param text
 * @param {boolean} makeRewrite
 * @param {int} threshold
 * @param {(() => void) | null} stopHandler
 */
export default function startRecording(voiceResponse, element, lang, text, makeRewrite, threshold, stopHandler = null) {
  const state = element.dataset.state
  if (!state) {
    $(document.getElementById("start-retelling-wrap")).hide()
    setTimeout(function () {
      voiceResponse.start(new Event('voiceResponseStart'), lang, function () {
        element.dataset.state = 'recording'
        element.classList.add('recording')

        const ring = document.createElement('div')
        ring.classList.add('pulse-ring')
        element.parentNode.insertBefore(ring, element)
      });
    }, 500);
  } else {
    voiceResponse.stop(function (args) {

      element.parentNode.querySelector('.pulse-ring')?.remove();
      element.classList.remove('recording')
      delete element.dataset.state

      const $resultSpan = $(document.getElementById("result_span"))
      const $finalSpan = $(document.getElementById("final_span"))

      if ($finalSpan.text().trim().length) {
        $resultSpan.text(
          $resultSpan.text().trim().length
            ? $resultSpan.text().trim() + "\n" + $finalSpan.text().trim()
            : $finalSpan.text().trim()
        )
        $finalSpan.empty()
      }

      if ($resultSpan.text().trim().length) {
        $(document.getElementById("start-retelling-wrap")).show()
      }

      const userResponse = $resultSpan.text().trim()
      if (userResponse.length && makeRewrite) {

        const similarityChecker = new SimilarityChecker(threshold)
        if (similarityChecker.check(text, userResponse)) {
          if (typeof stopHandler === 'function') {
            stopHandler()
          }
          return
        }

        const content = createRewriteContent(() => {})
        $(element).parents('.mental-map-detail-container').append(content)
        sendMessage(
          `/admin/index.php?r=gpt/stream/retelling-rewrite`,
          {
            userResponse,
            slideTexts: text
          },
          (message) => {
            $resultSpan.text(message)
          },
          () => {
            content.remove()
          },
          () => {
            content.remove()
            if (typeof stopHandler === 'function') {
              stopHandler()
            }
          }
        )
      }
    })
  }
}

function createRewriteContent(hideCallback) {
  const wrap = document.createElement('div')
  wrap.classList.add('retelling-wrap')
  wrap.style.backgroundColor = 'transparent'
  wrap.style.padding = '0'
  wrap.innerHTML = `
      <div style="display: flex; align-items: center; justify-content: center; height: 100%; background-color: rgba(255, 255, 255, 0.4); backdrop-filter: blur(4px);">
        <img alt="..." src="/img/loading.gif" style="width: 100px" />
      </div>
    `
  return wrap
}
