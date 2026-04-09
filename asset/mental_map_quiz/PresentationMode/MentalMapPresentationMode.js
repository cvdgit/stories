import './style.css';
import MentalMapImage from "../components/MentalMapImage";
import RecordingPanel from "./RecordingPanel";
import {userResponseChecker} from "../lib/userResponseProcessChain";

function createHideImagesButton(zoomWrap) {
  const hideBtn = document.createElement('button');
  hideBtn.classList.add('btn', 'btn-small', 'mental-map-hide-btn');
  hideBtn.textContent = 'Скрыть';
  hideBtn.addEventListener('click', e => {
    $(e.target).toggleClass('img-hide');
    if ($(e.target).hasClass('img-hide')) {
      $(zoomWrap).find('.mental-map-img .map-img').each((i, el) => $(el).css({opacity: '0'}));
      $(zoomWrap).find('.mental-map-img').each((i, el) => {
        $(el).append(`<span class="mental-map-point"></span>`);
      });
      $(e.target).text('Показать');
    } else {
      $(zoomWrap).find('.mental-map-img span').remove();
      $(zoomWrap).find('.mental-map-img .map-img').each((i, el) => $(el).css({opacity: '1'}));
      $(e.target).text('Скрыть');
    }
  });
  return hideBtn;
}

function initPanZoom(element, mapWidth, mapHeight, containerOffsetWidth, containerOffsetHeight) {
  let initialZoom = 0.8
  const containerWidth = containerOffsetWidth;
  const containerHeight = containerOffsetHeight;

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

/**
 * @param {HTMLElement} container
 * @param param1
 * @param {string} param1.mapUrl
 * @param {string} param1.mapWidth
 * @param {string} param1.mapHeight
 * @param {[]} param1.images
 * @param {string|null} param1.promptId
 * @param {Number} param1.threshold
 * @param {VoiceResponse} voiceResponse
 * @param {function(object): Promise} saveUserHistoryHandler
 * @param {[]} history
 * @return {HTMLDivElement|{getElement(): HTMLDivElement, destroy()}}
 * @constructor
 */
function MentalMapPresentationMode(container, {
  mapUrl,
  mapWidth,
  mapHeight,
  images,
  promptId,
  threshold,
}, voiceResponse, saveUserHistoryHandler, history) {

  const zoomContainer = document.createElement('div');
  zoomContainer.classList.add('zoom-container');
  container.appendChild(zoomContainer);

  const header = document.createElement('div');
  header.classList.add('presentation-map-header');
  header.innerHTML = `<h2>Пересказ презентации</h2><p>Нажмите на выделенную область или изображение и перескажи текст с подсказки</p>`;
  container.querySelector('.zoom-container').appendChild(
    header
  );

  let zoom;
  let isRecording = false;

  const zoomWrap = MentalMapImage(
    mapUrl,
    mapWidth,
    mapHeight,
    images,
    image => {

      if (isRecording) {
        return;
      }

      isRecording = true;

      const recordingPanelElement = new RecordingPanel(
        voiceResponse,
        async userResponse => {

          container.querySelectorAll(`[data-img-id]`).forEach(el => el._tippy.enable());

          if (!userResponse) {
            isRecording = false;
            container.querySelector('.fragment-recording-wrap').remove();
            return;
          }

          const response = await userResponseChecker(
            image.text,
            userResponse,
            threshold,
            promptId || image.promptId
          );

          const json = window.processOutputAsJson(response);

          const val = Number(json.similarity_percentage);

          let importantWordsPassed = true;
          if (json.all_important_words_included !== undefined) {
            importantWordsPassed = Boolean(json.all_important_words_included);
          }

          const done = val >= threshold && importantWordsPassed;

          const historyItem = history.find(h => h.id === image.id);
          if (historyItem) {
            historyItem.done = done;
            historyItem.all = Number(json.similarity_percentage);
            historyItem.hiding = 100;
            historyItem.target = 100;
          }

          saveUserHistoryHandler({
            image_fragment_id: image.id,
            overall_similarity: Number(json.similarity_percentage),
            text_hiding_percentage: 100,
            text_target_percentage: 100,
            content: image.text,
            user_response: userResponse,
            api_response: JSON.stringify(json),
            payload: json,
            all_important_words_included: importantWordsPassed
          });

          if (done) {
            const imgElem = zoomContainer.querySelector(`[data-img-id='${image.id}']`);
            imgElem.classList.add('fragment-item-done');
            if (image.makeTransparent) {
              imgElem.classList.add('fragment-transparent');
            }
          }

          isRecording = false;
          container.querySelector('.fragment-recording-wrap').remove();
        },
        () => {
          container.querySelectorAll(`[data-img-id]`).forEach(el => el._tippy.disable());
        }
      );

      container.querySelector('.zoom-container')
        .appendChild(
          recordingPanelElement
        );
    },
    [],
    () => {
    },
    ({id, makeTransparent}, mapImgWrap) => {

      const historyItem = history.find(h => h.id === id);
      if (!historyItem) {
        return;
      }

      if (historyItem.done) {
        mapImgWrap.classList.add('fragment-item-done');
        if (makeTransparent) {
          mapImgWrap.classList.add('fragment-transparent');
        }

        /*mapImgWrap.appendChild(
          MapImageStatus.render({
            hiding: historyItem.hiding,
            seconds: historyItem.seconds,
            hidingPrev: historyItem.hidingPrev,
          })
        );*/
      }
    },
    false
  );

  zoomContainer.appendChild(zoomWrap);
  zoomContainer.appendChild(createHideImagesButton(zoomWrap));

  zoom = initPanZoom(
    zoomWrap,
    Number(mapWidth.replace(/\D+/, '')),
    Number(mapHeight.replace(/\D+/, '')),
    container.offsetWidth,
    container.offsetHeight
  );

  container.parentElement.addEventListener('wheel', zoom.zoomWithWheel)

  return {
    getElement() {
      return zoomContainer;
    },
    destroy() {

    }
  }
}

export default MentalMapPresentationMode;
