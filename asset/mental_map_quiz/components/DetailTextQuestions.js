import DetailTextActions from "./DetailTextActions";

export default function DetailTextQuestions(questionsText, promptBtn) {
  const detailText = document.createElement('div')
  detailText.classList.add('detail-text')

  detailText.appendChild(DetailTextActions(undefined, promptBtn))

  const textElem = document.createElement('ol')
  textElem.style.paddingLeft = '0'
  questionsText.split('\n').map(t => {
    const li = document.createElement('li')
    li.style.marginBottom = '10px'
    li.innerHTML = t
    textElem.appendChild(li)
  })

  detailText.appendChild(textElem)
  return detailText
}
