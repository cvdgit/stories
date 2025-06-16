import {_extends} from "../common";
import 'mathlive';

function MathQuestion(test) {
  this.element = null;
  this.container = test.container;
  this.deck = test.getDeck();
}

MathQuestion.prototype.createWrapper = function ({question, content} = {}) {
  console.log('createWrapper', content)

  var $answers = $("<div/>").addClass("wikids-test-answers");

  var $wrapper = $(`
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center">
<div class="question-image"></div>
<div class="question-wrapper" style="min-width: 400px"></div>
</div>`);
  $wrapper.find(".question-wrapper").append($answers);
  return $wrapper;

  /*const $wrapper = $('<div class="seq-question image-gaps-question"></div>');
  if (content) {
    $wrapper.append(content);
  }
  return $wrapper;*/
};

MathQuestion.prototype.create = function (question, imageElement, answersElement) {
  const {payload} = question

  imageElement.empty()
  //answersElement.empty()

  imageElement.html(`<math-field read-only style="display:inline-block">${payload.job}</math-field>`)

  return this.element;
}

MathQuestion.prototype.getContent = function(question) {
  const {payload} = question
  const wrap = document.createElement('div')
  wrap.style.display = 'flex'
  wrap.style.justifyContent = 'space-between'
  wrap.style.flexDirection = 'column'

  wrap.innerHTML = `<div style="display: flex; flex-direction: column">
    <h5>Задание:</h5>
    <div style="display: flex; align-items: center; justify-content: center">
    <math-field read-only style="display:inline-block">${payload.job}</math-field></div>
    <h5>Верный ответ:</h5>
    <div class="math-correct-answers" style="display: flex; flex-direction: column; row-gap: 10px; align-items: center; justify-content: center"></div>
</div>
  `;

  (question.storyTestAnswers.filter(a => Number(a.is_correct) === 1) || []).map(a => {
    const el = document.createElement('div')
    el.innerHTML = `<math-field read-only style="display:inline-block">${a.name}</math-field>`
    wrap.querySelector('.math-correct-answers').appendChild(el)
  })


  return wrap
}

_extends(MathQuestion, {
  pluginName: 'mathQuestion'
});

export default MathQuestion
