import DetailTextActions from "./DetailTextActions";

export default function DetailText(text, itemClickHandler, afterRandCallback) {
  const detailText = document.createElement('div')
  detailText.classList.add('detail-text')

  let hideWord = false
  detailText.appendChild(DetailTextActions(() => {
    const nodeList = detailText.querySelectorAll('.text-item-word')
    for (let i = 0, curEl, nextEl; i < nodeList.length; i++) {
      curEl = nodeList[i]
      nextEl = nodeList[i + 1]
      if (curEl.classList.contains('selected')) {
        hideWord = false
        continue
      }
      if (hideWord === false) {
        hideWord = true
        continue
      }
      if (hideWord && curEl.innerText.length > 1) {
        if (curEl.classList.contains('selected') || nextEl?.classList.contains('selected')) {
          hideWord = false
        } else {
          curEl.classList.add('selected')
          const id = curEl.dataset.wordId

          const word = text.words.find(w => w.id === id)
          word.hidden = true

          hideWord = false
        }
      }
    }
    afterRandCallback()
  }))

  text.words.map(word => {
    const {type} = word
    if (type === 'break') {
      const breakElem = document.createElement('div')
      breakElem.classList.add('line-sep')
      detailText.appendChild(breakElem)
    } else {
      const currentSpan = document.createElement('span')
      currentSpan.classList.add('text-item-word')
      currentSpan.dataset.wordId = word.id
      currentSpan.innerHTML = word.word
      if (word.hidden) {
        currentSpan.classList.add('selected')
      }
      if (word?.target) {
        word.hidden = true
        currentSpan.classList.add('selected')
        currentSpan.classList.add('word-target')
      }
      currentSpan.addEventListener('click', () => {

        word.hidden = !word.hidden
        currentSpan.classList.toggle('selected')
        itemClickHandler()

        /*
        if (voiceResponse.getStatus()) {
          voiceResponse.stop()
          startRecording(recordingWrap.querySelector('#start-recording'))
        }
        ['#result_span', '#final_span', '#interim_span'].map(q => {
          detailTextWrap.querySelector(q).innerHTML = ''
          recordingWrap.querySelector('#start-retelling-wrap').style.display = 'none'
        })

        recordingWrap.querySelector('#hidden-text-percent').innerText = calcHiddenTextPercent(text)
        */
      })
      detailText.appendChild(currentSpan)
    }
  })

  return detailText
}