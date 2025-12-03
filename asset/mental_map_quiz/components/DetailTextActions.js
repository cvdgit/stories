export default function DetailTextActions(clickHandler, promptBtn, hideTextBlock) {

  const detailTextActions = document.createElement('div')
  detailTextActions.classList.add('detail-text-actions')
  detailTextActions.innerHTML = `<div class="left-buttons"></div><div class="right-buttons"></div>`;

  if (promptBtn) {
    detailTextActions
      .querySelector('.right-buttons')
      .appendChild(promptBtn);
  }

  if (typeof clickHandler === 'function') {
    const randBtn = document.createElement('button')
    randBtn.setAttribute('type', 'button')
    randBtn.textContent = 'Закрыть текст'
    randBtn.addEventListener('click', clickHandler)
    detailTextActions
      .querySelector('.right-buttons')
      .appendChild(randBtn);
  }

  if (hideTextBlock) {
    detailTextActions
      .querySelector('.left-buttons')
      .appendChild(hideTextBlock);
  }

  return detailTextActions
}
