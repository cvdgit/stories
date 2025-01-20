import sendEventSourceMessage from "../../app/sendEventSourceMessage";

export default async function sendMessage(url, payload, onMessage, onError, onEnd) {
  let accumulatedMessage = ''
  return sendEventSourceMessage({
    url,
    headers: {
      Accept: 'text/event-stream',
      'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
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
