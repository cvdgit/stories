(function() {
  async function sendAuth(id) {
    const response = await fetch(`/getcourse/auth`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      },
      body: JSON.stringify({
        id
      }),
    })
    if (!response.ok) {
      throw new Error('Auth send error')
    }
    return await response.json()
  }

  function createMessage(message) {
    const elem = document.createElement('div')
    elem.innerHTML = `
      <h1 class="h2" style="margin: 0">${message}</h1>
    `
    return elem
  }

  const id = window?.getCourseUserId

  sendAuth(id).then(r => {

    if (!r?.success) {
      $('#loader').hide()
      $('#wrap')
        .empty()
        .append(createMessage(r?.message  || 'Произошла ошибка. Попробуйте повторить позднее'))
      return
    }

    const mentalMapId = window?.mentalMapId
    const quizId = window?.quizId

    $('#loader').hide()

    if (mentalMapId) {
      const elem = document.createElement('div')
      elem.dataset.mentalMapId = mentalMapId
      elem.classList.add('mental-map')
      $('#wrap')
        .empty()
        .append(elem)
      initMentalMap(elem)
    }

    if (quizId) {

      const elemWrap = document.createElement('div')
      elemWrap.style.padding = '0'
      elemWrap.style.textAlign = 'center'
      elemWrap.style.height = '100%'
      elemWrap.style.width = '100%'

      const elem = document.createElement('div')
      elem.dataset.testId = quizId
      elem.dataset.studentId = r.student_id
      elem.classList.add('new-questions')

      elemWrap.appendChild(elem)

      $('#wrap')
        .empty()
        .append(elemWrap)

      initQuiz(elem)
    }
  })

  function initMentalMap(elem) {

    const mentalMapBuilder = window.mentalMapBuilder = new MentalMapManagerQuiz();

    const mentalMap = mentalMapBuilder.create(elem, undefined, {
      init: async () => {
        const response = await fetch(`/mental-map/init`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
          },
          body: JSON.stringify({
            id: window.mentalMapId
          })
        })

        if (!response.ok) {
          throw new Error(response.statusText)
        }

        const json = await response.json()
        if (!json.success) {
          throw new Error(json?.message || 'Error')
        }

        return {mentalMap: json.mentalMap, history: json.history}
      },
      ...{
        getCourseMode: true
      },
      ...elem.dataset
    })
    mentalMap.run()
  }

  function initQuiz(elem) {
    const test = WikidsStoryTest.create(elem, {
      dataUrl: '/question/get',
      dataParams: {...elem.dataset},
      forSlide: false,
      //repetitionMode: false,
      init: () => $.getJSON('/question/init', {...elem.dataset}),
      onInitialized: () => test.addEventListener('finish', event => {
        const {testID, _, studentId} = event;
        const formData = new FormData();
        formData.append('test_id', testID);
        formData.append('student_id', studentId);
        /*sendForm(formData, '/repetition/testing/finish', 'post')
          .done(response => {
            console.log(response);
          });*/
      })
    });
    test.run();
  }
})();
