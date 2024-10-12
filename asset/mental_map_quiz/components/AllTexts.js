import FragmentResultElement from "./FragmentResultElement";

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

export default function AllTexts(texts, images, history, imageClickHandler) {
  const list = document.createElement('div')
  list.classList.add('mental-map-all-text-container')
  texts.map(textState => {

    const item = document.createElement('div')
    item.classList.add('text-container-row')

    const imageItem = document.createElement('div')
    imageItem.classList.add('image-item')

    const img = document.createElement('img')
    img.style.marginBottom = '10px'

    const image = images.find(i => i.id === textState.id)
    item.dataset.imageFragmentId = image.id

    img.src = image.url
    img.style.cursor = 'pointer'
    img.addEventListener('click', e => {
      imageClickHandler(image)
    })

    imageItem.appendChild(img)

    imageItem.appendChild(FragmentResultElement(history.find(h => h.id === image.id)))

    item.appendChild(imageItem)

    const textItem = document.createElement('div')
    textItem.classList.add('text-item')

    appendAllTextWordElements(textState.words, textItem)

    item.appendChild(textItem)

    list.appendChild(item)
  })

  return list
}
