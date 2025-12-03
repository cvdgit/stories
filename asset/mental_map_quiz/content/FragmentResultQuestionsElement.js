export default function FragmentResultQuestionsElement(historyItem) {
  const resultElement = document.createElement('div')
  resultElement.classList.add('result-item')
  resultElement.innerHTML = `
<div
        data-trigger="hover"
        data-container="body"
        style="white-space: nowrap"
        title="% сходства"
        class="result-item-value bs-tooltip ${historyItem?.done ? 'fragment-done' : ''}">${historyItem ? `${historyItem.all}%` : 'Нет результата'}</div>
    `
  return resultElement
}
