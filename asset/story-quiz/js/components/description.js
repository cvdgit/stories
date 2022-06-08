
export default function createDescription(text) {
  const descriptionElement = document.createElement('div');
  descriptionElement.classList.add('question-description');
  descriptionElement.innerHTML =
    `<div class="question-description__inner">
           <p class="question-description__text">${text}</p>
         </div>`;
  return descriptionElement;
}
