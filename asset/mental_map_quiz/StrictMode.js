import tippy from "tippy.js";
import 'tippy.js/dist/tippy.css';

export default function StrictMode({canChange, checkHandler, defaultValue}) {

  const element = document.createElement('div')
  element.className = 'strict-mode-wrap'
  element.style = 'font-size: 20px'
  element.innerHTML = `<b>Включен строгий режим</b>`

  if (canChange) {
    element.innerHTML = `<label>Строгий режим <input type="checkbox" ${defaultValue ? 'checked' : ''}></label>`
    element.querySelector('input[type=checkbox]').addEventListener('click', e => {
      if (checkHandler(e.target.checked) === false) {
        e.preventDefault()
        e.stopPropagation()
      }
    })
  }

  tippy(element, {
    content: `Окно браузера должно занимать всю ширину экрана.<br>
  Масштаб внутри вкладки должен быть 100%<br>
  Во время проговаривания нельзя уводить указатель мыши за пределы вкладки и переключаться между окнами`,
    interactive: true,
    allowHTML: true,
    maxWidth: '40em',
    appendTo: () => document.body,
  })

  return {
    render() {
      return element
    }
  }
}
