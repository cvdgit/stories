import {_extends, shuffle} from "../common";
import 'mathlive';
import {MathfieldElement} from "mathlive";
import './StepQuestion.css';

MathfieldElement.fontsDirectory = '/build/fonts'

const stepQuestionState = new Map()

function StepQuestion(test) {
  this.element = null;
  this.container = test.container;
  this.deck = test.getDeck();
}

StepQuestion.prototype.createWrapper = function (question) {
  const {payload} = question
  return $(`<div class="step-question-wrap" style="display: flex; flex-direction: column; width: 100%; height: 100%"></div>`)
};

StepQuestion.prototype.getContent = function(question) {
  const {payload} = question
  const wrap = document.createElement('div')
  wrap.style.display = 'flex'
  wrap.style.justifyContent = 'space-between'
  wrap.style.flexDirection = 'column'

  /*wrap.innerHTML = `<div style="display: flex; flex-direction: column">
    <h5>Задание:</h5>
    <div style="display: flex; align-items: center; justify-content: center">
    <math-field read-only style="display:inline-block">${payload.job}</math-field></div>
    <h5>Верный ответ:</h5>
    <div class="math-correct-answers" style="display: flex; flex-direction: column; row-gap: 10px; align-items: center; justify-content: center"></div>
</div>
  `;*/

  /*(question.storyTestAnswers.filter(a => Number(a.is_correct) === 1) || []).map(a => {
    const el = document.createElement('div')
    el.innerHTML = `<math-field read-only style="display:inline-block">${a.name}</math-field>`
    wrap.querySelector('.math-correct-answers').appendChild(el)
  })*/

  return wrap
}

function createStepElement({id, index, name, job, isAnswerOptions, answers, fragments}, changeHandler, stepState) {
  const $elem = $('<div/>', {class: 'step-step', 'data-id': id})
  if (stepState.active) {
    $elem.addClass('step-active')
  }

  $elem.append(`<div class="step-header"><div class="step-index"><b>Шаг ${index}.</b></div><div style="flex: 1">${name}</div><div class="step-status"></div></div>`)

  $elem.append('<div class="step-content" />')
  const $content = $elem.find('.step-content')

  const stepIsDoneCorrect = stepState.done && stepState.correct
  if (stepIsDoneCorrect) {
    $elem.addClass('step-correct')
    $elem.find('.step-status').addClass('step-status-correct').append(
      `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>
`
    )
    if (isAnswerOptions) {
      $content.append(`<div class="step-answers"/>`)
      const multiple = answers.filter(a => a.correct).length > 1
      answers
        .filter(a => a.correct)
        .map(answer => {
          let input;
          if (multiple) {
            input = `<label class="step-answer-label"><input disabled type="checkbox" checked value="${answer.id}" /> ${answer.title}</label>`
          } else {
            input = `<label class="step-answer-label"><input disabled type="radio" checked value="${answer.id}" name="step${index}" /> ${answer.title}</label>`
          }
          $elem.find('.step-answers').append(input)
        })
    } else {
      stepState.answers.map(a => {
        job = job.replaceAll(`\\placeholder[${a.placeholder}]{}`, a.answer)
      })
      $content.append(`<div class="step-job">${job}</div>`)
    }
    return $elem
  }

  if (isAnswerOptions) {
    $content.append(`<div class="step-answers"/>`)
    const multiple = answers.filter(a => a.correct).length > 1

    answers = shuffle(answers)

    answers.map(answer => {
      let input;
      if (multiple) {
        input = `<label class="step-answer-label"><input type="checkbox" value="${answer.id}" /> ${answer.title}</label>`
      } else {
        input = `<label class="step-answer-label"><input type="radio" value="${answer.id}" name="step${index}" /> ${answer.title}</label>`
      }
      $elem.find('.step-answers').append(input)
    })
    $elem.find('.step-answers').on('change', 'input', e => {
      let values = []
      if (multiple) {
        $elem
          .find('.step-answers')
          .find('input[type=checkbox]:checked')
          .map((i, el) => values.push($(el).val()))
      } else {
        values.push(e.target.value)
      }
      changeHandler(id, values, answers.filter(a => a.correct).length)
    })
  } else {
    $content.append(`<div class="step-job"><div class="step-job-job">${job}</div><div class="step-job-check"></div></div>`)
    const checkJobBtn = $('<button type="button" class="btn">Проверить</button>').on('click', () => {
      let mathAnswers = []
      $elem
        .find('.step-job-job')
        .find('math-field')
        .map((i, mf) => {
          const fragmentId = mf.dataset.id
          const fragment = fragments.find(f => f.id === fragmentId)
          if (!fragment.placeholders.length) {
            return
          }
          mathAnswers = [...mathAnswers, ...fragment.placeholders.map(p => ({fragmentId: fragment.id, placeholder: p.id, answer: mf.getPromptValue(p.id)}))]
        })
      const userAnswer = mathAnswers.reduce((total, current) => total.toString() + current.answer.toString(), '')
      if (userAnswer.trim() === '') {
        return
      }
      changeHandler(id, mathAnswers)
    })
    $elem.find('.step-job-check').append(
      checkJobBtn
    )
  }

  const stepIsDoneIncorrect = stepState.done && stepState.correct === false
  if (stepIsDoneIncorrect) {
    $elem.find('.step-status').addClass('step-status-incorrect').append(
      `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>
`)
  }

  return $elem
}

