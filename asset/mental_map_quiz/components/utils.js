export function createNotify(text, options = {}) {

  const {
    persist = false,
    autoRemove = true
  } = options

  const div = document.createElement('div')
  div.classList.add('mental-map-notify')
  div.innerHTML = `
<div style="display: flex">
    <div class="mental-map-notify-text">${text}</div>
</div>
`
  if (persist === false) {
    const closeBtn = document.createElement('button')
    closeBtn.type = 'button'
    closeBtn.className = 'mental-map-notify-button'
    closeBtn.addEventListener('click', e => div.remove())
    div.appendChild(closeBtn)
  }

  if (autoRemove) {
    setTimeout(() => div.remove(), 5000)
  }

  return div
}
