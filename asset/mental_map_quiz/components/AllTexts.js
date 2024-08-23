export default function AllTexts(texts, images, imageClickHandler) {
  const list = document.createElement('div')
  list.classList.add('mental-map-all-text-container')
  texts.map(textState => {

    const item = document.createElement('div')
    item.classList.add('text-container-row')

    const imageItem = document.createElement('div')
    imageItem.classList.add('image-item')

    const img = document.createElement('img')
    const image = images.find(i => i.id === textState.id)
    img.src = image.url
    img.style.cursor = 'pointer'
    img.addEventListener('click', e => {
      imageClickHandler(image)
    })

    imageItem.appendChild(img)
    item.appendChild(imageItem)

    const textItem = document.createElement('div')
    textItem.classList.add('text-item')

    textState.words.map(word => {
      const {type} = word
      if (type === 'break') {
        const breakElem = document.createElement('div')
        breakElem.classList.add('line-sep')
        textItem.appendChild(breakElem)
      } else {
        const span = document.createElement('span')
        span.classList.add('text-item-word')
        if (word.hidden) {
          span.classList.add('selected')
        }
        if (word?.target) {
          //word.hidden = true
          span.classList.add('selected')
          span.classList.add('word-target')
        }
        span.textContent = word.word
        span.addEventListener('click', () => {
          word.hidden = !word.hidden
          span.classList.toggle('selected')
        })
        textItem.appendChild(span)
      }
    })

    item.appendChild(textItem)
    list.appendChild(item)
  })

  return list
}