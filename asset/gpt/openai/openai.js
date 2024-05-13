import {setAbortController} from "./abortController";
import {uuidv4} from "../utils";
import {applyPatch} from "fast-json-patch";
import {fetchEventSource} from "@microsoft/fetch-event-source";

export const fetchBaseUrl = (baseUrl) =>
  baseUrl || "https://api.openai.com/v1/chat/completions";

export const fetchHeaders = (options = {}) => {
  const {organizationId, apiKey} = options;
  return {
    Authorization: "Bearer " + apiKey,
    "Content-Type": "application/json",
    ...(organizationId && {"OpenAI-Organization": organizationId}),
  };
};

export const fetchBody = ({options = {}, messages = []}) => {
  const {top_p, n, max_tokens, temperature, model, stream} = options;
  return {
    input: {
      messages,
      stream,
      n: 1,
      ...(model && {model}),
      ...(temperature && {temperature}),
      ...(max_tokens && {max_tokens}),
      ...(top_p && {top_p}),
      ...(n && {n}),
    },
    config: {
      metadata: {
        conversation_id: uuidv4()
      }
    },
    include_names: [],
  };
};

export const fetchAction = async ({
                                    method = "POST",
                                    messages = [],
                                    options = {},
                                    signal,
                                    onError,
                                    onMessage,
                                    onEnd
                                  }) => {
  const {baseUrl, ...rest} = options;
  const url = fetchBaseUrl(baseUrl);
  const headers = fetchHeaders({...rest});
  const body = JSON.stringify(fetchBody({messages, options}));

  let streamedResponse = {}
  const response = await fetchEventSource(url, {
    method,
    headers,
    body,
    openWhenHidden: true,
    signal,
    onerror: onError,
    onmessage(msg) {

      if (msg.event === "end") {
        if (typeof onEnd === "function") {
          onEnd(streamedResponse.streamed_output.join(""))
        }
        return;
      }

      if (msg.event === "data" && msg.data) {
        const chunk = JSON.parse(msg.data);
        streamedResponse = applyPatch(
          streamedResponse,
          chunk.ops,
        ).newDocument;
        onMessage(streamedResponse.streamed_output.join(""))
      }

      if (msg.event === "error" && msg.data) {
        onError(msg.data)
      }
    },
  })

  return response;
};

export const fetchStream = async ({
                                    options,
                                    messages,
                                    onMessage,
                                    onEnd,
                                    onError,
                                  }) => {
  let answer = "";
  const {controller, signal} = setAbortController();

  const result = await fetchAction({options, messages, signal, onEnd, onMessage, onError})
    .catch((error) => {
      onError && onError(error, controller);
    })

  if (!result) return;
  if (!result.ok) {
    const error = await result.json();
    onError && onError(error);
    return;
  }
};
