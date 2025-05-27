
export default function DetailTextActions(clickHandler, promptBtn) {
  const detailTextActions = document.createElement('div')
  detailTextActions.classList.add('detail-text-actions')

  if (promptBtn) {
    detailTextActions.appendChild(promptBtn)
  }

  if (typeof clickHandler === 'function') {
    const randBtn = document.createElement('button')
    randBtn.setAttribute('type', 'button')
    randBtn.textContent = 'Закрыть текст'
    randBtn.addEventListener('click', clickHandler)
    detailTextActions.appendChild(randBtn)
  }
  return detailTextActions
}
