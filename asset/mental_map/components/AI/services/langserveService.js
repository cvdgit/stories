import { fetchEventSource } from '@microsoft/fetch-event-source';
import {applyPatch} from "fast-json-patch";

const LANGSERVE_URL = '/admin/index.php?r=gpt/mental-map/chat';

function buildLangServePayload(input) {
  return {
    input: {
      input,
      messages: [{ role: 'user', content: input }]
    },
    config: {},
    kwargs: {},
    stream: true
  };
}

function applyPatchOperations(document, ops) {
  const nextDocument = { ...document };

  for (const operation of ops) {
    if (operation.op === 'replace' && operation.path === '') {
      if (operation.value && typeof operation.value === 'object') {
        if (operation.value.final_output !== undefined) {
          nextDocument.final_output = operation.value.final_output ?? '';
        }
        if (Array.isArray(operation.value.streamed_output)) {
          nextDocument.streamed_output = operation.value.streamed_output;
        }
      }
      continue;
    }

    if (operation.op === 'replace' && operation.path === '/final_output') {
      nextDocument.final_output = operation.value ?? '';
      continue;
    }

    if (operation.op === 'add' && operation.path === '/streamed_output/-') {
      nextDocument.streamed_output = [...(nextDocument.streamed_output || []), operation.value ?? ''];
    }
  }

  return nextDocument;
}

function getPatchDelta(document, previousDocument) {
  const previousText = typeof previousDocument?.final_output === 'string' ? previousDocument.final_output : '';
  const currentText = typeof document?.final_output === 'string' ? document.final_output : '';

  if (currentText === previousText) {
    return '';
  }

  if (currentText.startsWith(previousText)) {
    return currentText.slice(previousText.length);
  }

  return currentText;
}

function extractTextFromPayload(payload, streamState = {}) {
  if (typeof payload === 'string') {
    return payload;
  }

  if (!payload || typeof payload !== 'object') {
    return '';
  }

  if (Array.isArray(payload.ops) && payload.ops.length > 0) {
    const previousDocument = streamState.document || { streamed_output: [], final_output: '' };
    const nextDocument = applyPatchOperations(previousDocument, payload.ops);
    const delta = getPatchDelta(nextDocument, previousDocument);
    streamState.lastText = typeof nextDocument.final_output === 'string' ? nextDocument.final_output : '';
    streamState.document = nextDocument;

    return delta;
  }

  if (payload.output_text) return payload.output_text;
  if (payload.answer) return payload.answer;
  if (payload.result) return payload.result;
  if (payload.content) return payload.content;
  if (payload.chunk?.text) return payload.chunk.text;
  if (payload.chunk?.value) return payload.chunk.value;
  if (payload.data?.chunk?.text) return payload.data.chunk.text;
  if (payload.data?.chunk?.value) return payload.data.chunk.value;
  if (payload.data?.output_text) return payload.data.output_text;
  if (payload.data?.content) return payload.data.content;
  if (payload.data?.answer) return payload.data.answer;
  if (payload.data?.result) return payload.data.result;
  if (payload.outputs?.[0]?.text) return payload.outputs[0].text;

  return '';
}

export function parseSsePayload(raw, streamState) {
  if (!raw) return '';

  const trimmed = String(raw).trim();
  if (!trimmed || trimmed === '[DONE]') return '';

  const lines = trimmed.split('\n').map((line) => line.trim());
  const payloadLines = lines.filter((line) => line.startsWith('data:'));

  let payloadText = '';

  if (payloadLines.length) {
    payloadText = payloadLines
      .map((line) => line.replace(/^data:\s*/, ''))
      .join('')
      .trim();
  } else {
    payloadText = trimmed;
  }

  if (!payloadText) return '';

  try {
    const parsed = JSON.parse(payloadText);
    return extractTextFromPayload(parsed, streamState);
  } catch {
    return payloadText;
  }
}

export async function streamLangServeResponse(input, onChunk) {
  const controller = new AbortController();
  const state = {
    text: '',
    document: { streamed_output: [], final_output: '' },
    lastText: ''
  };

  try {
    await fetchEventSource(LANGSERVE_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'text/event-stream',
        'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
      },
      body: JSON.stringify(buildLangServePayload(input)),
      signal: controller.signal,
      onopen: async (response) => {
        if (!response.ok) {
          throw new Error(`Ошибка запроса: ${response.status}`);
        }
      },
      onmessage(event) {
        const chunk = parseSsePayload(event.data, state);
        if (chunk) {
          state.text += chunk;
          onChunk(chunk);
        }
      },
      onclose() {
        controller.abort();
      },
      onerror(error) {
        throw error;
      }
    });
  } catch (error) {
    if (controller.signal.aborted) {
      return;
    }
    throw error;
  }
}

export function applyMessagePatch(messages, patch) {
  return applyPatch(messages, patch).newDocument;
}
