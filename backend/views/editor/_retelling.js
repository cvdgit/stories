(function () {

  async function sendMessage(payload, onMessage, onEndCallback) {
    let accumulatedMessage = ''
    return sendEventSourceMessage({
      url: '/admin/index.php?r=gpt/stream/retelling-answers',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'text/event-stream',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify(payload),
      onMessage: (streamedResponse) => {
        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }
        onMessage(accumulatedMessage)
      },
      onError: (streamedResponse) => {
        console.log(streamedResponse)
      },
      onEnd: onEndCallback
    })
  }

  async function sendRequest(url, method, headers, payload, formData) {
    let body = null
    if (payload) {
      body = JSON.stringify(payload)
    }
    if (formData) {
      body = formData
    }
    headers = headers || {}
    const response = await fetch(url, {
      method,
      cache: 'no-cache',
      headers: {
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content'),
        ...headers
      },
      body
    })

    if (!response.ok) {
      throw new Error('error - ' + response.statusText)
    }

    return await response.json()
  }

  async function sendForm(url, formData) {
    return await sendRequest(url, 'POST', {}, undefined, formData)
  }

  window.EditorRetelling = function () {

    const $modal = $('#retelling-modal')

    function resetModal() {
      $modal.find('#retelling-slide-text').html('')
      $modal.find('#retelling-with-questions').prop('checked', false)
      $modal.find('#retelling-questions-generate').css('display', 'none')
      $modal.find('#retelling-questions').html('')
      $modal.find('#retelling-required').prop('checked', true)
    }

    $modal.find('#retelling-questions-generate').on('click', e => {
      e.preventDefault()

      const text = $modal.find('#retelling-slide-text').text()
      if (!text) {
        return
      }
      sendMessage({
          slideTexts: text
        }, message => {
          $modal.find('#retelling-questions').html(message)
        },
        () => console.log('end'))
    })

    $modal.find('#retelling-with-questions').on('click', e => {
      $modal.find('#retelling-questions-generate').toggle()
    })

    /**
     * @param {int} storyId
     * @param {int} slideId
     * @param {boolean} withQuestions
     * @param {string} questions
     * @param {boolean} required
     */
    async function createHandler(storyId, slideId, withQuestions, questions, required) {
      const formData = new FormData()
      formData.append('story_id', storyId.toString())
      formData.append('slide_id', slideId.toString())
      formData.append('with_questions', withQuestions ? 1 : 0)
      formData.append('questions', questions)
      formData.append('required', required ? 1 : 0)
      const response = await fetch(`/admin/index.php?r=editor/retelling&current_slide_id=${slideId}&story_id=${storyId}`, {
        method: 'POST',
        cache: 'no-cache',
        headers: {
          'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
        },
        body: formData
      })

      if (!response.ok) {
        throw new Error('error - ' + response.statusText)
      }

      return await response.json()
    }

    async function updateHandler(retellingId, slideId, blockId, withQuestions, questions, required) {
      const formData = new FormData()
      formData.append('with_questions', withQuestions ? '1' : '0')
      formData.append('questions', questions)
      formData.append('required', required ? '1' : '0')
      return await sendForm(`/admin/index.php?r=editor/update-retelling&retelling_id=${retellingId}&slide_id=${slideId}&block_id=${blockId}`, formData)
    }

    this.showModal = ({storyId, slideId, texts}) => {

      $('.modal-title', $modal).text('Новый пересказ')

      resetModal()

      const $loader = $modal.find('#retelling-loader')
      $loader.css('display', 'none')

      $modal.find('#retelling-slide-text').html(texts)

      $modal.find('#retelling-action')
        .text('Создать')
        .off('click')
        .on('click', async e => {

          const withQuestions = $modal.find('#retelling-with-questions').is(':checked')
          const required = $modal.find('#retelling-required').is(':checked')

          let questions = ''
          if (withQuestions) {
            questions = $modal
              .find('#retelling-questions')
              .text()
              .replace(/```\n?|```/g, '')
              .trim()
          }

          if (withQuestions && !questions.length) {
            alert('Нет вопросов')
            return
          }

          $loader.css('display', 'flex')

          const json = await createHandler(storyId, slideId, withQuestions, questions, required)

          $modal.modal('hide')
          StoryEditor.loadSlides(json?.slide_id)
        })

      $modal.modal('show')
    }

    this.showUpdateModal = ({storyId, slideId, blockId}) => {
      $('.modal-title', $modal).text('Изменить пересказ')
      resetModal()

      const $loader = $modal.find('#retelling-loader')
      $loader.css('display', 'none')

      $modal
        .find('#retelling-action')
        .text('Сохранить')
        .off('click')

      $modal
        .off('show.bs.modal')
        .on('show.bs.modal', async (e) => {
        const {storyId, slideId, blockId} = e.relatedTarget
        const response = await fetch(`/admin/index.php?r=editor/load-retelling&story_id=${storyId}&slide_id=${slideId}&block_id=${blockId}`, {
          method: 'get',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
          },
        })
        if (!response.ok) {
          throw new Error('error')
        }
        const json = await response.json()
        const {texts, withQuestions, questions, required, retellingId} = json?.retelling || {}

        $modal.find('#retelling-slide-text').html(texts)
        $modal.find('#retelling-with-questions').prop('checked', withQuestions)
        if (withQuestions) {
          $modal.find('#retelling-questions-generate').toggle()
        }
        $modal.find('#retelling-questions').html(questions)
        $modal.find('#retelling-required').prop('checked', required)

        $modal
          .find('#retelling-action')
          .on('click', async (e) => {

            const withQuestions = $modal.find('#retelling-with-questions').is(':checked')
            const required = $modal.find('#retelling-required').is(':checked')

            let questions = ''
            if (withQuestions) {
              questions = $modal
                .find('#retelling-questions')
                .text()
                .replace(/```\n?|```/g, '')
                .trim()
            }

            if (withQuestions && !questions.length) {
              alert('Нет вопросов')
              return
            }

            $loader.css('display', 'flex')

            let json
            try {
              json = await updateHandler(retellingId, slideId, blockId, withQuestions, questions, required)
            } catch (ex) {
              toastr.error(ex.message)
              $loader.css('display', 'none')
              return
            }

            if (!json.success) {
              toastr.error(json?.message || 'Ошибка')
              $loader.css('display', 'none')
              return
            }

            StoryEditor.updateSlideBlock(json.block_id, json.html);
            toastr.success('Блок успешно изменен');

            $modal.modal('hide')
          })
      })

      $modal.modal('show', {storyId, slideId, blockId})
    }
  }
})()
