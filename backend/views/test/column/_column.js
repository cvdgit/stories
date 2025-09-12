(function() {

  function calcResult(first, second, sign) {
    console.log(first, second, sign)
    if (sign === '+') {
      return first + second
    }
    if (sign === '-') {
      return first - second
    }
    if (sign === '*') {
      return first * second
    }
  }

  $('.sign').on('change', e => {
    $('.result').val(calcResult(parseInt($('.firstDigit').val()), parseInt($('.secondDigit').val()), $(e.target).val()))
  })

  $('.firstDigit').on('input', e => {
    $('.result').val(calcResult(parseInt(e.target.value), parseInt($('.secondDigit').val()), $('.sign').val()))
  })

  $('.secondDigit').on('input', e => {
    $('.result').val(calcResult(parseInt($('.firstDigit').val()), parseInt(e.target.value), $('.sign').val()))
  })

  function multiplyColumnSteps(a, b) {
    // Преобразуем числа в строки и массивы цифр (с конца)
    let num1 = a.toString();
    let num2 = b.toString();
    let n1 = num1.length;
    let n2 = num2.length;
    let partials = [];
    let steps = [];

    // Перемножаем поразрядно (num2 - множитель, num1 - множимое)
    for (let i = n2 - 1; i >= 0; i--) {
      let digit2 = +num2[i];
      let carry = 0;
      let partial = '';
      let partialInt = 0
      for (let j = n1 - 1; j >= 0; j--) {
        let digit1 = +num1[j];
        let mul = digit1 * digit2 + carry;
        carry = Math.floor(mul / 10);
        partial = (mul % 10) + partial;
      }
      if (carry > 0) partial = carry + partial;

      partialInt = Number(partial)

      // Добавляем нули справа (сдвиг)
      partial += '0'.repeat(n2 - 1 - i);
      partials.push(partial);
      //steps.push(`Этап ${n2 - i}: ${num1} × ${digit2} = ${partial}`);
      steps.push({
        step: n2 - i,
        firstDigit: Number(num1),
        secondDigit: Number(digit2),
        result: Number(partial),
        resultInt: partialInt
      });
    }

    // Выравниваем длины для красивого вывода
    let maxLen = Math.max(num1.length, num2.length + 1, ...partials.map(p => p.length));
    let pad = s => s.padStart(maxLen, ' ');

    // Суммируем все частичные произведения
    let sum = '0';
    for (let p of partials) {
      sum = addStrings(sum, p);
    }

    // Формируем вывод
    /*let output = [];
    output.push(pad(num1));
    output.push('×' + pad(num2));
    output.push('-'.repeat(maxLen + 1));
    for (let i = 0; i < partials.length; i++) {
      output.push(pad(partials[i]));
    }
    if (partials.length > 1) {
      output.push('-'.repeat(maxLen + 1));
      output.push(pad(sum));
    }
    output.push('\nПошаговые этапы:');
    output.push(...steps);

    return output.join('\n');*/
    return steps
  }

  // Вспомогательная функция для сложения длинных чисел в строках
  function addStrings(num1, num2) {
    let res = '';
    let carry = 0;
    let i = num1.length - 1, j = num2.length - 1;
    while (i >= 0 || j >= 0 || carry) {
      let n1 = i >= 0 ? +num1[i] : 0;
      let n2 = j >= 0 ? +num2[j] : 0;
      let sum = n1 + n2 + carry;
      carry = Math.floor(sum / 10);
      res = (sum % 10) + res;
      i--; j--;
    }
    return res.replace(/^0+/, '') || '0';
  }

  attachBeforeSubmit(document.getElementById('column-question-form'), (form) => {
    const formData = new FormData(form)

    /*const sign = $('.sign').val()
    if (sign === '*') {
      const payload = multiplyColumnSteps(Number($('.firstDigit').val()), Number($('.secondDigit').val()))
      formData.append(`${$(form).attr('data-model-name')}[payload]`, JSON.stringify(payload))
    }*/

    sendForm($(form).attr('action'), $(form).attr('method'), formData)
      .done((response) => {
        if (response.url) {
          location.replace(response.url);
        }
        else {
          toastr.success('Успешно');
          location.reload()
        }
      })
  })
})()
