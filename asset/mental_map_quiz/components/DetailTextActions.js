
export default function DetailTextActions(clickHandler, promptBtn) {
  const detailTextActions = document.createElement('div')
  detailTextActions.classList.add('detail-text-actions')
  const randBtn = document.createElement('button')
  randBtn.setAttribute('type', 'button')
  randBtn.textContent = 'Закрыть текст'
  randBtn.addEventListener('click', clickHandler)
  if (promptBtn) {
    detailTextActions.appendChild(promptBtn)
  }
  detailTextActions.appendChild(randBtn)
  return detailTextActions
}
