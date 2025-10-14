function decodeHtml(html) {
  const txt = document.createElement("textarea");
  txt.innerHTML = html;
  return txt.value;
}

function createWordItem(itemText, itemId) {

  function processImageText(text) {
    const textFragments = new Map();
    const reg = new RegExp(`<span[^>]*>(.*?)<\\/span>`, 'gm');
    const imageText = decodeHtml(text.replace(/&nbsp;/g, ' ')).replace(reg, (match, p1) => {
      const id = uuidv4()
      textFragments.set(`${id}`, `${p1.trim()}`)
      return `{${id}}`
    })
    return {
      imageText,
      textFragments
    }
  }

  const {imageText, textFragments} = processImageText(itemText)
  const paragraphs = imageText.split('\n')
  const words = paragraphs.map(p => {
    if (p === '') {
      return [{type: 'break'}]
    }
    const words = p.split(' ').filter(w => w).map(word => {
      if (word.indexOf('{') > -1) {
        const id = word.toString().replace(/[^\w\-]+/gmui, '')
        if (textFragments.has(id)) {
          const reg = new RegExp(`{${id}}`)
          word = word.replace(reg, textFragments.get(id))
          // return word.split(' ').map(w => ({id: uuidv4(), word: w, type: 'word', hidden: true, target: true}))
          const merge = word.split(' ').length > 1
          return {id: uuidv4(), word, type: 'word', hidden: true, target: true, merge}
        }
      }
      return [{id: uuidv4(), word, type: 'word', hidden: false}]
    })
    return [...(words.flat()), {type: 'break'}]
  }).flat()
  return {
    id: itemId,
    text: itemText,
    words
  }
}

function appendWordElements(words, el) {
  words.map(word => {
    const {type} = word
    if (type === 'break') {
      const breakElem = document.createElement('div')
      breakElem.classList.add('line-sep')
      el.appendChild(breakElem)
    } else {
      const currentSpan = document.createElement('span')
      currentSpan.classList.add('text-item-word')
      currentSpan.dataset.wordId = word.id
      currentSpan.innerHTML = word.word
      if (word.hidden) {
        currentSpan.classList.add('selected')
      }
      if (word?.target) {
        currentSpan.classList.add('word-target')
        word.hidden = true
        if (word.hidden) {
          currentSpan.classList.add('selected')
        }
      }
      el.appendChild(currentSpan)
    }
  })
}

function getTextBySelections(currentWords) {
  let text = ''
  currentWords.map(word => {
    if (word.type === 'break') {
      text += "\n"
    } else {
      text += (word.hidden ? `<span class="target-text">${word.word}</span>` : word.word) + ' '
    }
  })
  return text.trim()
}
