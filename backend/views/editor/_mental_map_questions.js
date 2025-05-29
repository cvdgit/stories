function MentalMapQuestions() {

  const $modal = $('#mental-map-questions-modal')
  let mentalMapId

  async function fetchFragments(id) {
    const response = await fetch(`/admin/index.php?r=editor/mental-map/fragments&id=${id}`, {
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      }
    })
    if (!response.ok) {
      throw new Error('Fetch fragments error')
    }
    return await response.json()
  }

  async function fetchSourceFragments({slideId, blockId, mentalMapId}) {
    const response = await fetch(`/admin/index.php?r=editor/mental-map/source-fragments&slide_id=${slideId}&block_id=${blockId}&mental_map_id=${mentalMapId}`, {
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      }
    })
    if (!response.ok) {
      throw new Error('Fetch fragments error')
    }
    return await response.json()
  }

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

  const $list = $modal.find('#mental-map-questions-items')

  function processRow(el) {
    const text = el.find('.fragment-text').text().trim()
    if (!text) {
      return
    }

    el.find('.gen-img').hide()
    el.find('.gen-loading').show()
    $('<div/>', {class: 'gen-fragment-backdrop'}).appendTo(el.find('.gen-fragment-questions-wrap'));

    sendMessage({
        slideTexts: text
      }, message => {
        el.find('.gen-fragment-questions-text').html(message)
      },
      () => {
        el.find('.gen-fragment-backdrop').remove()
        el.find('.gen-img').show()
        el.find('.gen-loading').hide()
      })
  }

  $list.on('click', '.gen-fragment-questions', e => {
    e.preventDefault()
    const el = $(e.target).parents('.questions-fragment-row')
    if (el.find('.gen-loading').is(':visible')) {
      return
    }
    processRow(el)
  })

  $modal.on('show.bs.modal', () => {

  });

  $modal.find('#mental-map-questions-generate').on('click', e => {
    e.preventDefault()
    $list.find('.questions-fragment-row').each((i, row) => {
      processRow($(row))
    })
  })

  /**
   * @param {int} storyId
   * @param {int} slideId
   * @param {string} sourceMentalMapId
   * @param {array} fragments
   * @param {boolean} required
   */
  async function createHandler(storyId, slideId, sourceMentalMapId, fragments, required) {
    const response = await fetch(`/admin/index.php?r=editor/mental-map-questions&current_slide_id=${slideId}&story_id=${storyId}`, {
      method: 'POST',
      cache: 'no-cache',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      },
      body: JSON.stringify({
        mentalMapId: sourceMentalMapId,
        fragments,
        required
      })
    })

    if (!response.ok) {
      throw new Error('error - ' + response.statusText)
    }

    return await response.json()
  }

  async function updateHandler({slideId, blockId, mentalMapId, fragments, required}) {
    const response = await fetch(`/admin/index.php?r=editor/mental-map/update-questions`, {
      method: 'POST',
      cache: 'no-cache',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      },
      body: JSON.stringify({
        slideId,
        blockId,
        mentalMapId,
        fragments,
        required
      })
    })

    if (!response.ok) {
      throw new Error('error - ' + response.statusText)
    }

    return await response.json()
  }

  function createFragmentItem({fragmentId, fragmentText, question}) {
    return $(`<div class="questions-fragment-row" data-fragment-id="${fragmentId}" style="display: flex; column-gap: 20px; flex-direction: row">
<div style="flex: 1; display: flex; flex-direction: row; padding: 4px">
<div class="fragment-text" style="flex: 1;">${fragmentText}</div>
<div>
  <a class="gen-fragment-questions" href="" style="width: 32px; height: 32px; display: inline-flex">
      <img class="gen-img" style="pointer-events: none" src="/img/chatgpt-icon.png" alt="">
      <img class="gen-loading" style="display: none; pointer-events: none" src="/img/loading.gif" alt="">
  </a>
</div>
</div>
<div class="gen-fragment-questions-wrap" style="flex: 1; position: relative">
<div class="gen-fragment-questions-text" contenteditable="plaintext-only" style="width: 100%; height: 100%; padding: 4px">${question || ''}</div>
</div>
</div>`)
  }

  /**
   * @param {jQuery} list
   */
  function getFragmentsWithQuestions(list) {
    const fragments = []
    list.each((i, row) => {
      const fragmentId = $(row).attr('data-fragment-id')
      const fragment = $(row).find('.fragment-text').text().trim()
      const questions = $(row).find('.gen-fragment-questions-text')
        .text()
        .replace(/```\n?|```/g, '')
        .trim()
      if (fragment && questions) {
        fragments.push({
          fragmentId,
          text: fragment,
          questions
        })
      }
    })
    return fragments
  }

  this.showModal = async ({storyId, slideId, id}) => {

    $('.modal-title', $modal).text('Новая ментальная карта с вопросами')

    mentalMapId = id
    $list.empty()

    const $loader = $modal.find('#mental-map-questions-loader')
    $loader.css('display', 'none')

    const json = await fetchFragments(mentalMapId)
    json.items.map(i => createFragmentItem({fragmentId: i.id, fragmentText: i.text})
      .appendTo($list))

    $modal
      .find('#mental-map-questions-action')
      .text('Создать')
      .off('click')
      .on('click', async e => {
        e.preventDefault()

        const fragments = []
        $list.find('.questions-fragment-row').each((i, row) => {
          const fragmentId = $(row).attr('data-fragment-id')
          const fragment = $(row).find('.fragment-text').text().trim()
          const questions = $(row).find('.gen-fragment-questions-text')
            .text()
            .replace(/```\n?|```/g, '')
            .trim()
          if (fragment && questions) {
            fragments.push({
              fragmentId,
              text: fragment,
              questions
            })
          }
        })

        if (!fragments.length) {
          toastr.info('Нет данных')
          return
        }

        const required = $modal.find('#mental-map-questions-required').is(':checked')

        $loader.css('display', 'flex')

        const json = await createHandler(storyId, slideId, mentalMapId, fragments, required)
        if (!json.success) {
          toastr.error(json.message || 'Произошла ошибка')
          $loader.css('display', 'none')
          return
        }

        $modal.modal('hide')
        StoryEditor.loadSlides(json?.slide_id)
      })

    $modal.modal('show')
  }

  this.showUpdateModal = async ({storyId, slideId, blockId, mentalMapId}) => {
    $('.modal-title', $modal).text('Изменить ментальную карту с вопросами')
    // resetModal()

    $list.empty()

    const $loader = $modal.find('#mental-map-questions-loader')
    $loader.css('display', 'none')

    $modal
      .find('#mental-map-questions-action')
      .text('Сохранить')
      .off('click')

    const response = await fetchSourceFragments({slideId, blockId, mentalMapId})
    const {items, questions, required} = response

    items.map(i => createFragmentItem({
      fragmentId: i.id,
      fragmentText: i.text,
      question: questions.find(q => q.fragmentId === i.id)?.questions
    }).appendTo($list))

    $modal.find('#mental-map-questions-required').prop('checked', required)

    $modal
      .find('#mental-map-questions-action')
      .on('click', async e => {
        e.preventDefault()

        const fragments = getFragmentsWithQuestions($list.find('.questions-fragment-row'))
        if (!fragments.length) {
          toastr.info('Нет данных')
          return
        }

        const required = $modal.find('#mental-map-questions-required').is(':checked')

        $loader.css('display', 'flex')

        let json
        try {
          json = await updateHandler({slideId, blockId, mentalMapId, fragments, required})
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

    $modal
      .off('show.bs.modal')
      .on('show.bs.modal', async (e) => {
        const {storyId, slideId, blockId, mentalMapId} = e.relatedTarget

        /*
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
          */
      })


    $modal.modal('show', {storyId, slideId, blockId, mentalMapId})
  }
}
