import {_extends} from "../common";
import "./ColumnQuestion.css"

function ColumnQuestion(test) {
  this.element = null;
  this.container = test.container;
  this.deck = test.getDeck();
}

ColumnQuestion.prototype.createWrapper = function (question) {
  return $(`<div class="step-question-wrap" style="display: flex; flex-wrap: wrap; width: 100%; height: 100%; box-sizing: content-box; justify-content: center"></div>`)
};

ColumnQuestion.prototype.getContent = function(question) {
  const wrap = this.createWrapper(question)

  const {payload} = question
  const {sign, result, steps} = payload
  const firstDigit = String(payload.firstDigit)
  const secondDigit = String(payload.secondDigit)
  const digits = Math.max(String(firstDigit).length, String(secondDigit).length, String(result).length)

  const template = createTemplate()

  if (sign === '-') {
    minusTemplate(template)
  }

  if (sign === '+') {
    plusTemplate(template)
  }

  if (sign === '*') {
    multiplyTemplate(template, {steps, digits, firstDigit, secondDigit, showValue: true, result})
  }

  if (sign === '*') {
    for (let i = digits - 1; i >= 0; i--) {
      template.querySelector('.helperRow')
        .appendChild(createHelperCell(i === 0))
      template.querySelector('.firstDigitRow')
        .appendChild(createDigitCell(firstDigit[firstDigit.length - 1 - i]))
      template.querySelector('.secondDigitRow')
        .appendChild(createDigitCell(secondDigit[secondDigit.length - 1 - i]))
    }
  } else {
    console.log('incorrect')
    for (let i = digits; i > 0; i--) {
      if (firstDigit[firstDigit.length - i]) {
        template.querySelector('.helperRow')
          .appendChild(createHelperCell(i === 0))
        template.querySelector('.firstDigitRow')
          .appendChild(createDigitCell(firstDigit[firstDigit.length - i], false, true))
      }
      if (secondDigit[secondDigit.length - i]) {
        template.querySelector('.secondDigitRow')
          .appendChild(createDigitCell(secondDigit[secondDigit.length - i], false, true))
      }
      template.querySelector('.resultRow')
        .appendChild(createResultCell(result[result.length - i], true))
    }
  }

  wrap.append(template)

  return wrap
}

function createHelpersRow(cellsNumber, offset) {
  const el = document.createElement('div')
  el.classList.add('calculationRow')
  offset = offset || 0
  for (let i = cellsNumber + offset; i > 0; i--) {
    el.appendChild(createHelperCell(i <= offset))
  }
  return el
}

function createDigitsRow(cellsNumber, {offset, rowExtraClassName, data, value}) {
  const el = document.createElement('div')
  el.classList.add('calculationRow')
  if (rowExtraClassName) {
    el.classList.add(...(Array.isArray(rowExtraClassName) ? rowExtraClassName : [rowExtraClassName]))
  }
  if (data) {
    for (let key in data) {
      el.setAttribute(`data-${key}`, data[key])
    }
  }
  offset = offset || 0
  for (let i = cellsNumber + offset; i > 0; i--) {
    let val = value ? value[value.length + offset - i] : null
    el.appendChild(createDigitCell(val, i <= offset, false))
  }
  return el
}

function createResultsRow(cellsNumber, {value, showValue}) {
  const el = document.createElement('div')
  el.classList.add('calculationRow', 'resultRow')
  for (let i = cellsNumber; i > 0; i--) {
    let val = value ? value[value.length - i] : null
    el.appendChild(createResultCell(showValue ? val : null, showValue))
  }
  return el
}

function createHelperCell(offset) {
  offset = offset || false
  const el = document.createElement('input')
  el.type = 'text'
  el.maxLength = 2
  el.classList.add('calculationRow__cell')
  if (offset) {
    el.maxLength = 1
    el.classList.add('calculationRow__cell_offset')
  } else {
    el.classList.add('calculationRow__cell_helper')
  }
  return el
}

