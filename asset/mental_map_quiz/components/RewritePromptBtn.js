export default function RewritePromptBtn(clickHandler) {
  const promptBtn = document.createElement('button')
  promptBtn.setAttribute('type', 'button')
  promptBtn.style.marginRight = '10px'
  promptBtn.textContent = 'Rewrite prompt'
  promptBtn.addEventListener('click', clickHandler)
  return promptBtn
}
