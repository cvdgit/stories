import {_extends} from "../common";
import 'mathlive';
import {MathfieldElement} from "mathlive";

MathfieldElement.fontsDirectory = '/build/fonts'

function MathQuestion(test) {
  this.element = null;
  this.container = test.container;
  this.deck = test.getDeck();
}

MathQuestion.prototype.createWrapper = function (question) {

  const {payload} = question

  if (payload.isGapsQuestion) {
    return $(`<div class="math-gaps-wrap" style="display: flex; flex-direction: column"></div>`)
  }

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

  const mf = imageElement[0].querySelector('math-field')
  mf.mathVirtualKeyboardPolicy = "manual";

  return this.element;
}

MathQuestion.prototype.createInput = function (question, imageElement, answersContainer, handler) {
  const {payload} = question
  imageElement.empty()
  imageElement.html(`<math-field read-only style="display:inline-block">${payload.job}</math-field>`)
  /*answersContainer.append(`<div style="margin-top: 30px">
<label for="" style="margin-right: 10px">Введите ответ и нажмите Enter:</label>
<input spellcheck="false" class="answer-input" type="text">
</div>`)*/

  answersContainer.append(`<div style="margin-top: 30px">
<label for="" style="margin-right: 10px">Введите ответ и нажмите Enter:</label>
<math-field class="answer-input">
</div>`)

  answersContainer.find('math-field')
    /*.on('paste', e => {
      e.preventDefault()
      return false
    })*/
    .on('keypress', e => {
      if (e.which === 13) {
        handler(e.target.value)
        return false;
      }
    })



  return this.element
}

MathQuestion.prototype.createGapsQuestion = function(question, imageElement) {
  const {payload} = question
  console.log(payload)
  imageElement.empty()
  imageElement.html(payload.job)
  return this.element
}

MathQuestion.prototype.checkGapsAnswers = function(question, userAnswers) {
  const {payload} = question

  const userAnswer = userAnswers.reduce((total, current) => total.toString() + current.answer.toString(), '')
  if (userAnswer.trim() === '') {
    return false
  }

  let correct = true
  payload.fragments.map(f => {
    if (!f.placeholders.length) {
      return
    }
    f.placeholders.map(p => correct && p.value === userAnswers.find(a => a.placeholder === p.id).answer)
  })

  return correct
}

MathQuestion.prototype.userAnswers = function(question, elem) {
  const {payload} = question
  let answers = []
  elem.find('math-field').map((i, mf) => {
    const fragmentId = mf.dataset.id
    const fragment = payload.fragments.find(f => f.id === fragmentId)
    if (!fragment.placeholders.length) {
      return
    }
    answers = [...answers, ...fragment.placeholders.map(p => ({placeholder: p.id, answer: mf.getPromptValue(p.id)}))]
  })
  const userAnswer = answers.reduce((total, current) => total.toString() + current.answer.toString(), '')
  if (userAnswer.trim() === '') {
    return []
  }
  return answers
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
