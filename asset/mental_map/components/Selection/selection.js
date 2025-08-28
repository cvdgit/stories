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
  return paragraphs.map(p => {
    if (p === '') {
      return [{type: 'break'}]
    }
    const words = p.split(' ').map(word => {
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

export function wordClickHandler(word, prevState, ctrlKey) {
  const state = [...prevState]
    .map((w, j) => {
      if (ctrlKey) {
        if (w.id === word.id) {
          let targetWord
          const prevWord = prevState[j - 1]
          if (prevWord && prevWord.hidden) {
            targetWord = prevWord
            targetWord.word += ` ${w.word}`
            targetWord.merge = true
            w.word = ''
          }
          const nextWord = prevState[j + 1]
          if (nextWord && nextWord.hidden) {
            if (!targetWord) {
              targetWord = w
            }
            targetWord.word += ` ${nextWord.word}`
            targetWord.merge = true
            targetWord.hidden = !w.hidden
            nextWord.word = ''
          }
        }
        return w
      }
      if (w.id === word.id) {
        w.hidden = !w.hidden
        w.merge = false
      }
      return w
    })
    .filter(w => w.type === 'word' && w.word.trim().length > 0)

  state
    .filter((w, wordIndex) => {
      const filtered = w.type === 'word' && !w.hidden && w.word.split(' ').length > 1
      if (filtered) {
        w.index = wordIndex
      }
      return filtered
    })
    .map(w => {
      w.word.trim().split(' ').map((splitWord, splitIndex) => {
        if (splitIndex === 0) {
          w.word = splitWord
          return
        }
        state.splice(w.index + splitIndex, 0, {...w, id: uuidv4(), word: splitWord})
      })
    })

  return state
}
