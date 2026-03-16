import {fetchEventSource} from '@microsoft/fetch-event-source';
import {applyPatch} from "fast-json-patch";
import { v4 as uuidv4 } from 'uuid';
import io from 'socket.io-client';

window.sendEventSourceMessage = async function ({
                                                  url,
                                                  method = "POST",
                                                  headers = {},
                                                  body,
                                                  onEnd,
                                                  onMessage,
                                                  onError,
                                                  signal
                                                }) {
  let streamedResponse = {}
  return await fetchEventSource(url, {
    method,
    headers,
    body,
    signal,
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
          onError({error_text: chunk.message})
        } else if (chunk?.ops) {
          streamedResponse = applyPatch(
            streamedResponse,
            chunk.ops,
          ).newDocument;
          onError(streamedResponse)
        } else {
          onError({error_text: "Unknown error"})
        }
      }
    },
  });
}

window.uuidv4 = uuidv4

window.MicrophoneChecker = (function () {

  const MediaPermissionsErrorType = {};
  (function (MediaPermissionsErrorType) {
    /** (macOS) browser does not have permission to access cam/mic */
    MediaPermissionsErrorType["SystemPermissionDenied"] = "SystemPermissionDenied";
    /** user denied permission for site to access cam/mic */
    MediaPermissionsErrorType["UserPermissionDenied"] = "UserPermissionDenied";
    /** (Windows) browser does not have permission to access cam/mic OR camera is in use by another application or browser tab */
    MediaPermissionsErrorType["CouldNotStartVideoSource"] = "CouldNotStartVideoSource";
    /** all other errors */
    MediaPermissionsErrorType["Generic"] = "Generic";
  })(MediaPermissionsErrorType);

  let checkIsDone = false
  let error = null

  function check() {
    return new Promise((resolve, reject) => navigator.mediaDevices.getUserMedia({audio: true, video: false})
      .then(function (stream) {
        stream.getTracks().forEach(track => track.stop());
        resolve(true);
      })
      .catch(err => {
        const errName = err.name;
        const errMessage = err.message;
        let errorType = MediaPermissionsErrorType.Generic;
        if (errName === 'NotAllowedError') {
          if (errMessage === 'Permission denied by system') {
            errorType = MediaPermissionsErrorType.SystemPermissionDenied;
          } else if (errMessage === 'Permission denied' || errMessage === 'Permission dismissed') {
            errorType = MediaPermissionsErrorType.UserPermissionDenied;
          }
        } else if (errName === 'NotReadableError') {
          errorType = MediaPermissionsErrorType.CouldNotStartVideoSource;
        }
        error = {
          type: errorType,
          name: err.name,
          message: err.message
        }
        reject({
          type: errorType,
          name: err.name,
          message: err.message,
        });
      })
    )
  }

  return {
    check(force = false) {
      if (checkIsDone && !force) {
        if (error) {
          return new Promise((resolve, reject) => reject(error))
        }
        return new Promise((resolve, reject) => resolve(true))
      }
      checkIsDone = true;
      return check();
    },
    getError() {
      return error
    }
  }
})()

window.io = io;

window.sendStreamMessage = function(url, payload, onMessage, onEndCallback) {
  let accumulatedMessage = ''
  return sendEventSourceMessage({
    url,
    headers: {
      'Content-Type': 'application/json',
      Accept: 'text/event-stream',
      'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
    },
    body: JSON.stringify(payload),
    onMessage: (streamedResponse) => {
      if (Array.isArray(streamedResponse?.streamed_output)) {
        accumulatedMessage = streamedResponse.streamed_output.join("");
      }
      onMessage(accumulatedMessage)
    },
    onError: (streamedResponse) => {
      console.log(streamedResponse)
    },
    onEnd: () => onEndCallback(accumulatedMessage)
  })
}

window.processOutputAsJson = function (output) {
  return JSON.parse(output.replace(/```json\n?|```/g, ''));
}

window.Api = (function() {

  function isJsonResponse(response) {
    const type = response.headers.get('content-type');
    return type && type.includes('application/json');
  }

  function sendRequest(url, method, headers, data, formData) {

    const common = {
      method,
      headers: {
        Accept: 'application/json',
        'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
        ...headers,
      },
    };

    const body =
      data !== null
        ? {
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(data),
        }
        : {headers: {}};

    if (formData) {
      body.headers = {
        'Content-Type': 'application/x-www-form-urlencoded',
      };
      body.body = formData;
    }

    return fetch(url, {
      ...common,
      ...body,
      cache: 'no-cache',
      headers: {
        ...common.headers,
        ...(body.headers),
      },
    }).then((response) => {
      if (response.ok) {
        return response;
      }
      throw response;
    })
      .then((response) => {
        if (isJsonResponse(response)) {
          return response.json();
        }
        return response.text();
      });
  }

  return {
    async get(url) {
      return await sendRequest(url, 'GET', {});
    },
    async post(url, payload) {
      return await sendRequest(url, 'POST', {}, payload);
    },
    async postForm(url, payload) {

    }
  }
})();
