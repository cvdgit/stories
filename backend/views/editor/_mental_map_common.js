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

async function processContentMentalMaps(toCreateMaps, fragments) {

  const sendAIRequest = toCreateMaps
    .filter(({type}) => type === 'mental-map-plan' || type === 'mental-map-plan-accumulation')
    .length > 0;

  const allText = fragments.map(({title}) => title).join('\n');

  let sentences;
  if (sendAIRequest) {
    await window.sendStreamMessage(
      `/admin/index.php?r=gpt/story/speech-trainer-sentences`,
      {text: allText},
      () => {},
      sentencesJson => {
        try {
          sentences = window.processOutputAsJson(sentencesJson).map(({sentenceText, sentenceTitle}) => {
            const fragmentId = uuidv4();
            return {
              id: fragmentId,
              sentenceText,
              sentenceTitle,
              words: createWordItem(sentenceText, fragmentId).words
            }
          });
        } catch (ex) {
          throw new Error(ex.message);
        }
      }
    );
  }

  const sendTranslateAIRequest = toCreateMaps
    .filter(({type}) => type === 'mental-map-plan-translate')
    .length > 0;
  let translateSentences;
  if (sendTranslateAIRequest) {
    await window.sendStreamMessage(
      `/admin/index.php?r=gpt/story/speech-trainer-translate`,
      {text: allText},
      () => {},
      sentencesJson => {
        try {
          translateSentences = processOutputAsJson(sentencesJson).map(({sentenceText, sentenceTranslateText}) => {
            const fragmentId = uuidv4();
            return {
              id: fragmentId,
              sentenceText,
              sentenceTitle: sentenceTranslateText,
              words: createWordItem(sentenceText, fragmentId).words
            }
          });
        } catch (ex) {
          throw new Error(ex.message);
        }
      }
    );
  }

  const sendQuestionAIRequest = toCreateMaps
    .filter(({type}) => type === 'mental-map-plan-question')
    .length > 0;
  let questionSentences;
  if (sendQuestionAIRequest) {
    await window.sendStreamMessage(
      `/admin/index.php?r=gpt/story/speech-trainer-question`,
      {text: allText},
      () => {},
      sentencesJson => {
        try {
          questionSentences = processOutputAsJson(sentencesJson).map(({sentenceText, sentenceQuestion}) => {
            const fragmentId = uuidv4();
            return {
              id: fragmentId,
              sentenceText,
              sentenceTitle: sentenceQuestion,
              words: createWordItem(sentenceText, fragmentId).words
            }
          });
        } catch (ex) {
          throw new Error(ex.message);
        }
      }
    );
  }

  const mentalMapsAi = new MentalMapsAi()
  for (let i = 0; i < toCreateMaps.length; i++) {
    const type = toCreateMaps[i].type
    switch (type) {
      case 'mental-map':
        structuredClone(fragments).map(f => toCreateMaps[i].fragments.push({
          id: f.id,
          title: getTextBySelections(f.words)
        }))
        break;
      case 'mental-map-even-fragments':
        structuredClone(fragments).map(f => toCreateMaps[i].fragments.push({
          id: f.id,
          title: mentalMapsAi.hideWordsEven(f.words)
        }))
        break;
      case 'mental-map-odd-fragments':
        structuredClone(fragments).map(f => toCreateMaps[i].fragments.push({
          id: f.id,
          title: mentalMapsAi.hideWordsOdd(f.words)
        }))
        break;
      case 'mental-map-plan':
        structuredClone(sentences).map(({id, sentenceText, sentenceTitle}) => toCreateMaps[i].fragments.push({
          id,
          title: sentenceTitle,
          description: sentenceText
        }))
        break;
      case 'mental-map-plan-accumulation':
        structuredClone(sentences).map(({id, sentenceText, sentenceTitle}) => toCreateMaps[i].fragments.push({
          id,
          title: sentenceTitle,
          description: sentenceText
        }))
        break;
      case 'mental-map-plan-translate':
        structuredClone(translateSentences).map(({id, sentenceText, sentenceTitle}) => toCreateMaps[i].fragments.push({
          id,
          title: sentenceTitle,
          description: sentenceText
        }))
        break;
      case 'mental-map-plan-question':
        structuredClone(questionSentences).map(({id, sentenceText, sentenceTitle}) => toCreateMaps[i].fragments.push({
          id,
          title: sentenceTitle,
          description: sentenceText
        }))
        break;
    }
  }

  const text = fragments.map(f => getTextBySelections(f.words)).join('<br/>');

  return {
    text,
    mentalMaps: JSON.stringify(toCreateMaps)
  }
}
