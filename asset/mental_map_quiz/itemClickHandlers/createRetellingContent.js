export default function createRetellingContent(hideCallback) {
  const wrap = document.createElement('div')
  wrap.classList.add('retelling-wrap')
  wrap.innerHTML = `
        <div contenteditable="plaintext-only" id="retelling-response"
             style="font-size: 2.2rem; text-align: left; line-height: 3rem; overflow-y: scroll; height: 100%; max-height: 100%;"></div>
        <div style="display: flex; margin-top: 10px; flex-direction: row; align-items: center; justify-content: center">
            <img id="voice-loader" height="50px" src="/img/loading.gif" alt="">
            <button style="display: none" id="voice-finish" type="button" class="btn">OK</button>
        </div>
    `
  wrap.querySelector('#voice-finish').addEventListener('click', () => {
    wrap.remove()
    if (typeof hideCallback === 'function') {
      hideCallback()
    }
  })
  return wrap
}