function processNextActiveStep(stepsState) {
  const activeStep = stepsState.filter(i => i.active && i.done === false)
  if (activeStep.length > 0) {
    return activeStep[0].id
  }
  let activeStepId
  for (let i = 0; i < stepsState.length; i++) {
    const item = stepsState[i]
    if (item.active === false && item.done === false) {
      item.active = true
      activeStepId = item.id
      break
    }
  }
  return activeStepId
}

StepQuestion.prototype.create = function(question, container) {
  const {payload} = question;
  container.append(`<div class="step-question">${payload.job}</div>`)
  container.append(`<div class="step-steps"></div>`)

  const stepsState = payload.steps.map((s, i) => ({
    id: s.id,
    active: false,
    done: false,
    correct: false,
    message: '',
    answers: []
  }))

  function answerHandler(stepId, answers, correctAnswersNumber) {
    const step = payload.steps.find(s => s.id === stepId)

    let correct = false
    if (step.isAnswerOptions) {
      const correctValues = step.answers.filter(a => a.correct).map(a => a.id)
      correct = correctValues.every(correctValue =>  answers.some(value => correctValue === value))
    } else {
      correct = true
      step.fragments.map(f => {
        if (!f.placeholders.length) {
          return
        }
        correct = correct && f.placeholders.reduce((placeCorrect, p) => {
          return placeCorrect && p.value === answers.find(a => a.placeholder === p.id).answer
        }, true)
      })
    }

    const stateItem = stepsState.find(i => i.id === stepId)
    if (correct) {

      stateItem.active = false
      stateItem.done = true
      stateItem.correct = true
      stateItem.answers = answers

      container
        .find('.step-steps')
        .find(`.step-step[data-id='${stepId}']`)
        .replaceWith(
          createStepElement(step, answerHandler, stepsState.find(s => s.id === step.id))
        )

      const nextStepId = processNextActiveStep(stepsState)

      if (!nextStepId) {

        return
      }

      container
        .find('.step-steps')
        .find(`.step-step[data-id='${nextStepId}']`)
        .replaceWith(
          createStepElement(payload.steps.find(s => s.id === nextStepId), answerHandler, stepsState.find(s => s.id === nextStepId))
        )
      return
    }

    if (step.isAnswerOptions && answers.length !== correctAnswersNumber) {
      return
    }

    stateItem.active = true
    stateItem.done = true
    stateItem.correct = false
    stateItem.answers = answers

    container
      .find('.step-steps')
      .find(`.step-step[data-id='${stepId}']`)
      .replaceWith(
        createStepElement(step, answerHandler, stepsState.find(s => s.id === step.id))
      )
  }

  payload.steps.map(step => {
    container.find('.step-steps')
      .append(
        createStepElement(step, (id, values) => {
          answerHandler(step.id, values)
        }, stepsState.find(s => s.id === step.id))
      )
  })

  container.find('.step-step').removeClass('step-active')

  const activeStepId = processNextActiveStep(stepsState)

  const stepElement = container.find('.step-steps').find(`.step-step[data-id='${activeStepId}']`)
  stepElement.addClass('step-active')

  return [
    () => stepsState.filter(s => s.correct).map(s => question.storyTestAnswers.find(a => a.region_id === s.id).id).flat(),
    () => stepsState.reduce((total, current) => total && current.correct, true),
  ]
}

_extends(StepQuestion, {
  pluginName: 'stepQuestion'
});

export default StepQuestion
