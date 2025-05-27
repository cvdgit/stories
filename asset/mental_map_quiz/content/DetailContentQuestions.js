import DetailTextQuestions from "../components/DetailTextQuestions";
import FragmentResultQuestionsElement from "./FragmentResultQuestionsElement";
import RewritePromptBtn, {createUpdatePromptContent, saveRewritePrompt} from "../components/RewritePromptBtn";

export default function DetailContentQuestions({image, questionItem, historyItem, rewritePrompt}) {
  const detailImgWrap = document.createElement('div')
  detailImgWrap.classList.add('image-item')

  if (image.url) {
    const detailImg = document.createElement('img')
    detailImg.src = image.url
    detailImg.style.marginBottom = '10px'
    detailImgWrap.appendChild(detailImg)
  } else {
    const div = document.createElement('div')
    div.style.marginBottom = '10px'
    div.style.padding = '20px'
    div.style.cursor = 'pointer'
    div.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
</svg>
`
    detailImgWrap.appendChild(div)
  }

  detailImgWrap.appendChild(FragmentResultQuestionsElement(historyItem))

  const detailTextWrap = document.createElement('div')
  detailTextWrap.classList.add('detail-text-wrap')

  let promptBtn;
  if (rewritePrompt) {
    promptBtn = RewritePromptBtn(() => {
      const content = createUpdatePromptContent(rewritePrompt, (currentPrompt, close) => {
        rewritePrompt = currentPrompt
        saveRewritePrompt(currentPrompt).then(r => close())
      })
      detailContainer.appendChild(content)
    })
  }

  const detailText = DetailTextQuestions(questionItem?.questions, promptBtn)

  detailTextWrap.appendChild(detailText)

  const recordingContainer = document.createElement('div')
  recordingContainer.classList.add('recording-container')
  recordingContainer.innerHTML = `
      <div style="font-size: 2.2rem; line-height: 2.6rem; margin-bottom: 10px; color: #808080">Ответ ученика:</div>
      <div style="background-color: #eee; font-size: 2.2rem; line-height: 3rem">
            <span contenteditable="plaintext-only" id="result_span"
                  class="recording-final" style="line-height: 3rem"></span>
            <span contenteditable="plaintext-only" id="final_span"
                  class="recording-result" style="line-height: 3rem"></span>
            <span id="interim_span" class="recording-interim"></span>
        </div>
    `

  detailTextWrap.appendChild(recordingContainer)

  const detailContent = document.createElement('div')
  detailContent.classList.add('mental-map-detail-content')
  detailContent.appendChild(detailImgWrap)
  detailContent.appendChild(detailTextWrap)

  const detailContainerInner = document.createElement('div')
  detailContainerInner.classList.add('mental-map-detail-container-inner')
  detailContainerInner.appendChild(detailContent)

  const recordingWrap = document.createElement('div')
  recordingWrap.classList.add('recording-status')
  recordingWrap.innerHTML = `<div class="mental-map-text-status">
    <div>Сходство: <strong id="similarity-percent"></strong></div>
</div>
<div style="display: flex; align-items: center;">
    <select class="form-control" id="voice-lang" style="margin-right: 20px; font-size: 24px; height: auto">
        <option value="ru-RU" selected>rus</option>
        <option value="en-US">eng</option>
    </select>
    <div class="question-voice" style="bottom: 0; display: flex; position: relative;">
        <div class="question-voice__inner">
            <div id="start-recording" class="gn">
                <div class="mc" style="pointer-events: none"></div>
            </div>
        </div>
    </div>
</div>
<div class="retelling-container" id="start-retelling-wrap" style="display: none; text-align: center">
    <button class="btn" type="button" id="start-retelling">Проверить</button>
    <label style="display: block; font-weight: normal; font-size: 2.2rem; margin-top: 10px" for="clear-text"><input
            style="transform: scale(1.5); margin-right: 10px" type="checkbox" id="clear-text" checked> без
        знаков</label>
</div>`
  detailContainerInner.appendChild(recordingWrap)

  const detailContainer = document.createElement('div')
  detailContainer.classList.add('mental-map-detail-container')
  detailContainer.appendChild(detailContainerInner)

  return detailContainer
}
