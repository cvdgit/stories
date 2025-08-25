function MentalMapsAi() {

  async function sendMessage(payload, onMessage, onEndCallback) {
    let accumulatedMessage = ''
    return sendEventSourceMessage({
      url: '/admin/index.php?r=gpt/mental-map/text-fragments',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'text/event-stream',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify(payload),
      onMessage: (streamedResponse) => {
        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }
        onMessage(accumulatedMessage)
      },
      onError: (streamedResponse) => {
        console.log(streamedResponse)
      },
      onEnd: () => onEndCallback(accumulatedMessage)
    })
  }

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

  this.createMentalMaps = (text, endCallback) => {
    sendMessage({text}, () => {}, endCallback)
  }

  this.processFragment = (textFragment) => {
    return createWordItem(textFragment, '123')
  }

  let hideWord = false
  function hideWords(nodeList, words, even = true) {
    let counter = 0;
    for (let i = 0, curEl, nextEl; i < nodeList.length; i++) {

      curEl = nodeList[i]
      nextEl = nodeList[i + 1]

      /*if (curEl.classList.contains('selected') || curEl.innerText === '-') {
        hideWord = false
        continue
      }
      if (hideWord === false) {
        hideWord = true
        continue
      }*/

      if (/*hideWord && */curEl.innerText.length > 1) {
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

  this.hideWordsOdd = (words) => {
    const el = document.createElement('div')
    appendWordElements(words, el)
    hideWord = true
    hideWords(el.querySelectorAll('.text-item-word'), words, false)
    return getTextBySelections(words)
  }

  this.hideWordsEven = (words) => {
    const el = document.createElement('div')
    appendWordElements(words, el)
    hideWord = false
    hideWords(el.querySelectorAll('.text-item-word'), words)
    return getTextBySelections(words)
  }
}
