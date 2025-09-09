(function() {

  function calcResult(first, second, sign) {
    console.log(first, second, sign)
    if (sign === '+') {
      return first + second
    }
    if (sign === '-') {
      return first - second
    }
  }

  $('.firstDigit').on('input', e => {
    $('.result').val(calcResult(parseInt(e.target.value), parseInt($('.secondDigit').val()), $('.sign').val()))
  })

  $('.secondDigit').on('input', e => {
    $('.result').val(calcResult(parseInt($('.firstDigit').val()), parseInt(e.target.value), $('.sign').val()))
  })

  attachBeforeSubmit(document.getElementById('column-question-form'), (form) => {

    const formData = new FormData(form)

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
