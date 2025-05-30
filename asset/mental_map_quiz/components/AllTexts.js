import FragmentResultElement from "./FragmentResultElement";
import FragmentResultQuestionsElement from "../content/FragmentResultQuestionsElement";

export function appendAllTextWordElements(words, container) {
  words.map(word => {
    const {type} = word
    if (type === 'break') {
      const breakElem = document.createElement('div')
      breakElem.classList.add('line-sep')
      container.appendChild(breakElem)
    } else {
      const span = document.createElement('span')
      span.classList.add('text-item-word')
      if (word?.target) {
        span.classList.add('word-target')
      }
      span.textContent = word.word
      container.appendChild(span)
    }
  })
}

export default function AllTexts(texts, images, history, imageClickHandler, isMentalMapQuestions) {
  const list = document.createElement('div')
  list.classList.add('mental-map-all-text-container')
  texts.map(textState => {

    const item = document.createElement('div')
    item.classList.add('text-container-row')

    const imageItem = document.createElement('div')
    imageItem.classList.add('image-item')

    const image = images.find(i => i.id === textState.id)
    item.dataset.imageFragmentId = image.id

    if (image.url) {
      const img = document.createElement('img')
      img.style.marginBottom = '10px'
      img.src = image.url
      img.style.cursor = 'pointer'
      img.addEventListener('click', e => {
        imageClickHandler(image)
      })
      imageItem.appendChild(img)
    } else {
      const div = document.createElement('div')
      div.style.marginBottom = '10px'
      //div.style.padding = '20px'
      div.style.cursor = 'pointer'
      div.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
</svg>
`
      div.addEventListener('click', e => {
        imageClickHandler(image)
      })
      imageItem.appendChild(div)
    }

    const historyItem = history.find(h => h.id === image.id)
    imageItem.appendChild(isMentalMapQuestions ? FragmentResultQuestionsElement(historyItem) : FragmentResultElement(historyItem))

    item.appendChild(imageItem)

    const textItem = document.createElement('div')
    textItem.classList.add('text-item')

    appendAllTextWordElements(textState.words, textItem)

    item.appendChild(textItem)

    list.appendChild(item)
  })

  return list
}
