import {v4 as uuidv4} from "uuid";
import {decodeHtml} from "../common";

export function calcHiddenTextPercent(detailTexts) {
  let totalCounter = 0;
  let hiddenCounter = 0;
  detailTexts.words.map(w => {
    if (w.type === 'word') {
      totalCounter++;
      if (w.hidden) {
        hiddenCounter++
      }
    }
  })
  return totalCounter === 0 || hiddenCounter === 0 ? 0 : Math.round(hiddenCounter * 100 / totalCounter)
}

export function calcTargetTextPercent(detailTexts) {
  let targetCounter = 0;
  let targetHiddenCounter = 0;
  detailTexts.words.map(w => {
    if (w.type === 'word' && w?.target) {
      targetCounter++
      if (w.hidden) {
        targetHiddenCounter++
      }
    }
  })
  return targetCounter === 0 || targetHiddenCounter === 0 ? 0 : Math.round(targetHiddenCounter * 100 / targetCounter)
}

export function getTargetWordsCount(detailTexts) {
  return detailTexts.words.filter(w => w.type === 'word' && w?.target).length
}

export function canRecording(detailTexts) {
  if (getTargetWordsCount(detailTexts) === 0) {
    return true
  }
  return calcTargetTextPercent(detailTexts) === 100
}

export function createWordItem(itemText, itemId) {

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
          return word.split(' ').map(w => ({id: uuidv4(), word: w, type: 'word', hidden: false, target: true}))
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

let hideWord = false

function hideWords(nodeList, words, even = true) {
  let counter = 0;
  for (let i = 0, curEl, nextEl; i < nodeList.length; i++) {
    curEl = nodeList[i]
    nextEl = nodeList[i + 1]
    if (curEl.innerText.length > 1) {
      if (curEl.classList.contains('selected') || nextEl?.classList.contains('selected')) {
        hideWord = false
      } else {
        counter++
        if (even && counter % 2 !== 0) {
          continue
        }
        if (even === false && counter % 2 === 0) {
          continue
        }
        curEl.classList.add('selected')
        const id = curEl.dataset.wordId
        const word = words.find(w => w.id === id)
        word.hidden = true
        hideWord = false
      }
    }
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

export function hideWordsOdd(words) {
  const el = document.createElement('div')
  appendWordElements(words, el)
  hideWord = true
  hideWords(el.querySelectorAll('.text-item-word'), words, false)
  return getTextBySelections(words)
}

export function hideWordsEven(words) {
  const el = document.createElement('div')
  appendWordElements(words, el)
  hideWord = false
  hideWords(el.querySelectorAll('.text-item-word'), words)
  return getTextBySelections(words)
}
