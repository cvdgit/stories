import {SimilarityChecker} from "../lib/calcSimilarity";
import sendMessage from "../lib/sendMessage";

export default async function startRetelling(userResponse, targetText, threshold, promptId) {

  const onMessage = message => {
    const el = document.getElementById("retelling-response")
    $(el).show()
    el.innerText = message
    el.scrollTop = el.scrollHeight
  }

  const onError = message => {
    const el = document.getElementById("retelling-response")
    $(el).show()
    el.innerText = message
    $(document.getElementById('voice-loader')).hide()
    $(document.getElementById('voice-finish')).show()
  }
  const onEnd = () => {
    $(document.getElementById('voice-loader')).hide()
    $(document.getElementById('voice-finish')).show()
  }

  const similarityChecker = new SimilarityChecker(threshold)
  if (similarityChecker.check(targetText, userResponse)) {
    onMessage(`{"overall_similarity": ${similarityChecker.getSimilarityPercentage()}}`)
    onEnd()
    return new Promise((resolve, reject) => {
      resolve({})
    })
  }

  return sendMessage(`/admin/index.php?r=gpt/stream/retelling`, {
    userResponse,
    slideTexts: targetText,
    promptId
  }, onMessage, onError, onEnd)
}
