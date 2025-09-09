import {_extends} from "../common";
import "./ColumnQuestion.css"

function ColumnQuestion(test) {
  this.element = null;
  this.container = test.container;
  this.deck = test.getDeck();
}

ColumnQuestion.prototype.createWrapper = function (question) {
  return $(`<div class="step-question-wrap" style="display: flex; flex-wrap: wrap; width: 100%; box-sizing: content-box; justify-content: center"></div>`)
};

ColumnQuestion.prototype.getContent = function(question) {
  const wrap = document.createElement('div')
  wrap.style.display = 'flex'
  wrap.style.justifyContent = 'space-between'
  wrap.style.flexDirection = 'column'
  return wrap
}

ColumnQuestion.prototype.create = function(question, container, responseHandler) {
  const {payload} = question;

  container.empty()

  const {firstDigit, secondDigit, sign, result} = payload
  const digits = Math.max(firstDigit.length, secondDigit.length)

  const template = document.createElement('div')
  template.classList.add('template__container', 'template__container_small')
  template.style.justifyContent = 'center'
  template.innerHTML = `<div class="template">
    <div class="template__calculation">
        <div>
            <div class="calculationRow helperRow"></div>
            <div class="calculationRow firstDigitRow"></div>
            <div class="template__symbol"></div>
        </div>
        <div>
            <div class="calculationRow calculationRow_last secondDigitRow"></div>
            <div class="calculationRow resultRow"></div>
        </div>
    </div>
</div>
`

  if (sign === '+') {
    template.querySelector('.template__symbol').classList.add('template__symbol_plus')
    template.querySelector('.template__symbol').innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M8.25 13.75C9.35457 13.75 10.25 14.6454 10.25 15.75V22C10.25 23.1046 11.1454 24 12.25 24C13.3546 24 14.25 23.1046 14.25 22V15.75C14.25 14.6454 15.1454 13.75 16.25 13.75H22C23.1046 13.75 24 12.8546 24 11.75C24 10.6454 23.1046 9.75 22 9.75H16.25C15.1454 9.75 14.25 8.85457 14.25 7.75V2C14.25 0.895431 13.3546 0 12.25 0C11.1454 0 10.25 0.89543 10.25 2V7.75C10.25 8.85457 9.35457 9.75 8.25 9.75H2C0.89543 9.75 0 10.6454 0 11.75C0 12.8546 0.89543 13.75 2 13.75H8.25Z"
                      fill="#F09235"></path>
            </svg>
    `
    template.querySelector('.resultRow').innerHTML = `<input type="text" class="calculationRow__cell calculationRow__cell_result" maxlength="1">`
  }

  if (sign === '-') {
    template.querySelector('.template').classList.add('template_subtraction')
    template.querySelector('.template__symbol').innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M24 11.75C24 12.8546 23.1046 13.75 22 13.75H2C0.895431 13.75 0 12.8546 0 11.75V11.75C0 10.6454 0.89543 9.75 2 9.75H22C23.1046 9.75 24 10.6454 24 11.75V11.75Z" fill="#F09235"></path></svg>
    `
  }

  for (let i = digits - 1; i >= 0; i--) {

    const el = document.createElement('input')
    el.type = 'text'
    el.maxLength = 2
    el.classList.add('calculationRow__cell')
    if (i === 0) {
      el.maxLength = 1
      el.classList.add('calculationRow__cell_offset')
    } else {
      el.classList.add('calculationRow__cell_helper')
    }
    template.querySelector('.helperRow').appendChild(el)

    if (firstDigit[digits - 1 - i]) {
      const firstDigitEl = document.createElement('input')
      firstDigitEl.maxLength = 1
      firstDigitEl.classList.add('calculationRow__cell')
      firstDigitEl.value = firstDigit[digits - 1 - i]
      firstDigitEl.readOnly = true
      template.querySelector('.firstDigitRow').appendChild(firstDigitEl)
    } else {
      const firstDigitEl = document.createElement('input')
      firstDigitEl.maxLength = 1
      firstDigitEl.classList.add('calculationRow__cell')
      firstDigitEl.readOnly = true
      template.querySelector('.firstDigitRow').prepend(firstDigitEl)
    }

    if (secondDigit[digits - 1 - i]) {
      const secondDigitEl = document.createElement('input')
      secondDigitEl.maxLength = 1
      secondDigitEl.classList.add('calculationRow__cell')
      secondDigitEl.value = secondDigit[digits - 1 - i]
      secondDigitEl.readOnly = true
      template.querySelector('.secondDigitRow').appendChild(secondDigitEl)
    } else {
      const secondDigitEl = document.createElement('input')
      secondDigitEl.maxLength = 1
      secondDigitEl.classList.add('calculationRow__cell')
      secondDigitEl.readOnly = true
      template.querySelector('.secondDigitRow').prepend(secondDigitEl)
    }

    const resultEl = document.createElement('input')
    resultEl.maxLength = 1
    resultEl.classList.add('calculationRow__cell', 'calculationRow__cell_result')
    template.querySelector('.resultRow').appendChild(resultEl)
  }

  container.append(template)

  return [
    () => $('.resultRow', template)
      .find('.calculationRow__cell_result')
      .map((i, el) => $(el).val())
      .get()
      .filter(v => v !== '')
      .join(''),
    (userAnswer) => Number(userAnswer) === Number(result)
  ];
}

_extends(ColumnQuestion, {
  pluginName: 'columnQuestion'
});

export default ColumnQuestion
