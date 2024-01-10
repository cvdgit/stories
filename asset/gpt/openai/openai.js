import {setAbortController} from "./abortController";
import {uuidv4} from "../utils";
import {applyPatch} from "fast-json-patch";

export async function* streamAsyncIterable(stream) {
  const reader = stream.getReader();
  try {
    while (true) {
      const {done, value} = await reader.read();
      if (done) {
        return;
      }
      yield value;
    }
  } finally {
    reader.releaseLock();
  }
}

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

export const throwError = async (response) => {
  if (!response.ok) {
    let errorPayload = null;
    try {
      errorPayload = await response.json();
      console.log(errorPayload);
    } catch (e) {
      // ignore
    }
  }
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
                                  }) => {
  const {baseUrl, ...rest} = options;
  const url = fetchBaseUrl(baseUrl);
  const headers = fetchHeaders({...rest});
  const body = JSON.stringify(fetchBody({messages, options}));
  const response = await fetch(url, {
    method,
    headers,
    body,
    signal,
  });
  return response;
};

export const fetchStream = async ({
                                    options,
                                    messages,
                                    onMessage,
                                    onEnd,
                                    onError,
                                    onStar,
                                  }) => {
  let answer = "";
  const {controller, signal} = setAbortController();

  const result = await fetchAction({options, messages, signal}).catch(
    (error) => {
      onError && onError(error, controller);
    }
  );

  if (!result) return;
  if (!result.ok) {
    const error = await result.json();
    onError && onError(error);
    return;
  }

  const reader = result.body.getReader();
  const decoder = new TextDecoder('utf-8');

  let hasStarted = false;
  let streamedResponse = {}
  while (true) {

    const {done, value} = await reader.read();
    if (done) {
      break;
    }

    let decoded = decoder.decode(value);

    decoded.split("\r\n\r\n").map(row => {
      if (!row.length) {
        return;
      }

      const [firstRow, secondRow] = row.split("\n");
      const [, event] = firstRow.split(" ")

      if (event && event.trim() === "data") {
        const data = secondRow.toString().replace(/^data: /, "")
        if (data) {
          const chunk = JSON.parse(data);

          streamedResponse = applyPatch(
            streamedResponse,
            chunk.ops,
          ).newDocument;

          if (Array.isArray(streamedResponse?.streamed_output)) {
            answer = streamedResponse.streamed_output.join("")
            onMessage && onMessage(answer, controller);
          }

          if (!hasStarted) {
            hasStarted = true;
            onStar && onStar(answer, controller);
          }
        }
      }
    })
  }

  /*
  const decoder = new TextDecoder();

  let hasStarted = false;
  let streamedResponse = {}

  for await (const chunk of streamAsyncIterable(result.body)) {
    const str = decoder.decode(chunk);
    console.log(str)
    const [firstRow, secondRow] = str.split("\n")
    const [, event] = firstRow.split(" ")

    if (event && event.trim() === "data") {
      const data = secondRow.toString().replace(/^data: /, "")
      if (data) {

        const chunkJson = JSON.parse(data)

        streamedResponse = applyPatch(
          streamedResponse,
          chunkJson.ops,
        ).newDocument;

        console.log(streamedResponse)

        if (Array.isArray(streamedResponse?.streamed_output)) {
          answer = streamedResponse.streamed_output.join("")

          onMessage && onMessage(answer, controller);
        }

        if (!hasStarted) {
          hasStarted = true;
          onStar && onStar(answer, controller);
        }
      }
    }

    //answer += str;
  }
   */

  await onEnd(answer);
};
