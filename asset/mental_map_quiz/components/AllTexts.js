export function appendWordElements(words, container, init, clickHandler) {
  words.map(word => {
    const {type} = word
    if (type === 'break') {
      const breakElem = document.createElement('div')
      breakElem.classList.add('line-sep')
      container.appendChild(breakElem)
    } else {
      const span = document.createElement('span')
      span.classList.add('text-item-word')
      if (word.hidden) {
        word.hidden = true
        span.classList.add('selected')
      }
      if (word?.target) {
        span.classList.add('word-target')
        if (init === true) {
          word.hidden = true
          span.classList.add('selected')
        }
      }
      span.textContent = word.word
      span.addEventListener('click', () => {
        word.hidden = !word.hidden
        span.classList.toggle('selected')
      })
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

    const resultElement = document.createElement('div')
    resultElement.classList.add('result-item')
    const historyItem = history.find(h => h.id === image.id)
    resultElement.innerHTML = `
      <div class="result-item-value">${historyItem ? `${historyItem.all}% (${historyItem.hiding}% / ${historyItem?.target}%)` : 'Нет результата'}</div>
    `
    imageItem.appendChild(resultElement)

    item.appendChild(imageItem)

    const textItem = document.createElement('div')
    textItem.classList.add('text-item')

    appendWordElements(textState.words, textItem, true)

    item.appendChild(textItem)

    list.appendChild(item)
  })

  return list
}
