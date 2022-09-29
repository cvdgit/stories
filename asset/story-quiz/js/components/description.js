
export default function createDescription(text) {
  const descriptionElement = document.createElement('div');
  descriptionElement.classList.add('question-description');
  descriptionElement.innerHTML =
    `<div class="question-description__inner">
           <div class="question-description__text">${text}</div>
         </div>`;
  return descriptionElement;
}
