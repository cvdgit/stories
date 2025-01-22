import {v4 as uuidv4} from "uuid";

function decodeHtml(html) {
  const txt = document.createElement("textarea");
  txt.innerHTML = html;
  return txt.value;
}

function processImageText(text) {
  const textFragments = new Map();
  const reg = new RegExp(`<span[^>]*>(.*?)<\\/span>`, 'gm');
  const imageText = decodeHtml((text || '').replace(/&nbsp;/g, ' ')).replace(reg, (match, p1) => {
    const id = uuidv4()
    textFragments.set(`${id}`, `${p1.trim()}`)
    return `{${id}}`
  })
  return {
    imageText,
    textFragments
  }
}

export function createWordsFormText(text) {
  const {imageText, textFragments} = processImageText(text)
  const paragraphs = imageText.split('\n')
  const words = paragraphs.map(p => {
    if (p === '') {
      return [{type: 'break'}]
    }
    const words = p.split(' ').map(word => {
      if (word.indexOf('{') > -1) {
        const id = word.toString().replace(/[^\w\-]+/gmui, '')
        if (textFragments.has(id)) {
          const reg = new RegExp(`{${id}}`)
          word = word.replace(reg, textFragments.get(id))
          return word.split(' ').map(w => ({id: uuidv4(), word: w, type: 'word', hidden: true, target: true}))
        }
      }
      return [{id: uuidv4(), word, type: 'word', hidden: false}]
    })
    return [...(words.flat()), {type: 'break'}]
  }).flat()
  return words
}

/**
 * @param {array} currentWords
 * @returns {string}
 */
export function getTextBySelections(currentWords) {
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
