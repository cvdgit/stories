import RecordingPanel from "./RecordingPanel";
import {userResponseChecker} from "../lib/userResponseProcessChain";
import MapImageStatus from "../components/MapImageStatus";

function PresentationItemHandler(container, voiceResponse, {threshold, promptId}, saveUserHistoryHandler, history) {

  let isRecording = false;

  return {

    handle(image) {
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
            historyItem.allTextClosed = Number(json.similarity_percentage);
            historyItem.hiding = 0;
            historyItem.target = 0;
          }

          const historyResponse = await saveUserHistoryHandler({
            image_fragment_id: image.id,
            overall_similarity: Number(json.similarity_percentage),
            text_hiding_percentage: 0,
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

          const imgElem = container.querySelector(`.zoom-container [data-img-id='${image.id}']`);
          if (imgElem) {
            MapImageStatus.update(imgElem.querySelector('.map-user-status'), {
              hiding: historyItem.allTextClosed,
              seconds: historyItem.seconds,
              hidingPrev: historyItem.allTextClosedPrev,
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