function createDigitCell(value, offsetCell, readonly) {
  const el = document.createElement('input')
  el.maxLength = 1
  el.classList.add('calculationRow__cell')
  el.value = value || ''
  offsetCell = offsetCell || false
  if (offsetCell) {
    el.classList.add('calculationRow__cell_offset')
    el.disabled = true
  }
  el.readOnly = readonly || false
  return el
}

function createResultCell(value, readonly) {
  const el = document.createElement('input')
  el.maxLength = 1
  el.classList.add('calculationRow__cell', 'calculationRow__cell_result')
  el.readOnly = readonly || false
  if (value) {
    el.value = value
  }
  return el
}

function createTemplate() {
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
  return template
}

function minusTemplate(template) {
  template.querySelector('.template').classList.add('template_subtraction')
  template.querySelector('.template__symbol').innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M24 11.75C24 12.8546 23.1046 13.75 22 13.75H2C0.895431 13.75 0 12.8546 0 11.75V11.75C0 10.6454 0.89543 9.75 2 9.75H22C23.1046 9.75 24 10.6454 24 11.75V11.75Z" fill="#F09235"></path></svg>
    `
}

function plusTemplate(template) {
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

function multiplyTemplate(template, {steps, digits, firstDigit, secondDigit, showValue, result}) {
  template.querySelector('.template__symbol').innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><g clip-path="url(#clip0_142_4)"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.5564 16.2129C11.3374 15.4319 12.6037 15.4319 13.3848 16.2129L17.6276 20.4557C18.4086 21.2368 19.675 21.2368 20.456 20.4557V20.4557C21.2371 19.6747 21.2371 18.4083 20.456 17.6273L16.2132 13.3845C15.4322 12.6034 15.4322 11.3371 16.2132 10.5561L20.4557 6.31358C21.2368 5.53254 21.2368 4.26621 20.4557 3.48516V3.48516C19.6747 2.70411 18.4083 2.70411 17.6273 3.48516L13.3848 7.72764C12.6037 8.50869 11.3374 8.50869 10.5564 7.72764L6.31387 3.48514C5.53282 2.70409 4.26649 2.70409 3.48544 3.48514V3.48514C2.70439 4.26619 2.70439 5.53252 3.48544 6.31357L7.72794 10.5561C8.50899 11.3371 8.50899 12.6034 7.72794 13.3845L3.48514 17.6273C2.70409 18.4083 2.70409 19.6747 3.48514 20.4557V20.4557C4.26619 21.2368 5.53252 21.2368 6.31357 20.4557L10.5564 16.2129Z" fill="#F09235"></path></g><defs><clipPath id="clip0_142_4"><rect width="24" height="24" fill="white"></rect></clipPath></defs></svg>
    `
  const el = document.createElement('div')
  el.classList.add('template__calculation')
  showValue = showValue || false

  if (steps.length === 0) {
    template.querySelector('.secondDigitRow').classList.remove('calculationRow_last')
    const wrap = document.createElement('div')
    wrap.classList.add('calculationRow_first')
    wrap.appendChild(createHelpersRow(firstDigit.length + secondDigit.length, 1))
    wrap.appendChild(createResultsRow(firstDigit.length + secondDigit.length + 1, {
      value: result,
      showValue: Boolean(showValue)
    }))
    el.appendChild(wrap)
  } else {
    steps.map((step, i) => {
      const wrap = document.createElement('div')
      if (i === steps.length - 1) {
        wrap.appendChild(createHelpersRow(digits, i + 1))
        wrap.appendChild(createDigitsRow(digits + 1, {
          offset: i,
          rowExtraClassName: 'calculationRow_last',
          data: {step: step.step},
          value: showValue ? String(step.resultInt) : null
        }))
        wrap.appendChild(createHelpersRow(firstDigit.length + secondDigit.length, 1))
        wrap.appendChild(createResultsRow(firstDigit.length + secondDigit.length + 1, {
          value: result,
          showValue: Boolean(showValue)
        }))
      } else {
        wrap.appendChild(createHelpersRow(digits, i + 1))
        wrap.appendChild(createDigitsRow(digits + 1, {
          offset: i,
          data: {step: step.step},
          value: showValue ? String(step.resultInt) : null
        }))
        if (i === 0) {
          const symbolEl = document.createElement('div')
          symbolEl.classList.add('template__symbol', 'template__symbol_plus'/*, 'template__symbol_plus-helper'*/)
          symbolEl.innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.25 13.75C9.35457 13.75 10.25 14.6454 10.25 15.75V22C10.25 23.1046 11.1454 24 12.25 24C13.3546 24 14.25 23.1046 14.25 22V15.75C14.25 14.6454 15.1454 13.75 16.25 13.75H22C23.1046 13.75 24 12.8546 24 11.75C24 10.6454 23.1046 9.75 22 9.75H16.25C15.1454 9.75 14.25 8.85457 14.25 7.75V2C14.25 0.895431 13.3546 0 12.25 0C11.1454 0 10.25 0.89543 10.25 2V7.75C10.25 8.85457 9.35457 9.75 8.25 9.75H2C0.89543 9.75 0 10.6454 0 11.75C0 12.8546 0.89543 13.75 2 13.75H8.25Z" fill="#F09235"></path></svg>
          `
          wrap.appendChild(symbolEl)
        }
      }
      el.appendChild(wrap)
    })
  }

  template.querySelector('.template').appendChild(el)
}

ColumnQuestion.prototype.create = function(question, container, responseHandler) {
  const {payload} = question;

  container.empty()

  const {sign, result, steps} = payload
  const firstDigit = String(payload.firstDigit)
  const secondDigit = String(payload.secondDigit)
  const digits = Math.max(String(firstDigit).length, String(secondDigit).length)

  const template = createTemplate()

  if (sign === '+') {
    plusTemplate(template)
  }

  if (sign === '-') {
    minusTemplate(template)
  }

  if (sign === '*') {
    multiplyTemplate(template, {steps, digits, firstDigit, secondDigit, result})
  }

  if (sign === '*') {
    for (let i = digits - 1; i >= 0; i--) {
      template.querySelector('.helperRow')
        .appendChild(createHelperCell(i === 0))
      template.querySelector('.firstDigitRow')
        .appendChild(createDigitCell(firstDigit[firstDigit.length - 1 - i]))
      template.querySelector('.secondDigitRow')
        .appendChild(createDigitCell(secondDigit[secondDigit.length - 1 - i]))
    }
  } else {
    for (let i = digits - 1; i >= 0; i--) {
      template.querySelector('.helperRow')
        .appendChild(createHelperCell(i === 0))
      template.querySelector('.firstDigitRow')
        .appendChild(createDigitCell(firstDigit[firstDigit.length - 1 - i], false, true))
      template.querySelector('.secondDigitRow')
        .appendChild(createDigitCell(secondDigit[secondDigit.length - 1 - i], false, true))
      template.querySelector('.resultRow')
        .appendChild(createResultCell())
    }
  }

  container.append(template)

  return [
    () => $('.resultRow', template)
      .find('.calculationRow__cell_result')
      .map((i, el) => $(el).val())
      .get()
      .filter(v => v !== '')
      .join(''),
    () => $('[data-step]', template)
      .map((i, stepEl) => ({
        step: $(stepEl).attr('data-step'),
        result: $(stepEl)
          .find('.calculationRow__cell')
          .map((i, el) => $(el).val())
          .get()
          .filter(v => v !== '')
          .join('')
      })).get(),
    (userAnswer) => Number(userAnswer) === Number(result)
  ];
}

_extends(ColumnQuestion, {
  pluginName: 'columnQuestion'
});

export default ColumnQuestion
