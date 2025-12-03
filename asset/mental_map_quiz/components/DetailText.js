import DetailTextActions from "./DetailTextActions";
import {stripTags, removePunctuation} from "../common";

function hideWordsHandler(nodeList, hideHandler) {
  let hideWord = false;
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
        hideHandler(id);
        hideWord = false
      }
    }
  }
}

function createHideButtons(buttons) {
  const wrap = document.createElement('div');
  wrap.style.display = 'flex';
  wrap.style.flexDirection = 'row';
  wrap.style.gap = '10px';
  buttons.map(({name, percentage, clickHandler}) => {
    const btn = document.createElement('button');
    btn.classList.add('hide-words-btn', 'bs-tooltip');
    btn.setAttribute('type', 'button');
    btn.innerHTML = name;
    btn.setAttribute('data-trigger', 'hover');
    btn.setAttribute('data-container', 'body');
    btn.setAttribute('title', `Скрыть текст на ${percentage}%`);
    btn.addEventListener('click', e => clickHandler(percentage));
    wrap.appendChild(btn);
  });
  return wrap;
}

export default function DetailText(text, itemClickHandler, afterRandCallback, promptBtn, detailParams) {
  const detailText = document.createElement('div')
  detailText.classList.add('detail-text')

  function hideWordsByPercentage(percentage) {

    detailText.querySelectorAll('.text-item-word.selected')
      .forEach(node => node.classList.remove('selected'));

    text.words
      .filter(w => w.type === 'word')
      .map(w => w.hidden = false);

    const words = text.words
      .filter(w => w.type === 'word')
      .filter(w => removePunctuation(stripTags(w.word)).trim().length > 1);

    const totalWords = words.length;

    if (totalWords === 0) {
      return;
    }

    const byPercentWords = Math.round((totalWords * percentage) / 100);
    const allWordIds = words.map(w => w.id);

    const randWordIds = [];
    while (randWordIds.length < byPercentWords) {
      const num = Math.floor(Math.random() * allWordIds.length);
      if (!randWordIds.find(id => id === allWordIds[num])) {
        randWordIds.push(allWordIds[num]);
      }
    }

    randWordIds.map(wordId => {
      detailText
        .querySelector(`[data-word-id='${wordId}']`)
        .classList.add('selected');

      const word = text.words.find(w => w.id === wordId);
      word.hidden = true;
    });
  }

  const buttons = [
    {
      name: '20',
      percentage: 20,
      clickHandler: (percentage) => {
        hideWordsByPercentage(percentage);
        afterRandCallback();
      }
    },
    {
      name: '40',
      percentage: 40,
      clickHandler: (percentage) => {
        hideWordsByPercentage(percentage);
        afterRandCallback();
      }
    },
    {
      name: '60',
      percentage: 60,
      clickHandler: (percentage) => {
        hideWordsByPercentage(percentage);
        afterRandCallback();
      }
    },
    {
      name: '80',
      percentage: 80,
      clickHandler: (percentage) => {
        hideWordsByPercentage(percentage);
        afterRandCallback();
      }
    },
    {
      name: '100', percentage: 100, clickHandler: (percentage) => {
        detailText.querySelectorAll('.text-item-word:not(.selected)')
          .forEach(node => node.classList.add('selected'));
        text.words.map(w => w.hidden = true);
        afterRandCallback();
      }
    }
  ];

  detailText.appendChild(DetailTextActions(
    () => {
      const nodeList = detailText.querySelectorAll('.text-item-word');
      hideWordsHandler(nodeList, id => {
        const word = text.words.find(w => w.id === id);
        word.hidden = true;
      });
      afterRandCallback();
    },
    promptBtn,
    createHideButtons(buttons))
  )

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
        currentSpan.classList.add('word-target')
        word.hidden = true
        if (word.hidden) {
          currentSpan.classList.add('selected')
        }
      }
      currentSpan.addEventListener('click', () => {
        word.hidden = !word.hidden
        currentSpan.classList.toggle('selected')
        itemClickHandler()
      })
      detailText.appendChild(currentSpan)
    }
  })

  const {percentage} = detailParams || {};
  if (percentage) {
    hideWordsByPercentage(percentage);
  }

  return detailText
}
