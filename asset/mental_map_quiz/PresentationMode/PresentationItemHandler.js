import RecordingPanel from "./RecordingPanel";
import {userResponseChecker} from "../lib/userResponseProcessChain";

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
            historyItem.hiding = 0;
            historyItem.target = 0;
          }

          saveUserHistoryHandler({
            image_fragment_id: image.id,
            overall_similarity: Number(json.similarity_percentage),
            text_hiding_percentage: 0,
            text_target_percentage: 0,
            content: image.text,
            user_response: userResponse,
            api_response: JSON.stringify(json),
            payload: json,
            all_important_words_included: importantWordsPassed
          });

          if (done) {
            const imgElem = container.querySelector(`.zoom-container [data-img-id='${image.id}']`);
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
