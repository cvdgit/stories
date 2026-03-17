
function ContentMentalMap() {

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
        window.sendStreamMessage(
          `/admin/index.php?r=gpt/mental-map/text-fragments`,
          {text},
          () => {},
          content => {
            const textFragments = window.processOutputAsJson(content);
            fragmentsManager.loadTextFragments(textFragments.map(title => ({id: uuidv4(), title})));
            drawFragments($contentItemList, fragmentsManager);
          }
        );
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

        const allText = fragmentsManager.getFragments().map(({title}) => title).join('\n');

        let sentences;
        if (sendAIRequest) {
          await window.sendStreamMessage(
            `/admin/index.php?r=gpt/story/speech-trainer-sentences`,
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

        const sendTranslateAIRequest = toCreateMaps
          .filter(({type}) => type === 'mental-map-plan-translate')
          .length > 0;
        let translateSentences;
        if (sendTranslateAIRequest) {
          await window.sendStreamMessage(
            `/admin/index.php?r=gpt/story/speech-trainer-translate`,
            {text: allText},
            () => {},
            sentencesJson => {
              try {
                translateSentences = processOutputAsJson(sentencesJson).map(({sentenceText, sentenceTranslateText}) => {
                  const fragmentId = uuidv4();
                  return {
                    id: fragmentId,
                    sentenceText,
                    sentenceTitle: sentenceTranslateText,
                    words: createWordItem(sentenceText, fragmentId).words
                  }
                });
              } catch (ex) {
                throw new Error(ex.message);
              }
            }
          );
        }

        const sendQuestionAIRequest = toCreateMaps
          .filter(({type}) => type === 'mental-map-plan-question')
          .length > 0;
        let questionSentences;
        if (sendQuestionAIRequest) {
          await window.sendStreamMessage(
            `/admin/index.php?r=gpt/story/speech-trainer-question`,
            {text: allText},
            () => {},
            sentencesJson => {
              try {
                questionSentences = processOutputAsJson(sentencesJson).map(({sentenceText, sentenceQuestion}) => {
                  const fragmentId = uuidv4();
                  return {
                    id: fragmentId,
                    sentenceText,
                    sentenceTitle: sentenceQuestion,
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
            case 'mental-map-plan-translate':
              structuredClone(translateSentences).map(({id, sentenceText, sentenceTitle}) => toCreateMaps[i].fragments.push({
                id,
                title: sentenceTitle,
                description: sentenceText
              }))
              break;
            case 'mental-map-plan-question':
              structuredClone(questionSentences).map(({id, sentenceText, sentenceTitle}) => toCreateMaps[i].fragments.push({
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

  this.updateFragments = async ({contentItems, container, text, onUpdateHandler, currentSlideId, blockId, onDeleteHandler}) => {

    const $statusElem = $('<div style="font-size: 14px" />')
    container.append($statusElem)

    $('<button class="btn btn-primary">Сформировать фрагменты (AI)</button>')
      .on('click', () => {
        $contentItemList.html(`<img alt="..." width="32" height="32" src="/img/loading.gif" />`)
        window.sendStreamMessage(
          `/admin/index.php?r=gpt/mental-map/text-fragments`,
          {text},
          () => {},
          content => {
            const textFragments = window.processOutputAsJson(content);
            fragmentsManager.loadTextFragments(textFragments.map(title => ({id: uuidv4(), title})));
            drawFragments($contentItemList, fragmentsManager);
          }
        );
      })
      .appendTo($statusElem)

    const mentalMap = contentItems.find(m => m.type === 'mental-map')

    const fragmentsManager = new FragmentsManager()
    fragmentsManager.loadTextFragments(mentalMap?.fragments || [])

    const $contentItemList = $('<div class="content-mm-fragments"/>')
    container.append($contentItemList)

    drawFragments($contentItemList, fragmentsManager)
    initFragmentsElement($contentItemList, fragmentsManager)

    const $mapsContainer = $('<div class="content-mm-maps"/>')
    container.append($mapsContainer)

    $mapsContainer.append('<div style="font-weight: 500; font-size: 18px">Существующие ментальные карты/пересказ:<div/>')

    contentItems.map(({id, title, type, required, editUrl}) => {
      const $item = $(`
<div data-content-id="${id}" class="content-mm-maps-item" style="flex-direction: row; justify-content: space-between">
<div class="content-item-title" style="display: flex; flex-direction: row; gap: 10px; align-items: center">
<label><input type="checkbox" checked disabled> ${title}</label>
</div>
<label class="content-item-required-wrap"><input class="content-item-required" type="checkbox" ${required ? 'checked' : ''}> Обязательно</label>
</div>
`);

      $item.find('.content-item-required').on('change', async (e) => {
        $(e.target).prop('disabled', true);
        const response = await window.Api.post(
          `/admin/index.php?r=editor/mental-map/content-required&slide_id=${currentSlideId}&id=${id}&type=${type}`,
          {required: e.target.checked}
        );
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

      if (type !== 'retelling') {
        $item.find('.content-item-title').append(
          `<a href="${editUrl}" title="Редактор" target="_blank">
            <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-6" style="margin-left: 10px;">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
            </svg>
          </a>`
        )
      }

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
        const response = await window.Api.get(`/admin/index.php?r=editor/mental-map/delete&slide_id=${currentSlideId}&block_id=${blockId}`);
        onDeleteHandler(response?.success)
      })
      .appendTo($controls)
  }
}
