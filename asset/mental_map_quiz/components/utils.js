export function createNotify(text, options = {}) {

  const {
    persist = false,
    autoRemove = true,
    backdrop = false,
    timeout = 5000
  } = options

  const div = document.createElement('div')
  div.classList.add('mental-map-notify')

  if (backdrop) {
    div.classList.add('backdrop')
  }

  div.innerHTML = `
<div class="mental-map-notify-card">
    <div class="mental-map-notify-text">${text}</div>
</div>
`
  if (persist === false) {
    const closeBtn = document.createElement('button')
    closeBtn.type = 'button'
    closeBtn.className = 'mental-map-notify-button'
    closeBtn.addEventListener('click', e => div.remove())
    div.querySelector('.mental-map-notify-card').appendChild(closeBtn)
  }

  if (autoRemove) {
    setTimeout(() => {
      div.style.opacity = '0'
      setTimeout(() => div.remove(), 500)
    }, timeout)
  }

  return div
}
