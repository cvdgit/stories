export function createNotify(text) {
  const div = document.createElement('div')
  div.classList.add('mental-map-notify')
  div.innerHTML = `
<div style="display: flex">
    <div class="mental-map-notify-text">${text}</div>
    <button class="mental-map-notify-button" type="button"></button>
</div>
`
  div.querySelector('.mental-map-notify-button').addEventListener('click', e => div.remove())
  setTimeout(() => div.remove(), 5000)
  return div
}
