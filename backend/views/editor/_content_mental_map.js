
function ContentMentalMap() {

  async function sendMessage(payload, onMessage, onEndCallback) {
    let accumulatedMessage = ''
    return sendEventSourceMessage({
      url: '/admin/index.php?r=gpt/mental-map/text-fragments',
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
      onEnd: () => onEndCallback(accumulatedMessage)
    })
  }

  async function streamMessage(payload, onMessage, onEndCallback) {
    let accumulatedMessage = ''
    return sendEventSourceMessage({
      url: '/admin/index.php?r=gpt/story/speech-trainer-sentences',
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
      onEnd: () => onEndCallback(accumulatedMessage)
    })
  }

  function wordClickHandler(word, prevState, ctrlKey) {
    const state = [...prevState]
      .map((w, j) => {
        if (ctrlKey) {
          if (w.id === word.id) {
            let targetWord
            const prevWord = prevState[j - 1]
            if (prevWord && prevWord.hidden) {
              targetWord = prevWord
              targetWord.word += ` ${w.word}`
              targetWord.merge = true
              w.word = ''
            }
            const nextWord = prevState[j + 1]
            if (nextWord && nextWord.hidden) {
              if (!targetWord) {
                targetWord = w
              }
              targetWord.word += ` ${nextWord.word}`
              targetWord.merge = true
              targetWord.hidden = !w.hidden
              nextWord.word = ''
            }
          }
          return w
        }
        if (w.id === word.id) {
          w.hidden = !w.hidden
          w.merge = false
        }
        return w
      })
      .filter(w => w.type === 'break' || (w.type === 'word' && w.word.trim().length > 0))

    state
      .filter((w, wordIndex) => {
        const filtered = w.type === 'word' && !w.hidden && w.word.split(' ').length > 1
        if (filtered) {
          w.index = wordIndex
        }
        return filtered
      })
      .map(w => {
        w.word.trim().split(' ').map((splitWord, splitIndex) => {
          if (splitIndex === 0) {
            w.word = splitWord
            return
          }
          state.splice(w.index + splitIndex, 0, {...w, id: uuidv4(), word: splitWord})
        })
      })

    return state
  }

  function drawWords(containerElem, words) {
    containerElem.empty()
    words.map((word, i) => {
      const {type} = word
      if (type === 'break') {
        containerElem.append('<div className="line-sep"></div>')
        return
      }
      const $span = $(`<span class="text-item-word ${word.hidden ? 'selected' : ''}"/>`)
      $span.attr('data-word-id', word.id)
      $span.html(word.word)
      containerElem.append($span)
    })
  }

  function createFragmentElement(fragment, index) {
    const $fragmentElem = $(`<div class="fragment-item" data-fragment-id="${fragment.id}">` +
      '<div class="fragment-item-header">' +
      '<div class="fragment-item-number"></div>' +
      '<div class="fragment-item-controls"><a class="fragment-edit" href="">Редактировать</a><a class="fragment-save" href="">Сохранить</a><a class="fragment-delete" href="">Удалить</a></div>' +
      '</div>' +
      '<div class="fragment-item-words"></div>' +
      '</div>')

    $fragmentElem.find('.fragment-item-number').text(String(index) + '.')
    drawWords($fragmentElem.find('.fragment-item-words'), fragment.words)

    $fragmentElem.on('click', '.text-item-word', ({target, ctrlKey}) => {
      const wordId = target.getAttribute('data-word-id')
      const word = fragment.words.find(w => w.id === wordId)
      fragment.words = wordClickHandler(word, fragment.words, ctrlKey)
      drawWords($fragmentElem.find('.fragment-item-words'), fragment.words)
    })

    return $fragmentElem
  }

  function drawFragments($contentItemList, fragmentsManager) {
    $contentItemList.empty()
    $contentItemList.append('<div style="font-weight: 500; font-size: 18px">Фрагменты:<div/>')
    $contentItemList.append(`<div class="fragment-item" style="border: 0 none"><div class="fragment-create"><button type="button" class="fragment-create-btn">+</button></div></div>`)
    fragmentsManager.getFragments().map((fragment, i) =>  {
      const $fragmentElem = createFragmentElement(fragment, i + 1)
      $contentItemList.append($fragmentElem)
      $fragmentElem.append(`<div class="fragment-create"><button type="button" class="fragment-create-btn">+</button></div>`)
    })
  }

  function FragmentsManager() {
    let fragments = []

    this.loadTextFragments = textFragments => {
      fragments = []
      textFragments.map(fragment => {
        const {id, title} = fragment
        fragments.push({
          id,
          title,
          words: createWordItem(title, id).words
        })
      })
    }

    this.insertEmptyAfterFragment = afterId => {
      if (afterId) {
        const index = fragments.findIndex(f => f.id === afterId)
        fragments.splice(index + 1, 0, {id: uuidv4(), title: '', words: []});
        return
      }
      fragments.unshift({id: uuidv4(), title: '', words: []})
    }

    this.deleteFragment = id => fragments = fragments.filter(f => f.id !== id)

    this.getFragments = () => fragments

    this.getFragment = id => fragments.find(f => f.id === id)

    this.updateFragment = (id, payload) => {
      fragments = fragments.map(f => {
        if (f.id === id) {
          f.title = payload.title
          f.words = payload.words
          return f
        }
        return f
      })
    }
  }

  function decodeHtmlEntities(html) {
    const doc = new DOMParser().parseFromString(html, 'text/html');
    return doc.documentElement.textContent;
  }

  function initFragmentsElement($contentItemList, fragmentsManager) {

    $contentItemList.on('click', '.fragment-create-btn', ({target}) => {
      const id = $(target).parents('.fragment-item').attr('data-fragment-id')
      fragmentsManager.insertEmptyAfterFragment(id)
      drawFragments($contentItemList, fragmentsManager)
    })

    $contentItemList.on('click', '.fragment-delete', e => {
      e.preventDefault()
      const id = $(e.target).parents('.fragment-item').attr('data-fragment-id')
      fragmentsManager.deleteFragment(id)
      drawFragments($contentItemList, fragmentsManager)
    })

    $contentItemList.on('click', '.fragment-edit', e => {
      e.preventDefault()
      const $fragmentElem = $(e.target).parents('.fragment-item')
      $fragmentElem.addClass('fragment-editing')
      const fragment = fragmentsManager.getFragment($fragmentElem.attr('data-fragment-id'))
      $fragmentElem
        .find('.fragment-item-words')
        .replaceWith(`<div class="textarea" contenteditable="plaintext-only" spellcheck="true">${getTextBySelections(fragment.words)}</div>`)
    })

    $contentItemList.on('click', '.fragment-save', e => {
      e.preventDefault()
      const $fragmentElem = $(e.target).parents('.fragment-item')
      $fragmentElem.removeClass('fragment-editing')
      const fragmentId = $fragmentElem.attr('data-fragment-id')
      const text = $fragmentElem.find('.textarea').html().trim()
      fragmentsManager.updateFragment($fragmentElem.attr('data-fragment-id'), {
        title: text,
        words: createWordItem(text, fragmentId).words
      })
      drawFragments($contentItemList, fragmentsManager)
    })
  }

  function processOutputAsJson(output) {
    let json = null
    try {
      json = JSON.parse(output.replace(/```json\n?|```/g, ''))
    } catch (ex) {
      console.log(ex.message)
    }
    return json
  }

  this.createFragments = async ({currentSlideId, blockId, container, text, onCreateHandler, mapOrder}) => {

    const $statusElem = $('<div style="font-size: 14px" />')
    container.append($statusElem)

    $('<button class="btn btn-primary">Сформировать фрагменты (AI)</button>')
      .on('click', () => {
        $contentItemList.html(`<img width="32" height="32" src="/img/loading.gif" />`)
        sendMessage({text}, () => {}, content => {
          const textFragments = JSON.parse(content)
          fragmentsManager.loadTextFragments(textFragments.map(title => ({id: uuidv4(), title})))
          drawFragments($contentItemList, fragmentsManager)
        })
      })
      .appendTo($statusElem)

    const $contentItemList = $('<div class="content-mm-fragments"/>')
    container.append($contentItemList)

    const textFragments = text
      .split('\n\n')
      .map(t => decodeHtmlEntities(t))
      .map(t => t.trim())
      .filter(t => t.length > 0);

    const fragmentsManager = new FragmentsManager()
    fragmentsManager.loadTextFragments(textFragments.map(title => ({id: uuidv4(), title})))

    drawFragments($contentItemList, fragmentsManager)
    initFragmentsElement($contentItemList, fragmentsManager)

    const $mapsContainer = $('<div class="content-mm-maps"/>')
    container.append($mapsContainer)

    $mapsContainer.append('<div style="font-weight: 500; font-size: 18px">Создать:<div/>');
    mapOrder.map(({type, title}) => {
      const $item = $(`
<div data-content-type="${type}" class="content-mm-maps-item" style="flex-direction: row; justify-content: space-between">
<label><input class="content-item-selected" type="checkbox" checked> ${title}</label>
<label class="content-item-required-wrap"><input class="content-item-required" type="checkbox"> Обязательно</label>
</div>
`);
      $mapsContainer.append($item);
    });

    const $controls = $('<div class="content-mm-controls"/>')
    container.append($controls)
    $('<button class="btn btn-primary">Создать речевой тренажер</button>')
      .on('click', async ({target}) => {

        const $btn = $(target);
        $btn.prop('disabled', true);

        const $loader = $('<div style="display: flex; align-items: center; gap: 10px">Создание речевого тренажера... <img style="height: 30px" src="/img/loading.gif" alt=""></div>')
        $controls.append($loader);

        const toCreateMaps = $mapsContainer
          .find('.content-mm-maps-item .content-item-selected:checked')
          .map((i, el) => ({
            title: $(el).parent().text().trim(),
            type: $(el).parents('.content-mm-maps-item').attr('data-content-type'),
            fragments: [],
            required: $(el).parents('.content-mm-maps-item').find('.content-item-required').is(':checked')
          }))
          .get();

        const sendAIRequest = toCreateMaps
          .filter(({type}) => type === 'mental-map-plan' || type === 'mental-map-plan-accumulation')
          .length > 0;

        let sentences;
        if (sendAIRequest) {
          const allText = fragmentsManager.getFragments().map(({title}) => title).join('\n');
          await streamMessage(
            {text: allText},
            () => {},
            sentencesJson => {
              try {
                sentences = processOutputAsJson(sentencesJson).map(({sentenceText, sentenceTitle}) => {
                  const fragmentId = uuidv4();
                  return {
                    id: fragmentId,
                    sentenceText,
                    sentenceTitle,
                    words: createWordItem(sentenceText, fragmentId).words
                  }
                });
              } catch (ex) {
                throw new Error(ex.message);
              }
            }
          );
        }

        const mentalMapsAi = new MentalMapsAi()
        for (let i = 0; i < toCreateMaps.length; i++) {
          const type = toCreateMaps[i].type
          switch (type) {
            case 'mental-map':
              structuredClone(fragmentsManager.getFragments()).map(f => toCreateMaps[i].fragments.push({
                id: f.id,
                title: getTextBySelections(f.words)
              }))
              break;
            case 'mental-map-even-fragments':
              structuredClone(fragmentsManager.getFragments()).map(f => toCreateMaps[i].fragments.push({
                id: f.id,
                title: mentalMapsAi.hideWordsEven(f.words)
              }))
              break;
            case 'mental-map-odd-fragments':
              structuredClone(fragmentsManager.getFragments()).map(f => toCreateMaps[i].fragments.push({
                id: f.id,
                title: mentalMapsAi.hideWordsOdd(f.words)
              }))
              break;
            case 'mental-map-plan':
              structuredClone(sentences).map(({id, sentenceText, sentenceTitle}) => toCreateMaps[i].fragments.push({
                id,
                title: sentenceTitle,
                description: sentenceText
              }))
              break;
            case 'mental-map-plan-accumulation':
              structuredClone(sentences).map(({id, sentenceText, sentenceTitle}) => toCreateMaps[i].fragments.push({
                id,
                title: sentenceTitle,
                description: sentenceText
              }))
              break;
          }
        }

        const text = fragmentsManager.getFragments().map(f => getTextBySelections(f.words)).join('<br/>')

        const formData = new FormData()
        formData.append('mentalMaps', JSON.stringify(toCreateMaps))
        formData.append('slideId', currentSlideId)
        formData.append('blockId', blockId)
        formData.append('text', text)

        formHelper.sendForm('/admin/index.php?r=editor/mental-map/create-content-handler', 'POST', formData)
          .done(response => {
            if (response && response.success) {
              $loader.remove();
              onCreateHandler(text)
              toastr.success('Успешно')
              return
            }
            toastr.error(response?.message || 'Произошла ошибка')
          })
      })
      .appendTo($controls)
  }

  async function sendRequest(url, method, headers, payload, formData) {
    let body = null
    if (payload) {
      body = JSON.stringify(payload)
    }
    if (formData) {
      body = formData
    }
    const response = await fetch(url, {
      method,
      cache: 'no-cache',
      headers: {
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content'),
        ...(headers || {})
      },
      body
    })

    if (!response.ok) {
      throw new Error('error - ' + response.statusText)
    }

    return await response.json()
  }

  this.updateFragments = async ({contentItems, container, text, onUpdateHandler, currentSlideId, blockId, onDeleteHandler}) => {

    const $statusElem = $('<div style="font-size: 14px" />')
    container.append($statusElem)

    $('<button class="btn btn-primary">Сформировать фрагменты (AI)</button>')
      .on('click', () => {
        $contentItemList.html(`<img alt="..." width="32" height="32" src="/img/loading.gif" />`)
        sendMessage({text}, () => {}, content => {
          const textFragments = JSON.parse(content)
          fragmentsManager.loadTextFragments(textFragments.map(title => ({id: uuidv4(), title})))
          drawFragments($contentItemList, fragmentsManager)
        })
      })
      .appendTo($statusElem)

    const mentalMap = contentItems.find(m => m.type === 'mental-map')

    const fragmentsManager = new FragmentsManager()
    fragmentsManager.loadTextFragments(mentalMap.fragments)

    const $contentItemList = $('<div class="content-mm-fragments"/>')
    container.append($contentItemList)

    drawFragments($contentItemList, fragmentsManager)
    initFragmentsElement($contentItemList, fragmentsManager)

    const $mapsContainer = $('<div class="content-mm-maps"/>')
    container.append($mapsContainer)

    $mapsContainer.append('<div style="font-weight: 500; font-size: 18px">Существующие ментальные карты/пересказ:<div/>')

    contentItems.map(({id, title, type, required}) => {
      const $item = $(`
<div data-content-id="${id}" class="content-mm-maps-item" style="flex-direction: row; justify-content: space-between">
<label><input type="checkbox" checked disabled> ${title}</label>
<label class="content-item-required-wrap"><input class="content-item-required" type="checkbox" ${required ? 'checked' : ''}> Обязательно</label>
</div>
`);
      $item.find('.content-item-required').on('change', e => {
        $(e.target).prop('disabled', true);
        sendRequest(
          `/admin/index.php?r=editor/mental-map/content-required&slide_id=${currentSlideId}&id=${id}&type=${type}`,
          'post',
          {'Content-Type': 'application/json'},
          {required: e.target.checked}
        ).then(response => {
          if (response.success) {
            $item.find('.content-item-required-wrap')
              .popover({placement: 'top', title: '', content: 'Сохранено', trigger: 'manual'})
              .popover('show');
            setTimeout(() => {
              $item.find('.content-item-required-wrap')
                .popover('hide')
                .popover('destroy')
                .removeAttr('data-original-title');
              $(e.target).removeAttr('disabled');
            }, 500);
          }
        });
      });

      $mapsContainer.append($item);
    })

    const $controls = $('<div class="content-mm-controls"/>')
    container.append($controls)

    $('<button class="btn btn-primary">Изменить</button>')
      .on('click', e => {

        const toUpdateMaps = contentItems.map(({id, title, type}) => ({id, title, type, fragments: []}))

        const mentalMapsAi = new MentalMapsAi()
        for (let i = 0; i < toUpdateMaps.length; i++) {
          const type = toUpdateMaps[i].type
          switch (type) {
            case 'mental-map':
              structuredClone(fragmentsManager.getFragments()).map(f => toUpdateMaps[i].fragments.push({
                id: f.id,
                title: getTextBySelections(f.words)
              }))
              break;
            case 'mental-map-even-fragments':
              structuredClone(fragmentsManager.getFragments()).map(f => toUpdateMaps[i].fragments.push({
                id: f.id,
                title: mentalMapsAi.hideWordsEven(f.words)
              }))
              break;
            case 'mental-map-odd-fragments':
              structuredClone(fragmentsManager.getFragments()).map(f => toUpdateMaps[i].fragments.push({
                id: f.id,
                title: mentalMapsAi.hideWordsOdd(f.words)
              }))
              break;
          }
        }

        const text = fragmentsManager.getFragments().map(f => getTextBySelections(f.words)).join('<br/>')

        const formData = new FormData()
        formData.append('mentalMaps', JSON.stringify(toUpdateMaps))
        formData.append('slideId', currentSlideId)
        formData.append('blockId', blockId)
        formData.append('text', text)

        formHelper.sendForm('/admin/index.php?r=editor/mental-map/update-content-handler', 'POST', formData)
          .done(response => {
            if (response && response.success) {
              onUpdateHandler(text)
              toastr.success('Успешно')
              return
            }
            toastr.error(response?.message || 'Произошла ошибка')
          })
      })
      .appendTo($controls)

    $('<button class="btn btn-danger">Удалить</button>')
      .on('click', async () => {
        if (!confirm('Подтверждаете?')) {
          return
        }
        const response = await sendRequest(`/admin/index.php?r=editor/mental-map/delete&slide_id=${currentSlideId}&block_id=${blockId}`, 'get')
        onDeleteHandler(response?.success)
      })
      .appendTo($controls)
  }
}
