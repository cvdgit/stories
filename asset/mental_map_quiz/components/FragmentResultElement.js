export default function FragmentResultElement(historyItem) {
  const resultElement = document.createElement('div')
  resultElement.classList.add('result-item')
  resultElement.innerHTML = `
<div
        data-triggre="hover"
        data-container="body"
        style="white-space: nowrap"
        title="% сходства (% закрытия текста / % закрытия важного текста)"
        class="result-item-value ${historyItem.done ? 'fragment-done' : ''}">${historyItem ? `${historyItem.all}% (${historyItem.hiding}% / ${historyItem?.target}%)` : 'Нет результата'}</div>
    `
  return resultElement
}
