import './MentalMap.css'
import InnerDialog from "./Dialog";
import VoiceResponse from "./lib/VoiceResponse"
import MissingWordsRecognition from "./lib/MissingWordsRecognition"
import AllTexts from "./components/AllTexts";
import MentalMapImage from "./components/MentalMapImage";
import FragmentResultElement from "./components/FragmentResultElement";
import sendEventSourceMessage from "../app/sendEventSourceMessage";
import Panzoom from "../app/panzoom.min"
import TreeView from "./TreeView/TreeView";
import MentalMapQuestions from "./questions";
import DetailContentQuestions from "./content/DetailContentQuestions";
import {calcHiddenTextPercent, calcTargetTextPercent, canRecording, createWordItem} from "./words";
import DetailContent from "./content/DetailContent";
import {processOutputAsJson, stripTags} from "./common";
import FragmentResultQuestionsElement from "./content/FragmentResultQuestionsElement";
import {diffRetelling, SimilarityChecker} from "./lib/calcSimilarity";

/**
 * @param element
 * @param {Reveal|undefined} deck
 * @param params
 * @returns {{run: ((function(): Promise<void>)|*)}}
 * @constructor
 */
export default function MentalMap(element, deck, params) {

  this.element = element
  let texts = []
  let mentalMapHistory = []
  let mentalMapId

  params = params || {}
  params.slide_id = params.slide_id || (deck ? Number($(deck.getCurrentSlide()).attr('data-id')) : null)

  const repetitionMode = Boolean(params?.repetitionMode)
  const getCourseMode = Boolean(params?.getCourseMode)

  const voiceResponse = new VoiceResponse(new MissingWordsRecognition({}))
  voiceResponse.onResult(args => {
    const finalSpan = document.getElementById("final_span")
    if (finalSpan) {
      finalSpan.innerHTML = args.args?.result
    }
    const interimSpan = document.getElementById("interim_span")
    if (interimSpan) {
      interimSpan.innerHTML = args.args?.interim
    }
  })

  const container = document.createElement('div')
  container.classList.add('mental-map-container')

  const blockTypes = ['text', 'image']

  function showDialogHandler() {
    if (!deck) {
      return
    }
    deck.configure({keyboard: false});
    $('.reveal .story-controls').hide();
    blockTypes.map(blockType => {
      $(deck.getCurrentSlide()).find(`div.sl-block[data-block-type=${blockType}]`).css('zIndex', '-1')
    })
  }

  function hideDialogHandler() {
    if (!deck) {
      return
    }
    if ($(deck.getCurrentSlide()).find('.slide-hints-wrapper').length) {
      return
    }
    deck.configure({keyboard: true})
    $('.reveal .story-controls').show();
    blockTypes.map(blockType => {
      $(deck.getCurrentSlide()).find(`div.sl-block[data-block-type=${blockType}]`).css('zIndex', 'auto')
    })
  }

  function RecordingLangStore(defaultLang) {
    let lang = defaultLang
    return {
      fromStore(langValue) {
        if (langValue !== lang) {
          lang = langValue
        }
        return lang
      },
      get() {
        return lang
      }
    }
  }

  const langStore = new RecordingLangStore('ru-RU')

  async function saveUserResult(payload) {
    const response = await fetch(`/mental-map/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      },
      body: JSON.stringify(payload),
    })
    if (!response.ok) {
      throw new Error(response.statusText)
    }
    return await response.json()
  }

  async function finishRepetition(mentalMapId) {
    const response = await fetch(`/mental-map/finish`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      },
      body: JSON.stringify({
        mental_map_id: mentalMapId
      }),
    })
    if (!response.ok) {
      throw new Error(response.statusText)
    }
    return await response.json()
  }

  function showMentalMapHandler(zoomWrap, closeMentalMapHandler, fastMode, fastModeChangeHandler) {
    const zoomContainer = document.createElement('div')
    zoomContainer.classList.add('zoom-container')

    zoomContainer.appendChild(zoomWrap)

    const closeBtn = document.createElement('button')
    closeBtn.classList.add('btn', 'btn-small', 'mental-map-close')
    closeBtn.textContent = 'Закрыть'
    closeBtn.addEventListener('click', closeMentalMapHandler)
    zoomContainer.appendChild(closeBtn)

    const hideBtn = document.createElement('button')
    hideBtn.classList.add('btn', 'btn-small', 'mental-map-hide-btn')
    hideBtn.textContent = 'Скрыть'
    hideBtn.addEventListener('click', e => {
      $(e.target).toggleClass('img-hide')
      if ($(e.target).hasClass('img-hide')) {
        $(zoomWrap).find('.mental-map-img .map-img')
          .each((i, el) => $(el).css({opacity: '0'}))
        $(zoomWrap).find('.mental-map-img').each((i, el) => {
          $(el).append(`<span class="mental-map-point"></span>`)
        })
        $(e.target).text('Показать')
      } else {
        $(zoomWrap).find('.mental-map-img span').remove()
        $(zoomWrap).find('.mental-map-img .map-img')
          .each((i, el) => $(el).css({opacity: '1'}))
        $(e.target).text('Скрыть')
      }
    })
    zoomContainer.appendChild(hideBtn)

    const fastWrapEl = document.createElement('div')
    fastWrapEl.classList.add('mental-map-fast-wrap')
    fastWrapEl.innerHTML = `<label>Быстрый режим<input type="checkbox" ${fastMode ? 'checked' : ''}></label>`
    fastWrapEl.querySelector('input[type=checkbox]').addEventListener('click', fastModeChangeHandler)
    zoomContainer.appendChild(fastWrapEl)

    return zoomContainer
  }

  function historyIsDone(history) {
    return history.reduce((all, val) => all && val.done, true)
  }

  function initPanZoom(element, mapWidth, mapHeight) {
    let initialZoom = 0.8
    const containerWidth = container.offsetWidth
    const containerHeight = container.offsetHeight

    if (mapHeight > containerHeight) {
      initialZoom = containerHeight / mapHeight
    } else {
      initialZoom = 1;
    }

    if (mapWidth > containerWidth) {
      initialZoom = containerWidth / mapWidth
    }

    return Panzoom(element, {
      excludeClass: 'mental-map-img',
      startScale: initialZoom,
      minScale: 0.4,
      maxScale: 2,
    })
  }

  const removePunctuation = text => text.replace(/[!"#$%&'()*+,-./:;<=>?@[\]^_`{|}–«»~]/g, '').replace(/\s{2,}/g, " ")

  function mapImageClickHandler({image, texts, historyItem, rewritePrompt, threshold, dialogHideHandler, fastMode, hideFragmentText, settingsPromptId}) {
    const text = texts.find(t => t.id === image.id)

    let hideText = hideFragmentText;
    if (image.textState === 'hide') {
      hideText = true;
    }
    if (image.textState === 'show') {
      hideText = false;
    }

    const detailContainer = DetailContent({
      image,
      text,
      historyItem,
      rewritePrompt,
      itemClickHandler: (recordingWrap) => {
        if (voiceResponse.getStatus()) {
          voiceResponse.stop()
          const voiceLang = langStore.fromStore($(recordingWrap).find("#voice-lang option:selected").val())
          startRecording(recordingWrap.querySelector('#start-recording'), voiceLang, stripTags(text.text), false)
        }
        recordingWrap.querySelector('#hidden-text-percent').innerText = calcHiddenTextPercent(text) + '%'

        const gm = recordingWrap.querySelector('#start-recording')

        $(gm)
          .removeAttr('title')
          .tooltip('destroy')

        gm.classList.remove('disabled')
        if (!canRecording(text)) {
          gm.classList.add('disabled')
          $(gm)
            .attr('title', 'Нужно закрыть все важные слова')
            .tooltip()
        }
        recordingWrap.querySelector('#target-text-percent').innerText = calcTargetTextPercent(text) + '%'
      },
      diffClickHandler: () => {
        $(detailContainer).append(createDiffContent({
          text: stripTags(image.text),
          userResponse: detailContainer.querySelector('#result_span').innerText.trim()
        }))
      },
      hideText
    })
    const dialog = new InnerDialog($(container), {title: 'Перескажите текст', content: detailContainer});
    dialog.show(wrapper => {
      showDialogHandler()

      $(wrapper).on('paste', (e) => {
        e.preventDefault()
        return false
      })

      $(wrapper).find('.result-item-value').tooltip()

      $(wrapper).find(`#voice-lang`).val(langStore.get())

      wrapper.querySelector('#start-recording').addEventListener('click', e => {

        if (!canRecording(text)) {
          return
        }

        if (!voiceResponse.getStatus()) {
          ['#result_span', '#final_span', '#interim_span'].map(q => {
            wrapper.querySelector(q).innerHTML = ''
            wrapper.querySelector('#start-retelling-wrap').style.display = 'none'
          });
        }
        wrapper.querySelector('.content-diff').style.display = 'none';

        const voiceLang = langStore.fromStore($(wrapper).find("#voice-lang option:selected").val());
        startRecording(e.target, voiceLang, stripTags(text.text), true, threshold, () => {
          wrapper.querySelector('.content-diff').style.display = 'inline-block';
          if (fastMode) {
            setTimeout(() => {
              wrapper.querySelector('#start-retelling').click()
            }, 100)
          }
        })
      })
      wrapper.querySelector('#start-retelling').addEventListener('click', e => {

        if (voiceResponse.getStatus()) {
          voiceResponse.stop()
        }

        const userResponse = wrapper.querySelector('#result_span').innerText.trim()
        if (!userResponse) {
          alert("Ответ пользователя пуст")
          return
        }

        const content = createRetellingContent(() => {
          ['#result_span', '#final_span', '#interim_span'].map(q => {
            wrapper.querySelector(q).innerHTML = ''
            wrapper.querySelector('#start-retelling-wrap').style.display = 'none'
          })
        })
        wrapper.querySelector('.mental-map-detail-container').appendChild(content)

        const clearText = $(wrapper).find('#clear-text').is(':checked')

        const removePunctuation = text => text.replace(/[!"#$%&'()*+,-./:;<=>?@[\]^_`{|}–«»~]/g, '').replace(/\s{2,}/g, " ")

        startRetelling(
          clearText ? removePunctuation(userResponse) : userResponse,
          clearText ? removePunctuation(stripTags(text.text)) : stripTags(text.text),
          threshold,
          settingsPromptId || image.promptId
        ).then(response => {
          const json = processOutputAsJson(wrapper.querySelector('#retelling-response').innerText)
          if (json) {
            const val = Number(json.overall_similarity)
            wrapper.querySelector('#similarity-percent').innerText = `${val}%`

            const textHidingPercentage = calcHiddenTextPercent(text)
            const textTargetPercentage = calcTargetTextPercent(text)

            const detailTextContent = wrapper.querySelector('.detail-text').cloneNode(true)
            detailTextContent.querySelector('.detail-text-actions').remove()

            saveUserResult({
              story_id: params?.story_id,
              slide_id: params?.slide_id,
              mental_map_id: mentalMapId,
              image_fragment_id: image.id,
              overall_similarity: Number(json.overall_similarity),
              text_hiding_percentage: textHidingPercentage,
              text_target_percentage: textTargetPercentage,
              content: detailTextContent.innerHTML,
              repetition_mode: repetitionMode,
              threshold
            }).then(response => {
              if (response && response?.success) {
                historyItem.all = response.history.all
                historyItem.hiding = response.history.hiding
                historyItem.target = response.history.target
                historyItem.done = response.history.done

                // wrapper.querySelector('.result-item-value').innerHTML = `${val}% (${textHidingPercentage}% / ${textTargetPercentage}%)`
                wrapper.querySelector('.image-item > .result-item').remove()
                wrapper.querySelector('.image-item').appendChild(FragmentResultElement(historyItem))

                if (fastMode) {
                  if (historyItem.done) {
                    dialog.hide()
                  }
                }
              }
            })
          }
        })
      })

      wrapper.querySelector('#hidden-text-percent').innerText = calcHiddenTextPercent(text) + '%'
      wrapper.querySelector('#target-text-percent').innerText = calcTargetTextPercent(text) + '%'

      wrapper.querySelector('#result_span').addEventListener('input', e => {
        const text = e.target.innerText
        const display = text.length > 0 ? 'block' : 'none'
        if (display !== wrapper.querySelector('#start-retelling-wrap').style.display) {
          wrapper.querySelector('#start-retelling-wrap').style.display = display
        }
        if (display === 'none') {
          wrapper.querySelector('.content-diff').style.display = 'none'
        }
      })

      if (fastMode) {
        setTimeout(() => {
          wrapper.querySelector('#start-recording').click()
        }, 100)
      }
    })
    dialog.onHide(dialogHideHandler)
  }

  function mapImageClickHandlerQuestions({image, questionItem, historyItem, rewritePrompt, threshold, dialogHideHandler, fastMode}) {
    const detailContainer = DetailContentQuestions({
      image,
      questionItem,
      historyItem,
      rewritePrompt
    })
    const dialog = new InnerDialog($(container), {title: 'Перескажите текст отвечая на вопросы', content: detailContainer});
    dialog.show(wrapper => {
      showDialogHandler()

      $(wrapper).on('paste', (e) => {
        e.preventDefault()
        return false
      })

      $(wrapper).find('.result-item-value').tooltip()

      $(wrapper).find(`#voice-lang`).val(langStore.get())

      wrapper.querySelector('#start-recording').addEventListener('click', e => {

        if (!voiceResponse.getStatus()) {
          ['#result_span', '#final_span', '#interim_span'].map(q => {
            wrapper.querySelector(q).innerHTML = ''
            wrapper.querySelector('#start-retelling-wrap').style.display = 'none'
          });
        }

        const voiceLang = langStore.fromStore($(wrapper).find("#voice-lang option:selected").val());
        startRecording(e.target, voiceLang, stripTags(image.text), true, threshold, () => {
          if (fastMode) {
            setTimeout(() => {
              wrapper.querySelector('#start-retelling').click()
            }, 100)
          }
        })
      })

      wrapper.querySelector('#start-retelling').addEventListener('click', e => {

        if (voiceResponse.getStatus()) {
          voiceResponse.stop()
        }

        const userResponse = wrapper.querySelector('#result_span').innerText.trim()
        if (!userResponse) {
          alert("Ответ пользователя пуст")
          return
        }

        const content = createRetellingContent(() => {
          ['#result_span', '#final_span', '#interim_span'].map(q => {
            wrapper.querySelector(q).innerHTML = ''
            wrapper.querySelector('#start-retelling-wrap').style.display = 'none'
          })
        })
        wrapper.querySelector('.mental-map-detail-container').appendChild(content)

        const clearText = $(wrapper).find('#clear-text').is(':checked')

        const removePunctuation = text => text.replace(/[!"#$%&'()*+,-./:;<=>?@[\]^_`{|}–«»~]/g, '').replace(/\s{2,}/g, " ")

        startRetelling(clearText ? removePunctuation(userResponse) : userResponse, clearText ? removePunctuation(stripTags(image.text)) : stripTags(image.text), threshold, image.promptId).then(response => {
          const json = processOutputAsJson(wrapper.querySelector('#retelling-response').innerText)
          if (json) {
            const val = Number(json?.overall_similarity)
            detailContainer.querySelector('#similarity-percent').innerText = `${val}%`

            //const textHidingPercentage = calcHiddenTextPercent(text)
            //const textTargetPercentage = calcTargetTextPercent(text)

            const detailTextContent = detailContainer.querySelector('.detail-text').cloneNode(true)
            detailTextContent.querySelector('.detail-text-actions')?.remove()

            saveUserResult({
              story_id: params?.story_id,
              slide_id: params?.slide_id,
              mental_map_id: mentalMapId,
              image_fragment_id: image.id,
              overall_similarity: Number(json?.overall_similarity),
              text_hiding_percentage: 0,
              text_target_percentage: 0,
              content: detailTextContent.innerHTML,
              repetition_mode: repetitionMode,
              threshold
            }).then(response => {
              if (response && response?.success) {
                historyItem.all = response.history.all
                //historyItem.hiding = response.history.hiding
                //historyItem.target = response.history.target
                historyItem.done = response.history.done

                // wrapper.querySelector('.result-item-value').innerHTML = `${val}% (${textHidingPercentage}% / ${textTargetPercentage}%)`
                wrapper.querySelector('.image-item > .result-item').remove()
                wrapper.querySelector('.image-item').appendChild(FragmentResultQuestionsElement(historyItem))
              }
            })
          }
        })
      })

      //recordingWrap.querySelector('#hidden-text-percent').innerText = calcHiddenTextPercent(text) + '%'
      //recordingWrap.querySelector('#target-text-percent').innerText = calcTargetTextPercent(text) + '%'

      wrapper.querySelector('#result_span').addEventListener('input', e => {
        const text = e.target.innerText
        const display = text.length > 0 ? 'block' : 'none'
        if (display !== wrapper.querySelector('#start-retelling-wrap').style.display) {
          wrapper.querySelector('#start-retelling-wrap').style.display = display
        }
      })

      if (fastMode) {
        setTimeout(() => {
          wrapper.querySelector('#start-recording').click()
        }, 100)
      }
    })
    dialog.onHide(dialogHideHandler)
  }

  async function restartMentalMap(id) {
    const response = await fetch(`/mental-map/restart?id=${id}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      },
    })
    if (!response.ok) {
      throw new Error(response.statusText)
    }
    return await response.json()
  }

  const restartHandler = async (id) => {
    if (!confirm('Будет удалена история прохождения этой ментальной карты. Подтверждаете?')) {
      return false
    }

    try {
      const response = await restartMentalMap(id)
      if (response.success) {
        this.element.innerHTML = ''
        container.innerHTML = ''
        await run()
        return true
      }
    } catch (ex) {
      console.log(ex.message)
    }

    return false
  }

  const run = async () => {

    let responseJson
    try {
      responseJson = await params.init()
    } catch (ex) {
      container.innerText = ex.message
      this.element.appendChild(container)
      return
    }
console.log(params)
    const {mentalMap: json, history, rewritePrompt, threshold} = responseJson
    mentalMapId = json.id
    mentalMapHistory = history

    const {treeView} = json
    if (treeView) {
      this.element.appendChild(TreeView({
        id: json.id,
        name: json.name,
        tree: json.treeData,
        history,
        params: {
          story_id: params?.story_id,
          slide_id: params?.slide_id,
          mental_map_id: params.mentalMapId,
          repetition_mode: repetitionMode,
          threshold
        },
        settings: json.settings || {}
      }, new VoiceResponse(new MissingWordsRecognition({}))))

      $('[data-toggle="tooltip"]', this.element).tooltip({
        container: 'body'
      });

      return
    }

    window.addEventListener('blur', function() {
      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
        const el = document.querySelector('#start-recording')
        if (el) {
          $(el).trigger('click')
        }
      }
    }, false);

    const {mapTypeIsMentalMapQuestions, questions} = json
    const mapQuestions = new MentalMapQuestions({typeIsMentalMapQuestions: mapTypeIsMentalMapQuestions, questions})

    let fastMode = true
    function fastModeChangeHandler(e) {
      fastMode = e.target.checked
    }

    texts = json.map.images.map(image => createWordItem(image.text, image.id))

    const imageFirst = Boolean(json.settings?.imageFirst)
    const hideTooltip = Boolean(json.settings?.hideTooltip)
    const hideFragmentText = Boolean(json.settings?.hideText)
    const settingsPromptId = json.settings?.promptId

    function hideTooltipChecker(tooltipState) {
      if (tooltipState === 'hide') {
        return true;
      }
      if (tooltipState === 'show') {
        return false;
      }
      return hideTooltip;
    }

    function fragmentDialogHideHandler(image, historyItem) {
      hideDialogHandler()

      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
      }

      const el = container.querySelector(`[data-image-fragment-id='${image.id}']`)
      el.querySelector('.image-item > .result-item').remove()
      el.querySelector('.image-item').appendChild(FragmentResultElement(historyItem))
      $(el.querySelector('.result-item')).tooltip()

      // appendAllTextWordElements(texts.find(t => t.id === image.id).words, el.querySelector('.text-item'))

      if (repetitionMode) {
        const done = mentalMapHistory.reduce((all, val) => all && val.done, true)
        if (done) {
          const content = createFinishRepetitionContent()
          $(container).parents('.mental-map').append(content)
          finishRepetition(params.mentalMapId)
        }
      }

      /*texts = texts.map(t => {
        if (t.id === image.id) {
          return createWordItem(image)
        }
        return t
      })*/

      if (/*getCourseMode &&*/ historyIsDone(history)) {
        const content = createFinishContent(history, texts, mapQuestions.typeIsMentalMapQuestions(), () => restartHandler(mentalMapId))
        $(container).parents('.mental-map').append(content)
      }
    }

    container.appendChild(AllTexts(texts, json.map.images, history, (image) => {

      const historyItem = history.find(h => h.id === image.id)
      const questionItem = mapQuestions.findQuestion(image.id)
      if (mapQuestions.typeIsMentalMapQuestions()) {
        mapImageClickHandlerQuestions({
          image,
          questionItem,
          historyItem,
          rewritePrompt,
          threshold,
          dialogHideHandler: () => fragmentDialogHideHandler(image, historyItem),
          fastMode
        })
        return
      }

      mapImageClickHandler({
        image,
        texts,
        historyItem,
        rewritePrompt,
        threshold,
        dialogHideHandler: () => fragmentDialogHideHandler(image, historyItem),
        fastMode,
        hideFragmentText,
        settingsPromptId
      })
    }, mapQuestions.typeIsMentalMapQuestions()))

    const toolbar = document.createElement('div')
    toolbar.classList.add('mental-map-toolbar')

    const mentalMapBtn = document.createElement('button')
    mentalMapBtn.classList.add('btn', 'btn-small', 'mental-map-btn')
    mentalMapBtn.textContent = 'Ментальная карта'
    let zoom
    mentalMapBtn.addEventListener('click', (e) => {

      //const zoomContainer = document.createElement('div')
      //zoomContainer.classList.add('zoom-container')

      const zoomWrap = MentalMapImage(
        json.map.url,
        `${json.map.width}px`,
        `${json.map.height}px`,
        json.map.images,
        (image) => {
          element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)

          const historyItem = history.find(h => h.id === image.id)
          const questionItem = mapQuestions.findQuestion(image.id)

          if (mapQuestions.typeIsMentalMapQuestions()) {
            mapImageClickHandlerQuestions({
              image,
              questionItem,
              historyItem,
              rewritePrompt,
              threshold,
              dialogHideHandler: () => {
                element.parentElement.addEventListener('wheel', zoom.zoomWithWheel)
                const imgElem = zoomContainer.querySelector(`[data-img-id='${image.id}']`)
                if (historyItem.done) {
                  imgElem.classList.add('fragment-item-done')
                  if (image.makeTransparent) {
                    imgElem.classList.add('fragment-transparent')
                  }
                }
                fragmentDialogHideHandler(image, historyItem)
              },
              fastMode
            })
            return
          }

          mapImageClickHandler({
            image,
            texts,
            historyItem,
            rewritePrompt,
            threshold,
            dialogHideHandler: () => {
              element.parentElement.addEventListener('wheel', zoom.zoomWithWheel)
              const imgElem = zoomContainer.querySelector(`[data-img-id='${image.id}']`)
              if (historyItem.done) {
                imgElem.classList.add('fragment-item-done')
                if (image.makeTransparent) {
                  imgElem.classList.add('fragment-transparent')
                }
              }
              fragmentDialogHideHandler(image, historyItem)
            },
            fastMode,
            hideFragmentText,
            settingsPromptId
          })
        },
        mentalMapHistory,
        hideTooltipChecker
      )

      /*
      zoomContainer.appendChild(zoomWrap)

      const closeBtn = document.createElement('button')
      closeBtn.classList.add('btn', 'btn-small', 'mental-map-close')
      closeBtn.textContent = 'Закрыть'
      closeBtn.addEventListener('click', () => {
        zoom.destroy()
        zoomContainer.remove()
        element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
      })
      zoomContainer.appendChild(closeBtn)

      const hideBtn = document.createElement('button')
      hideBtn.classList.add('btn', 'btn-small', 'mental-map-hide-btn')
      hideBtn.textContent = 'Скрыть'
      hideBtn.addEventListener('click', e => {
        $(e.target).toggleClass('img-hide')
        if ($(e.target).hasClass('img-hide')) {
          $(this.element).find('.mental-map-img .map-img')
            .each((i, el) => $(el).css({opacity: '0'}))
          $(this.element).find('.mental-map-img').each((i, el) => {
            $(el).append(`<span class="mental-map-point"></span>`)
          })
          $(e.target).text('Показать')
        } else {
          $(this.element).find('.mental-map-img span').remove()
          $(this.element).find('.mental-map-img .map-img')
            .each((i, el) => $(el).css({opacity: '1'}))
          $(e.target).text('Скрыть')
        }
      })
      zoomContainer.appendChild(hideBtn)
       */

      const zoomContainer = showMentalMapHandler(zoomWrap, () => {
        zoom.destroy()
        zoomContainer.remove()
        element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
      }, fastMode, fastModeChangeHandler)

      this.element.appendChild(zoomContainer)

      $('.mental-map-img .map-img').tooltip()

      let initialZoom = 0.8
      const containerWidth = container.offsetWidth
      const containerHeight = container.offsetHeight

      if (json.map.height > containerHeight) {
        initialZoom = containerHeight / json.map.height;
      } else {
        initialZoom = 1;
      }

      if (json.map.width > containerWidth) {
        initialZoom = containerWidth / json.map.width;
      }

      zoom = Panzoom(zoomWrap, {
        excludeClass: 'mental-map-img',
        startScale: initialZoom,
        minScale: 0.4,
        maxScale: 2,
      });
      element.parentElement.addEventListener('wheel', zoom.zoomWithWheel);
    })

    toolbar.appendChild(mentalMapBtn)

    const header = document.createElement('p')
    header.style.marginLeft = '20px'
    header.style.marginBottom = '0'
    header.style.fontSize = '2.2rem';
    header.style.lineHeight = '3rem';
    header.innerHTML = `Точность пересказа установлена в <strong>${threshold}</strong>%`
    toolbar.appendChild(header)

    this.element.appendChild(toolbar)
    this.element.appendChild(container)

    $('.result-item-value').tooltip()

    if (imageFirst) {

      const zoomWrap = MentalMapImage(
        json.map.url,
        `${json.map.width}px`,
        `${json.map.height}px`,
        json.map.images,
        (image) => {

          element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)

          const historyItem = history.find(h => h.id === image.id)
          const questionItem = mapQuestions.findQuestion(image.id)

          if (mapQuestions.typeIsMentalMapQuestions()) {
            mapImageClickHandlerQuestions({
              image,
              questionItem,
              historyItem,
              rewritePrompt,
              threshold,
              dialogHideHandler: () => {
                element.parentElement.addEventListener('wheel', zoom.zoomWithWheel)
                const imgElem = zoomContainer.querySelector(`[data-img-id='${image.id}']`)
                if (historyItem.done) {
                  imgElem.classList.add('fragment-item-done')
                  if (image.makeTransparent) {
                    imgElem.classList.add('fragment-transparent')
                  }
                }
                fragmentDialogHideHandler(image, historyItem)
              },
              fastMode
            })
            return
          }

          mapImageClickHandler({
            image,
            texts,
            historyItem,
            rewritePrompt,
            threshold,
            dialogHideHandler: () => {
              element.parentElement.addEventListener('wheel', zoom.zoomWithWheel)
              const imgElem = zoomContainer.querySelector(`[data-img-id='${image.id}']`)
              if (historyItem.done) {
                imgElem.classList.add('fragment-item-done')
                if (image.makeTransparent) {
                  imgElem.classList.add('fragment-transparent')
                }
              }
              fragmentDialogHideHandler(image, historyItem)
            },
            fastMode,
            hideFragmentText,
            settingsPromptId
          })
        },
        mentalMapHistory,
        hideTooltipChecker
      )
      const zoomContainer = showMentalMapHandler(zoomWrap, () => {
        zoom.destroy()
        zoomContainer.remove()
        element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
      }, fastMode, fastModeChangeHandler)
      this.element.appendChild(zoomContainer)
      //$('.mental-map-img').popover()

      zoom = initPanZoom(zoomWrap, json.map.width, json.map.height)
      element.parentElement.addEventListener('wheel', zoom.zoomWithWheel)
    }

    if (/*getCourseMode &&*/ historyIsDone(history)) {
      const content = createFinishContent(history, texts, mapQuestions.typeIsMentalMapQuestions(), () => restartHandler(mentalMapId))
      $(container).parents('.mental-map').append(content)
      if (imageFirst) {
        element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
      }
    }
  }

  /**
   * @param {HTMLElement} element
   * @param {string} lang
   * @param text
   */
  function startRecording(element, lang, text, makeRewrite, threshold, stopHandler) {
    const state = element.dataset.state
    if (!state) {
      $(document.getElementById("start-retelling-wrap")).hide()
      setTimeout(function () {
        voiceResponse.start(new Event('voiceResponseStart'), lang, function () {
          element.dataset.state = 'recording'
          element.classList.add('recording')

          const ring = document.createElement('div')
          ring.classList.add('pulse-ring')
          element.parentNode.insertBefore(ring, element)
        });
      }, 500);
    } else {
      voiceResponse.stop(function (args) {

        element.parentNode.querySelector('.pulse-ring').remove()
        element.classList.remove('recording')
        delete element.dataset.state

        const $resultSpan = $(document.getElementById("result_span"))
        const $finalSpan = $(document.getElementById("final_span"))

        if ($finalSpan.text().trim().length) {
          $resultSpan.text(
            $resultSpan.text().trim().length
              ? $resultSpan.text().trim() + "\n" + $finalSpan.text().trim()
              : $finalSpan.text().trim()
          )
          $finalSpan.empty()
        }

        if ($resultSpan.text().trim().length) {
          $(document.getElementById("start-retelling-wrap")).show()
        }

        const userResponse = $resultSpan.text().trim()
        if (userResponse.length && makeRewrite) {

          const similarityChecker = new SimilarityChecker(threshold)
          if (similarityChecker.check(text, userResponse)) {
            if (typeof stopHandler === 'function') {
              stopHandler()
            }
            return
          }

          const content = createRewriteContent(() => {})
          $(element).parents('.mental-map-detail-container').append(content)
          sendMessage(
            `/admin/index.php?r=gpt/stream/retelling-rewrite`,
            {
              userResponse,
              slideTexts: text
            },
            (message) => {
              $resultSpan.text(message)
            },
            () => {
              content.remove()
            },
            () => {
              content.remove()
              if (typeof stopHandler === 'function') {
                stopHandler()
              }
            }
          )
        }
      })
    }
  }

  function createRewriteContent(hideCallback) {
    const wrap = document.createElement('div')
    wrap.classList.add('retelling-wrap')
    wrap.style.backgroundColor = 'transparent'
    wrap.style.padding = '0'
    wrap.innerHTML = `
      <div style="display: flex; align-items: center; justify-content: center; height: 100%; background-color: rgba(255, 255, 255, 0.4); backdrop-filter: blur(4px);">
        <img src="/img/loading.gif" style="width: 100px" />
      </div>
    `
    return wrap
  }

  function createFinishRepetitionContent() {
    const elem = document.createElement('div')
    elem.classList.add('retelling-wrap')
    elem.style.backgroundColor = 'transparent'
    elem.style.padding = '0'
    elem.innerHTML = `
      <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background-color: rgba(255, 255, 255, 0.4); backdrop-filter: blur(4px);">
        <h2 style="margin-bottom: 20px">Ментальная карта пройдена</h2>
        <a class="btn" href="${params.repetitionBackUrl}">Назад к обучению</a>
      </div>
    `
    return elem
  }

  function createFinishContent(history, texts, isMentalMapQuestions, restartHandler) {
    const elem = document.createElement('div')
    elem.classList.add('retelling-wrap')
    elem.style.backgroundColor = 'transparent'
    elem.style.padding = '0'
    elem.innerHTML = `
      <div class="mental-map-done-wrap" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background-color: rgba(255, 255, 255, 0.4); backdrop-filter: blur(4px);">
        <h2 style="margin-bottom: 20px; display: flex; align-items: center">Ментальная карта пройдена (<a class="mental-map-restart" style="font-size:20px" href="">пройти еще раз</a>)</h2>
      </div>
    `

    const historyWrap = document.createElement('div')
    historyWrap.style.width = '800px'
    historyWrap.style.backgroundColor = 'white'
    historyWrap.style.padding = '20px'
    historyWrap.style.border = '1px #ddd solid'
    historyWrap.style.borderRadius = '10px'
    historyWrap.style.maxHeight = '500px'
    historyWrap.style.overflowY = 'auto'
    history.map(h => {

      const el = document.createElement('div')
      el.style.marginBottom = '10px'
      el.style.display = 'flex'
      el.style.flexDirection = 'row'
      el.style.columnGap = '20px'

      el.appendChild(isMentalMapQuestions ? FragmentResultQuestionsElement(h) : FragmentResultElement(h))

      const textEl = document.createElement('div')
      textEl.classList.add('text-item')
      textEl.style.flex = '1 1 auto'
      textEl.style.textAlign = 'left'
      textEl.innerHTML = texts.find(t => t.id === h.id)?.text
      el.appendChild(textEl)

      historyWrap.appendChild(el)
    })

    elem.querySelector('.mental-map-done-wrap').appendChild(historyWrap)

    elem.querySelector('.mental-map-restart').addEventListener('click', e => {
      e.preventDefault()
      if (restartHandler()) {
        elem.remove()
      }
    })

    return elem
  }

  function createRetellingContent(hideCallback) {
    const wrap = document.createElement('div')
    wrap.classList.add('retelling-wrap')
    wrap.innerHTML = `
        <div contenteditable="plaintext-only" id="retelling-response"
             style="font-size: 2.2rem; text-align: left; line-height: 3rem; overflow-y: scroll; height: 100%; max-height: 100%;"></div>
        <div style="display: flex; margin-top: 10px; flex-direction: row; align-items: center; justify-content: center">
            <img id="voice-loader" height="50px" src="/img/loading.gif" alt="">
            <button style="display: none" id="voice-finish" type="button" class="btn">OK</button>
        </div>
    `
    wrap.querySelector('#voice-finish').addEventListener('click', () => {
      wrap.remove()
      if (typeof hideCallback === 'function') {
        hideCallback()
      }
    })
    return wrap
  }

  function createDiffContent({text, userResponse}) {
    const wrap = document.createElement('div')
    wrap.classList.add('retelling-wrap')
    wrap.innerHTML = `
        <div style="font-size: 2.2rem; text-align: left; line-height: 3rem; overflow-y: scroll; height: 100%; max-height: 100%;">
          <div style="display: flex; flex-direction: column; height: 100%; user-select: none">
            <div class="diff-text" style="flex: 1"></div>
            <div class="diff-user-response" style="flex: 1"></div>
            <div class="diff-diff" style="flex: 1"></div></div>
</div>
        <div style="display: flex; margin-top: 10px; flex-direction: row; align-items: center; justify-content: center">
        <button type="button" class="button diff-dialog-close">Закрыть</button>
        </div>
    `
    wrap.querySelector('.diff-dialog-close').addEventListener('click', () => {
      wrap.remove()
    })

    wrap.querySelector('.diff-text').innerHTML = text
    wrap.querySelector('.diff-user-response').innerHTML = userResponse
    wrap.querySelector('.diff-diff').appendChild(diffRetelling(text.toLowerCase(), userResponse.toLowerCase()))

    return wrap
  }

  function createProcessContent(text, hideCallback) {
    const wrap = document.createElement('div')
    wrap.classList.add('retelling-wrap')
    wrap.style.backgroundColor = 'transparent'
    wrap.style.padding = '0'
    wrap.innerHTML = `
      <div class="retelling-status">
        <div class="retelling-info-text">${text}</div>
        <img class="retelling-loader" src="/img/loading.gif" alt="..." />
        <button class="btn retelling-resend" type="button">Повторить</button>
      </div>
    `
    return {
      getElement() {
        return wrap
      },
      setText(text) {
        wrap.querySelector('.retelling-info-text').textContent = text
      },
      setErrorText(text, resendHandler) {
        wrap.querySelector('.retelling-status').classList.add('retelling-status-error')
        wrap.querySelector('.retelling-info-text').textContent = text
        if (resendHandler !== undefined) {
          wrap.querySelector('.retelling-resend').addEventListener('click', resendHandler)
        }
      },
      remove() {
        wrap.remove()
      }
    }
  }

  async function startRetelling(userResponse, targetText, threshold, promptId) {

    const onMessage = message => {
      const el = document.getElementById("retelling-response")
      $(el).show()
      el.innerText = message
      el.scrollTop = el.scrollHeight
    }

    const onError = message => {
      const el = document.getElementById("retelling-response")
      $(el).show()
      el.innerText = message
      $(document.getElementById('voice-loader')).hide()
      $(document.getElementById('voice-finish')).show()
    }
    const onEnd = () => {
      $(document.getElementById('voice-loader')).hide()
      $(document.getElementById('voice-finish')).show()
    }

    const similarityChecker = new SimilarityChecker(threshold)
    if (similarityChecker.check(targetText, userResponse)) {
      onMessage(`{"overall_similarity": ${similarityChecker.getSimilarityPercentage()}}`)
      onEnd()
      return new Promise((resolve, reject) => {
        resolve({})
      })
    }

    return await sendMessage(`/admin/index.php?r=gpt/stream/retelling`, {
      userResponse,
      slideTexts: targetText,
      promptId
    }, onMessage, onError, onEnd)
  }

  async function sendMessage(url, payload, onMessage, onError, onEnd) {
    let accumulatedMessage = ""

    return sendEventSourceMessage({
      url,
      headers: {
        Accept: "text/event-stream",
        "X-CSRF-Token": $("meta[name=csrf-token]").attr("content")
      },
      body: JSON.stringify(payload),
      onMessage: (streamedResponse) => {
        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }
        onMessage(accumulatedMessage)
      },
      onError: (streamedResponse) => {
        accumulatedMessage = streamedResponse?.error_text
        onError(accumulatedMessage)
      },
      onEnd
    })
  }

  return {
    run,
    canNext() {
      if (params?.mentalMapRequired) {
        return mentalMapHistory.reduce((all, val) => all && val.done, true)
      }
      return true
    }
  }
}
