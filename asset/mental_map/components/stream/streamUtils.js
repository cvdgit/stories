import {fetchEventSource} from "@microsoft/fetch-event-source";
import {applyPatch} from "fast-json-patch";

async function streamMessage(url, payload, onMessage, onEnd, onError) {
  let streamedResponse = {};
  let accumulatedMessage = '';
  return await fetchEventSource(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'text/event-stream',
      'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
    },
    body: JSON.stringify(payload),
    openWhenHidden: true,
    onerror(err) {
      console.error(err);
      onError && onError(err);
    },
    onmessage(msg) {
      if (msg.event === "end") {
        console.log("end")
        onEnd && onEnd(accumulatedMessage)
        return;
      }
      if (msg.event === "data" && msg.data) {
        const chunk = JSON.parse(msg.data);
        streamedResponse = applyPatch(
          streamedResponse,
          chunk.ops,
        ).newDocument;

        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }

        onMessage && onMessage(accumulatedMessage);
      }

      if (msg.event === "error" && msg.data) {
        onError && onError(msg.data)
      }
    },
    onclose: () => {
      console.log('close')
    }
  });
}

export async function streamFragmentTitle(fragmentText, onMessage, onEnd) {
  return streamMessage(
    '/admin/index.php?r=gpt/story/fragment-title',
    {text: fragmentText},
    message => onMessage(message),
    onEnd
  );
}
