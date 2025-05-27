import DetailTextActions from "./DetailTextActions";

export default function DetailTextQuestions(questionsText, promptBtn) {
  const detailText = document.createElement('div')
  detailText.classList.add('detail-text')

  detailText.appendChild(DetailTextActions(undefined, promptBtn))

  const textElem = document.createElement('p')
  textElem.innerText = questionsText

  detailText.appendChild(textElem)
  return detailText
}
