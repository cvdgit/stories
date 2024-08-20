
export default function DetailTextActions(clickHandler) {
  const detailTextActions = document.createElement('div')
  detailTextActions.classList.add('detail-text-actions')
  const randBtn = document.createElement('button')
  randBtn.setAttribute('type', 'button')
  randBtn.textContent = 'Закрыть текст'
  randBtn.addEventListener('click', clickHandler)
  detailTextActions.appendChild(randBtn)
  return detailTextActions
}
