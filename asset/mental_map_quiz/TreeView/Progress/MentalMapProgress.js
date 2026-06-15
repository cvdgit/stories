import './MentalMapProgress.css'
import tippy from "tippy.js";

export default function MentalMapProgress({percent, words, hiddenWords}) {
  const element = document.createElement('div')
  element.className = 'progress-wrap'
  element.innerHTML = `<div class="map-progress">
                    <div class="progress__container">
                        <div class="progress__container-indicator" style="transform: translate3d(${percent || 0}%, 0px, 0px);"></div>
                    </div>
                </div>`

  let tipContent = `Слов ${hiddenWords} из ${words} - ${percent}%`
  const tippyInstance = tippy(element, {
    content: tipContent,
    interactive: true,
    allowHTML: true,
    maxWidth: '60em',
    appendTo: () => document.body,
  })

  return {
    render() {
      return element
    },
    setProgress(progress) {
      const {
        percent,
        words,
        hiddenWords
      } = progress
      if (isNaN(percent)) {
        throw new Error('Progress value must be number')
      }

      tippyInstance.setContent(`Слов ${hiddenWords} из ${words} - ${percent}%`)

      element.querySelector('.progress__container-indicator').style.transform = `translate3d(${percent}%, 0px, 0px)`
    }
  }
}
