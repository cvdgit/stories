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
import {diffRetelling} from "./lib/calcSimilarity";
import MapImageStatus from "./components/MapImageStatus";
import SecondTimer from "./components/SecondTimer";
import MentalMapPresentationMode from "./PresentationMode";
import FragmentState from "./FragmentState";
import PresentationItemHandler from "./PresentationMode/PresentationItemHandler";
import {showDialogHandler, hideDialogHandler} from "./itemClickHandlers/dialogHandlers";
import startRecording from "./itemClickHandlers/startRecording";
import createRetellingContent from "./itemClickHandlers/createRetellingContent";
import startRetelling from "./itemClickHandlers/startRetelling";
import saveUserResult from "./itemClickHandlers/saveUserResult";

/**
 * @param {HTMLElement} element
 * @param {Reveal|undefined} deck
 * @param params
 * @returns {{run: ((function(): Promise<void>)|*)}}
 * @constructor
 */
export default function MentalMap(element, deck, params, microphoneChecker) {

  this.element = element;
  let texts = []
  let mentalMapHistory = []
  let mentalMapId

  params = params || {}
  params.slide_id = params.slide_id || (deck ? Number($(deck.getCurrentSlide()).attr('data-id')) : null)

  let mentalMapUserProgress = 0
  let treeViewInstance;

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

  const loader = document.createElement('div')
  loader.classList.add('content-loader-wrap')
  loader.innerHTML = `<div style="display: flex; flex-direction: row; gap: 20px; align-items: center">Загрузка ментальной карты... <img width="50" src="/img/loading.gif" alt="loading"></div>`
  this.element.appendChild(loader)

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

  function showMentalMapHandler(zoomWrap, closeMentalMapHandler, fastMode, fastModeChangeHandler, presentationModeChangeHandler) {
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

    const fastWrapEl = document.createElement('div');
    fastWrapEl.classList.add('mental-map-fast-wrap');

    fastWrapEl.appendChild(closeBtn);
    fastWrapEl.appendChild(hideBtn);

    const fastBox = document.createElement('label');
    fastBox.innerHTML = `Быстрый режим<input type="checkbox" ${fastMode ? 'checked' : ''}>`;
    fastBox.querySelector('input[type=checkbox]')
      .addEventListener('click', fastModeChangeHandler);
    fastWrapEl.appendChild(fastBox);

    const presentationBox = document.createElement('label');
    presentationBox.innerHTML = `Режим презентации<input type="checkbox"></label>`;
    presentationBox.querySelector('input[type=checkbox]')
      .addEventListener('click', presentationModeChangeHandler);
    fastWrapEl.appendChild(presentationBox);

    zoomContainer.appendChild(fastWrapEl);

    return zoomContainer;
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

  const fragmentState = new FragmentState(params.mentalMapId);

  const removePunctuation = text => text.replace(/[!"#$%&'()*+,-./:;<=>?@[\]^_`{|}–«»~]/g, '').replace(/\s{2,}/g, " ")

  function mapImageClickHandler({
                                  image,
                                  texts,
                                  historyItem,
                                  rewritePrompt,
                                  threshold,
                                  dialogHideHandler,
                                  fastMode,
                                  hideFragmentText,
                                  settingsPromptId,
                                  detailParams
                                }) {

    const text = texts.find(t => t.id === image.id);

    let hideText = hideFragmentText;
    if (image.textState === 'hide') {
      hideText = true;
    }
    if (image.textState === 'show') {
      hideText = false;
    }

    if (!hideText) {
      const prevWords = fragmentState.get(
        image.id,
        image.text
      );
      if (prevWords) {
        text.words = prevWords;
      }
    }

    const timer = new SecondTimer();

    const detailContainer = DetailContent({
      image,
      text,
      historyItem,
      rewritePrompt,
      itemClickHandler: (recordingWrap) => {
        if (voiceResponse.getStatus()) {
          voiceResponse.stop();
          timer.stop();
          const voiceLang = langStore.fromStore($(recordingWrap).find("#voice-lang option:selected").val())
          startRecording(
            voiceResponse,
            recordingWrap.querySelector('#start-recording'),
            voiceLang,
            stripTags(text.text),
            false,
            threshold
          );
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
        recordingWrap.querySelector('#target-text-percent').innerText = calcTargetTextPercent(text) + '%';
      },
      diffClickHandler: () => {
        $(detailContainer).append(createDiffContent({
          text: stripTags(image.text),
          userResponse: detailContainer.querySelector('#result_span').innerText.trim()
        }))
      },
      hideText,
      detailParams,
      stopRecordingHandler: (recordingWrap) => {
        if (!voiceResponse.getStatus()) {
          return;
        }
        voiceResponse.stop();
        timer.stop();
        const voiceLang = langStore.fromStore($(recordingWrap).find("#voice-lang option:selected").val());
        startRecording(
          voiceResponse,
          recordingWrap.querySelector('#start-recording'),
          voiceLang,
          stripTags(text.text),
          false,
          threshold
        );
      },
      onWordsChanged: (args) => {
        fragmentState.set(
          image.id,
          image.text,
          text.words
        );
      }
    })
    const dialog = new InnerDialog($(container), {title: 'Перескажите текст', content: detailContainer});
    dialog.show(wrapper => {
      showDialogHandler(deck);

      $(wrapper).on('paste', (e) => {
        e.preventDefault()
        return false
      })

      $(wrapper).find('.bs-tooltip').tooltip();

      $(wrapper).find(`#voice-lang`).val(langStore.get())

      wrapper.querySelector('#start-recording').addEventListener('click', e => {

        if (!canRecording(text)) {
          return
        }

        if (voiceResponse.getStatus()) {
          timer.stop();
        } else {
          timer.start(wrapper.querySelector('.fragment-timer'));
        }

        if (!voiceResponse.getStatus()) {
          ['#result_span', '#final_span', '#interim_span'].map(q => {
            wrapper.querySelector(q).innerHTML = ''
            wrapper.querySelector('#start-retelling-wrap').style.display = 'none'
          });
        }
        wrapper.querySelector('.content-diff').style.display = 'none';

        const voiceLang = langStore.fromStore($(wrapper).find("#voice-lang option:selected").val());
        startRecording(
          voiceResponse,
          e.target,
          voiceLang,
          stripTags(text.text),
          true,
          threshold,
          () => {
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
              threshold,
              payload: json,
              location: params.location,
              seconds: timer.getTimerSeconds()
            }).then(response => {

              if (deck) {
                if (deck.hasPlugin('stat')) {
                  const statPlugin = deck.getPlugin('stat');
                  statPlugin.sendStat({slideId: params.slide_id});
                }
              }

              /*if (response && response?.success) {
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
              }*/
            });

            historyItem.all = Number(json.overall_similarity);
            historyItem.hiding = textHidingPercentage;
            historyItem.target = textTargetPercentage;
            historyItem.done = Number(json.overall_similarity) >= threshold;
            historyItem.seconds = timer.getTimerSeconds();

            wrapper.querySelector('.image-item > .result-item').remove()
            wrapper.querySelector('.image-item').appendChild(FragmentResultElement(historyItem))

            if (fastMode) {
              if (historyItem.done) {
                dialog.hide()
              }
            }
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
    const timer = new SecondTimer();
    const detailContainer = DetailContentQuestions({
      image,
      questionItem,
      historyItem,
      rewritePrompt
    })
    const dialog = new InnerDialog($(container), {title: 'Перескажите текст отвечая на вопросы', content: detailContainer});
    dialog.show(wrapper => {
      showDialogHandler(deck);

      $(wrapper).on('paste', (e) => {
        e.preventDefault()
        return false
      })

      $(wrapper).find('.bs-tooltip').tooltip()

      $(wrapper).find(`#voice-lang`).val(langStore.get())

      wrapper.querySelector('#start-recording').addEventListener('click', e => {

        if (voiceResponse.getStatus()) {
          timer.stop();
        } else {
          timer.start(wrapper.querySelector('.fragment-timer'));
        }

        if (!voiceResponse.getStatus()) {
          ['#result_span', '#final_span', '#interim_span'].map(q => {
            wrapper.querySelector(q).innerHTML = ''
            wrapper.querySelector('#start-retelling-wrap').style.display = 'none'
          });
        }

        const voiceLang = langStore.fromStore($(wrapper).find("#voice-lang option:selected").val());
        startRecording(
          voiceResponse,
          e.target,
          voiceLang,
          stripTags(image.text),
          true,
          threshold,
          () => {
            if (fastMode) {
              setTimeout(() => {
                wrapper.querySelector('#start-retelling').click()
              }, 100)
            }
          }
        )
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
              threshold,
              payload: json,
              location: params.location,
              seconds: timer.getTimerSeconds()
            }).then(response => {

              if (deck) {
                if (deck.hasPlugin('stat')) {
                  const statPlugin = deck.getPlugin('stat');
                  statPlugin.sendStat({slideId: params.slide_id});
                }
              }

              /*if (response && response?.success) {
                historyItem.all = response.history.all
                //historyItem.hiding = response.history.hiding
                //historyItem.target = response.history.target
                historyItem.done = response.history.done

                // wrapper.querySelector('.result-item-value').innerHTML = `${val}% (${textHidingPercentage}% / ${textTargetPercentage}%)`
                wrapper.querySelector('.image-item > .result-item').remove()
                wrapper.querySelector('.image-item').appendChild(FragmentResultQuestionsElement(historyItem))
              }*/
            })

            historyItem.all = Number(json.overall_similarity);
            historyItem.hiding = 0;
            historyItem.target = 0;
            historyItem.done = Number(json.overall_similarity) >= threshold;
            historyItem.seconds = timer.getTimerSeconds();

            wrapper.querySelector('.image-item > .result-item').remove()
            wrapper.querySelector('.image-item').appendChild(FragmentResultElement(historyItem))

            if (fastMode) {
              if (historyItem.done) {
                dialog.hide()
              }
            }
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

  function createNoMicrophoneElement(message) {
    const noMicroElem = document.createElement('div')
    noMicroElem.classList.add('microphone-error')
    noMicroElem.innerHTML = `<div style="padding: 20px; display: flex; flex-direction: column; row-gap: 10px; border-radius: 20px; background-color: RGBA(220, 53, 69, 1); color: white"><div>Микрофон недоступен:</div><div>${message}</div></div>`
    return noMicroElem
  }

  const run = async () => {

    let responseJson
    try {

      responseJson = await params.init()

      if (microphoneChecker) {
        microphoneChecker
          .check()
          .catch(error => this.element.appendChild(createNoMicrophoneElement(error.name + ': ' + error.message)));
      }

    } catch (ex) {
      container.innerText = ex.message
      loader.remove()
      this.element.appendChild(container)
      return
    }

    const {mentalMap: json, history, rewritePrompt, threshold, userProgress} = responseJson
    mentalMapUserProgress = userProgress
    mentalMapId = json.id
    mentalMapHistory = history

    const saveHistoryParams = {
      story_id: params?.story_id,
      slide_id: params?.slide_id,
      mental_map_id: params.mentalMapId,
      repetition_mode: repetitionMode,
      threshold,
      location: params.location
    };

    const {treeView} = json
    if (treeView) {

      const treeTexts = TreeView
        .flatten(json.treeData)
        .map(image => createWordItem(image.description || '', image.id));

      treeViewInstance = new TreeView({
        id: json.id,
        name: json.name,
        tree: json.treeData,
        infoText: json.infoText,
        history,
        params: saveHistoryParams,
        settings: json.settings || {},
        onMentalMapChange: progress => {
          mentalMapUserProgress = progress;
          if (deck) {
            if (deck.hasPlugin('stat')) {
              const statPlugin = deck.getPlugin('stat');
              statPlugin.sendStat({slideId: params.slide_id});
            }
          }
        },
        deck,
        itemClickHandler: (item) => {

          const image = {
            ...item,
            textState: 'show',
          };

          const historyItem = history.find(h => h.id === item.id);

          mapImageClickHandler({
            image,
            texts: treeTexts,
            historyItem,
            rewritePrompt,
            threshold,
            dialogHideHandler: () => {

              const imgElem = container.querySelector(`[data-node-id='${image.id}']`);
              imgElem.classList.remove('node-row-done', 'node-row-fail');

              if (historyItem.done) {
                imgElem.classList.add('node-row-done');
                MapImageStatus.update(imgElem.querySelector('.map-user-status'), {
                  hiding: historyItem.hiding,
                  seconds: historyItem.seconds,
                  hidingPrev: historyItem.hidingPrev,
                });
              }

              fragmentDialogHideHandler(image, historyItem);
            },
            fastMode: true,
            hideFragmentText: false,
            settingsPromptId: json.settings?.promptId
          });

        }
      }, new VoiceResponse(new MissingWordsRecognition({
        getRecordingLang() {
          return (json.settings || {}).recognitionLang || 'ru-RU';
        }
      })));

      loader.remove()

      container.appendChild(
        treeViewInstance.getElement()
      );
      this.element.appendChild(container);

      $('[data-toggle="tooltip"]', this.element).tooltip({
        container: 'body'
      });

      return
    }

    const {mapTypeIsMentalMapQuestions, questions} = json;
    const mapQuestions = new MentalMapQuestions({typeIsMentalMapQuestions: mapTypeIsMentalMapQuestions, questions});

    let fastMode = true;
    function fastModeChangeHandler(e) {
      fastMode = e.target.checked;
    }

    let presentationMode = false;
    const presentationModeChangeHandler = ({target}) => {
      presentationMode = target.checked;
      const elements = this.element.querySelectorAll('.zoom-wrap [data-img-id]');
      elements.forEach(elem => {
        const elemId = elem.dataset.imgId;
        const historyItem = history.find(h => h.id === elemId);
        if (presentationMode) {
          MapImageStatus.update(elem.querySelector('.map-user-status'), {
            hiding: historyItem.all,
            seconds: historyItem.seconds,
            hidingPrev: historyItem.allPrev,
          });
          return;
        }
        MapImageStatus.update(elem.querySelector('.map-user-status'), {
          hiding: historyItem.hiding,
          seconds: historyItem.seconds,
          hidingPrev: historyItem.hidingPrev,
        });
      });
    }

    texts = json.map.images.map(image => createWordItem(image.text, image.id));

    const imageFirst = Boolean(json.settings?.imageFirst);
    const hideTooltip = Boolean(json.settings?.hideTooltip);
    const hideFragmentText = Boolean(json.settings?.hideText);
    const settingsPromptId = json.settings?.promptId;

    const {settings} = json;
    const isPresentationMode = Boolean(settings?.presentationMode);
    if (isPresentationMode) {

      loader.remove();
      treeViewInstance = MentalMapPresentationMode(
        this.element,
        {
          mapUrl: json.map.url,
          mapWidth: `${json.map.width}px`,
          mapHeight: `${json.map.height}px`,
          images: json.map.images,
          promptId: settings?.promptId,
          threshold
        },
        new VoiceResponse(new MissingWordsRecognition({
          getRecordingLang() {
            return (json.settings || {}).recognitionLang || 'ru-RU';
          }
        })),
        (payload) => {
          return saveUserResult({
            ...saveHistoryParams,
            ...payload
          }).then(response => {
            if (response.success) {
              if (deck) {
                if (deck.hasPlugin('stat')) {
                  const statPlugin = deck.getPlugin('stat');
                  statPlugin.sendStat({slideId: params.slide_id});
                }
              }
            }

            /*if (historyIsDone(history)) {
              const content = createFinishContent(
                history,
                texts,
                mapQuestions.typeIsMentalMapQuestions(),
                () => restartHandler(mentalMapId)
              );
              $(this.element).append(content)
              if (imageFirst) {
                element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
              }
            }*/
          })
        },
        history
      );

      $('[data-toggle="tooltip"]', this.element).tooltip({
        container: 'body'
      });

      /*if (historyIsDone(history)) {
        const content = createFinishContent(
          history,
          texts,
          mapQuestions.typeIsMentalMapQuestions(),
          () => restartHandler(mentalMapId)
        );
        $(this.element).append(content)
        if (imageFirst) {
          element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
        }
      }*/

      return;
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
      hideDialogHandler(deck, voiceResponse);

      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
      }

      const el = container.querySelector(`[data-image-fragment-id='${image.id}']`);
      if (el) {
        el.querySelector('.image-item > .result-item').remove();
        el.querySelector('.image-item').appendChild(FragmentResultElement(historyItem));
        $(el.querySelector('.result-item')).tooltip();
      }

      if (repetitionMode) {
        const done = mentalMapHistory.reduce((all, val) => all && val.done, true)
        if (done) {
          const content = createFinishRepetitionContent()
          $(container).parents('.mental-map').append(content)
          finishRepetition(params.mentalMapId)
        }
      }

      if (/*getCourseMode &&*/ historyIsDone(history)) {
        const content = createFinishContent(history, texts, mapQuestions.typeIsMentalMapQuestions(), () => restartHandler(mentalMapId))
        $(container).parents('.mental-map').append(content)
      }
    }

    container.appendChild(AllTexts(texts, json.map.images, history, (image, detailParams) => {

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
        settingsPromptId,
        detailParams
      })
    }, mapQuestions.typeIsMentalMapQuestions()))

    const toolbar = document.createElement('div')
    toolbar.classList.add('mental-map-toolbar')

    const mentalMapBtn = document.createElement('button')
    mentalMapBtn.classList.add('btn', 'btn-small', 'mental-map-btn')
    mentalMapBtn.textContent = 'Ментальная карта'
    let zoom;

    const presentationHandler = new PresentationItemHandler(
      this.element,
      new VoiceResponse(new MissingWordsRecognition({
        getRecordingLang() {
          return (json.settings || {}).recognitionLang || 'ru-RU';
        }
      })),
      {promptId: settings?.promptId, threshold},
      (payload) => {
        return saveUserResult({
          ...saveHistoryParams,
          ...payload
        }).then(response => {
          if (response.success) {
            if (deck) {
              if (deck.hasPlugin('stat')) {
                const statPlugin = deck.getPlugin('stat');
                statPlugin.sendStat({slideId: params.slide_id});
              }
            }
          }

          if (historyIsDone(history)) {
            const content = createFinishContent(
              history,
              texts,
              mapQuestions.typeIsMentalMapQuestions(),
              () => restartHandler(mentalMapId)
            );
            $(this.element).append(content)
            if (imageFirst) {
              element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
            }
          }
        })
      },
      history
    );

    mentalMapBtn.addEventListener('click', (e) => {

      const zoomWrap = MentalMapImage(
        json.map.url,
        `${json.map.width}px`,
        `${json.map.height}px`,
        json.map.images,
        (image, detailParams) => {
          element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)

          const historyItem = history.find(h => h.id === image.id);
          if (presentationMode) {
            this.element.querySelector('.zoom-container')
              .appendChild(
                presentationHandler.handle(image)
              );
            return;
          }

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
                  MapImageStatus.update(imgElem.querySelector('.map-user-status'), {
                    hiding: historyItem.hiding,
                    seconds: historyItem.seconds,
                    hidingPrev: historyItem.hidingPrev,
                  });
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
                MapImageStatus.update(imgElem.querySelector('.map-user-status'), {
                  hiding: presentationMode ? historyItem.all : historyItem.hiding,
                  seconds: historyItem.seconds,
                  hidingPrev: presentationMode ? historyItem.allPrev : historyItem.hidingPrev,
                });
              }
              fragmentDialogHideHandler(image, historyItem)
            },
            fastMode,
            hideFragmentText,
            settingsPromptId,
            detailParams
          })
        },
        mentalMapHistory,
        hideTooltipChecker,
        ({id, makeTransparent}, mapImgWrap) => {
          const historyItem = history.find(h => h.id === id);
          if (!historyItem) {
            return;
          }

          mapImgWrap.appendChild(
            MapImageStatus.render({
              hiding: presentationMode ? historyItem.all : historyItem.hiding,
              seconds: historyItem.seconds,
              hidingPrev: presentationMode ? historyItem.allPrev : historyItem.hidingPrev,
            })
          );

          if (!historyItem.done) {
            return;
          }
          mapImgWrap.classList.add('fragment-item-done');
          if (makeTransparent) {
            mapImgWrap.classList.add('fragment-transparent');
          }
        }
      );

      const zoomContainer = showMentalMapHandler(
        zoomWrap,
        () => {
          zoom.destroy()
          zoomContainer.remove()
          element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
        },
        fastMode,
        fastModeChangeHandler,
        presentationModeChangeHandler
      );

      this.element.appendChild(zoomContainer)

      $('.mental-map-img .map-img').tooltip()

      zoom = initPanZoom(zoomWrap, json.map.width, json.map.height);
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

    loader.remove()
    this.element.appendChild(toolbar)
    this.element.appendChild(container)

    $('.bs-tooltip').tooltip()

    if (imageFirst) {

      const zoomWrap = MentalMapImage(
        json.map.url,
        `${json.map.width}px`,
        `${json.map.height}px`,
        json.map.images,
        (image, detailParams) => {

          element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)

          const historyItem = history.find(h => h.id === image.id);
          if (presentationMode) {
            this.element.querySelector('.zoom-container')
              .appendChild(
                presentationHandler.handle(image)
              );
            return;
          }

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
                  MapImageStatus.update(imgElem.querySelector('.map-user-status'), {
                    hiding: historyItem.hiding,
                    seconds: historyItem.seconds,
                    hidingPrev: historyItem.hidingPrev,
                  });
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
                MapImageStatus.update(imgElem.querySelector('.map-user-status'), {
                  hiding: presentationMode ? historyItem.all : historyItem.hiding,
                  seconds: historyItem.seconds,
                  hidingPrev: presentationMode ? historyItem.allPrev : historyItem.hidingPrev,
                });
              }
              fragmentDialogHideHandler(image, historyItem)
            },
            fastMode,
            hideFragmentText,
            settingsPromptId,
            detailParams
          })
        },
        mentalMapHistory,
        hideTooltipChecker,
        ({id, makeTransparent}, mapImgWrap) => {
          const historyItem = history.find(h => h.id === id);
          if (!historyItem) {
            return;
          }

          mapImgWrap.appendChild(
            MapImageStatus.render({
              hiding: presentationMode ? historyItem.all : historyItem.hiding,
              seconds: historyItem.seconds,
              hidingPrev: presentationMode ? historyItem.allPrev : historyItem.hidingPrev,
            })
          );

          if (!historyItem.done) {
            return;
          }
          mapImgWrap.classList.add('fragment-item-done');
          if (makeTransparent) {
            mapImgWrap.classList.add('fragment-transparent');
          }
        }
      );

      const zoomContainer = showMentalMapHandler(
        zoomWrap,
        () => {
          zoom.destroy()
          zoomContainer.remove()
          element.parentElement.removeEventListener('wheel', zoom.zoomWithWheel)
        },
        fastMode,
        fastModeChangeHandler,
        presentationModeChangeHandler
      );

      this.element.appendChild(zoomContainer)

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
    },
    getUserProgress() {
      return mentalMapUserProgress
    },
    destroy() {
      if (treeViewInstance) {
        treeViewInstance.destroy()
      }
      if (voiceResponse.getStatus()) {
        voiceResponse.stop()
      }
    }
  }
}
