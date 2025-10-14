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
