import FragmentResultElement from "./FragmentResultElement";
import FragmentResultQuestionsElement from "../content/FragmentResultQuestionsElement";
import {buttons} from "../words";

export function appendAllTextWordElements(words, container) {
  words.map(word => {
    const {type} = word
    if (type === 'break') {
      const breakElem = document.createElement('div')
      breakElem.classList.add('line-sep')
      container.appendChild(breakElem)
    } else {
      const span = document.createElement('span')
      span.classList.add('text-item-word')
      if (word?.target) {
        span.classList.add('word-target')
      }
      span.textContent = word.word
      container.appendChild(span)
    }
  })
}

function createHideButtons(buttons, clickHandler) {
  const wrap = document.createElement('div');
  wrap.style.display = 'flex';
  wrap.style.flexDirection = 'row';
  wrap.style.gap = '10px';
  wrap.style.fontSize = '22px';
  wrap.style.lineHeight = '28px';
  buttons.map(({name, percentage}) => {
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

export default function AllTexts(texts, images, history, imageClickHandler, isMentalMapQuestions) {
  const list = document.createElement('div')
  list.classList.add('mental-map-all-text-container')
  texts.map(textState => {

    const item = document.createElement('div')
    item.classList.add('text-container-row')

    const imageItem = document.createElement('div')
    imageItem.classList.add('image-item')
    imageItem.style.width = 'auto';
    imageItem.style.maxWidth = '300px';
    imageItem.style.gap = '15px';
    imageItem.style.alignItems = 'center';

    const image = images.find(i => i.id === textState.id)
    item.dataset.imageFragmentId = image.id

    if (image.url) {
      const img = document.createElement('img')
      //img.style.marginBottom = '10px'
      img.src = image.url
      img.style.cursor = 'pointer'
      img.addEventListener('click', e => {
        imageClickHandler(image)
      })
      imageItem.appendChild(img)
    } else {
      const div = document.createElement('div')
      //div.style.marginBottom = '10px'
      //div.style.padding = '20px'
      div.style.cursor = 'pointer'
      div.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
</svg>
`
      div.addEventListener('click', e => {
        imageClickHandler(image)
      })
      imageItem.appendChild(div)
    }

    imageItem.appendChild(createHideButtons(buttons, (percentage) => imageClickHandler(image, {percentage})));

    const historyItem = history.find(h => h.id === image.id)
    imageItem.appendChild(isMentalMapQuestions ? FragmentResultQuestionsElement(historyItem) : FragmentResultElement(historyItem))

    item.appendChild(imageItem)

    const textItem = document.createElement('div')
    textItem.classList.add('text-item')

    appendAllTextWordElements(textState.words, textItem)

    item.appendChild(textItem)

    list.appendChild(item)
  })

  return list
}
