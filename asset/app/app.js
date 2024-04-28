import {fetchEventSource} from '@microsoft/fetch-event-source';
import {applyPatch} from "fast-json-patch";

window.sendEventSourceMessage = async function ({
                                                  url,
                                                  method = "POST",
                                                  headers = {},
                                                  body,
                                                  onEnd,
                                                  onMessage,
                                                  onError
                                                }) {
  let streamedResponse = {}
  return await fetchEventSource(url, {
    method,
    headers,
    body,
    openWhenHidden: true,
    onerror(err) {
      throw err;
    },
    onmessage(msg) {

      if (msg.event === "end") {
        if (typeof onEnd === "function") {
          onEnd()
        }
        return;
      }

      if (msg.event === "data" && msg.data) {
        const chunk = JSON.parse(msg.data);
        streamedResponse = applyPatch(
          streamedResponse,
          chunk.ops,
        ).newDocument;
        onMessage(streamedResponse)
      }

      if (msg.event === "error" && msg.data) {
        const chunk = JSON.parse(msg.data);
        if (chunk?.status_code) {
          onError(chunk)
        }
      }
    },
  });
}
