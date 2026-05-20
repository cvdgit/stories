import "./style.css";
import RecordingPanel from "./RecordingPanel";

function PresentationItemHandler(processUserResponseCallback) {

  let isRecording = false;

  return {
    isRecording() {
      return isRecording;
    },
    /**
     * @param {HTMLElement} container
     */
    handle(container) {
      if (isRecording) {
        return;
      }

      isRecording = true;

      const recordingPanel = new RecordingPanel(
        async (userResponse) => {

          const abort = await processUserResponseCallback(userResponse);

          if (abort === true) {
            isRecording = false;
            recordingPanel.destroy();
            return;
          }

          /*container.querySelectorAll(`[data-img-id]`).forEach(el => el._tippy && el._tippy.enable());*/

          isRecording = false;
          recordingPanel.destroy();
        },
        () => {
          container.querySelectorAll(`[data-img-id]`).forEach(el => el._tippy && el._tippy.disable());
        }
      );

      container.appendChild(recordingPanel.render());
      recordingPanel.startRecording();
    }
  }
}

export default PresentationItemHandler;
