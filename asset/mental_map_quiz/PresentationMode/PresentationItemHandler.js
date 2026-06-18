import RecordingPanel from "./RecordingPanel";
import {userResponseChecker} from "../lib/userResponseProcessChain";
import MapImageStatus from "../components/MapImageStatus";
import {stripTags} from "../common";
import tippy from "tippy.js";
import 'tippy.js/dist/tippy.css';
import {createNotify} from "../components/utils";

function PresentationItemHandler(container, voiceResponse, {threshold, promptId, presentationPromptEdit}, saveUserHistoryHandler, history) {

  let isRecording = false;

  return {
    isRecording() {
      return isRecording;
    },
    handle(image, historyItem, onHistoryChangeHandler) {
      if (isRecording) {
        return;
      }

      isRecording = true;

      return RecordingPanel(
        voiceResponse,
        async userResponse => {

          container.querySelectorAll(`[data-img-id]`).forEach(el => el._tippy && el._tippy.enable());

          if (!userResponse) {
            isRecording = false;
            container.querySelector('.fragment-recording-wrap').remove();
            return;
          }

          let response
          try {
            response = await userResponseChecker(
              image.text,
              userResponse,
              threshold,
              promptId || image.promptId
            )
          } catch (ex) {

            document.querySelector('.mental-map').appendChild(
              createNotify(ex.message)
            )

            isRecording = false;
            container.querySelector('.fragment-recording-wrap').remove();
            return
          }

          const json = window.processOutputAsJson(response);

          const val = Number(json.similarity_percentage);

          let importantWordsPassed = true;
          if (json.all_important_words_included !== undefined) {
            importantWordsPassed = Boolean(json.all_important_words_included);
          }

          const done = val >= threshold && importantWordsPassed;

          if (!historyItem) {
            historyItem = history.find(h => h.id === image.id);
          }

          if (historyItem) {
            historyItem.done = done;
            historyItem.all = Number(json.similarity_percentage);
            historyItem.allTextClosed = Number(json.similarity_percentage);
            historyItem.hiding = 100;
            historyItem.target = 0;
          }

          const historyResponse = await saveUserHistoryHandler({
            image_fragment_id: image.id,
            overall_similarity: Number(json.similarity_percentage),
            text_hiding_percentage: 100,
            text_target_percentage: 0,
            content: image.text,
            user_response: userResponse,
            api_response: JSON.stringify(json),
            payload: json,
            all_important_words_included: importantWordsPassed,
            all_hiding_percentage: Number(json.similarity_percentage),
          });

          const {history: responseHistoryItem} = historyResponse;
          historyItem.all = responseHistoryItem.all;
          historyItem.hiding = responseHistoryItem.hiding;
          historyItem.target = responseHistoryItem.target;
          historyItem.done = responseHistoryItem.done;
          historyItem.seconds = responseHistoryItem.seconds;
          historyItem.allTextClosed = responseHistoryItem.allTextClosed;
          historyItem.allTextClosedPrev = responseHistoryItem.allTextClosedPrev;
          historyItem.result = '';

          if (typeof onHistoryChangeHandler === 'function') {
            onHistoryChangeHandler(historyItem)
          }

          const imgElem = container.querySelector(`[data-img-id='${image.id}']`);
          if (imgElem) {
            MapImageStatus.updateAllClosedValue(imgElem.querySelector('.map-user-status'), {
              hiding: historyItem.allTextClosed,
              seconds: historyItem.seconds,
              hidingPrev: historyItem.allTextClosedPrev,
              /*statClickHandler: () => {

                const tip = document.createElement('div');
                tip.innerHTML = '<div class="tip-content"><div class="tip-content-header"><b>Результат:</b></div><div class="tip-content-body"><div style="display: flex; flex-direction: row; gap: 10px; align-items: center"><img width="30px" src="/img/loading.gif" alt="loading" /> запрос...</div></div></div>';

                if (historyItem.result) {
                  tip.innerHTML = `
<div class="tip-content">
<div class="tip-content-header"><b>Результат:</b> ${presentationPromptEdit ? `<a target="_blank" href="${presentationPromptEdit}">Edit prompt</a>` : ''}</div>
<div class="tip-content-body">${historyItem.result}</div>
</div>
`;
                } else {
                  window.sendStreamMessage(
                    `/admin/index.php?r=gpt/mental-map/fragment-result`,
                    {
                      text: stripTags(image.text).trim(),
                      userResponse: json.user_response
                    },
                    (message) => {
                      instance.setContent(`<div class="tip-content"><div class="tip-content-header"><b>Результат:</b> ${presentationPromptEdit ? `<a target="_blank" href="${presentationPromptEdit}">Edit prompt</a>` : ''}</div><div class="tip-content-body">${message}</div></div>`);
                    },
                    (message) => {
                      historyItem.result = message;
                    }
                  );
                }

                const instance = tippy(
                  imgElem,
                  {
                    trigger: 'manual',
                    content: tip,
                    allowHTML: true,
                    interactive: true,
                    appendTo: document.body,
                    maxWidth: 'none',
                  }
                );

                instance.show();
              }*/
            });
          }

          if (done && imgElem) {
            imgElem.classList.add('fragment-item-done');
            if (image.makeTransparent) {
              imgElem.classList.add('fragment-transparent');
            }
          }

          isRecording = false;
          container.querySelector('.fragment-recording-wrap').remove();
        },
        () => {
          container.querySelectorAll(`[data-img-id]`).forEach(el => el._tippy && el._tippy.disable());
        }
      );
    }
  }
}

export default PresentationItemHandler;
