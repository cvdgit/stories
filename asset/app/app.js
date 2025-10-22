import {fetchEventSource} from '@microsoft/fetch-event-source';
import {applyPatch} from "fast-json-patch";
import { v4 as uuidv4 } from 'uuid';

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

  let error = null

  navigator.mediaDevices.getUserMedia({audio: true, video: false})
    .then(function (stream) {
      stream.getTracks().forEach(track => track.stop());
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
    });

  return {
    getError() {
      return error
    }
  }
})()
