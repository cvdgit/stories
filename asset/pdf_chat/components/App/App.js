import React from 'react';
import {useState} from 'react';
import PromptInput from "../PromptInput/PromptInput";
import './App.css';
import PromptResponseList from "../PromptResponseList/PromptResponseList";
import {applyPatch} from "fast-json-patch";
import {fetchEventSource} from "@microsoft/fetch-event-source";

function uuidv4() {
  return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
  );
}

const App = () => {

  const [responseList, setResponseList] = useState([]);
  const [prompt, setPrompt] = useState('');
  const [promptToRetry, setPromptToRetry] = useState(null);
  const [uniqueIdToRetry, setUniqueIdToRetry] = useState(null);
  const [isLoading, setIsLoading] = useState(false);
  let loadInterval;

  const generateUniqueId = () => {
    const timestamp = Date.now();
    const randomNumber = Math.random();
    const hexadecimalString = randomNumber.toString(16);
    return `id-${timestamp}-${hexadecimalString}`;
  }

  const htmlToText = (html) => {
    const temp = document.createElement('div');
    temp.innerHTML = html;
    return temp.textContent;
  }

  const delay = (ms) => {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  const addResponse = (selfFlag, response) => {
    const uid = generateUniqueId()
    setResponseList(prevResponses => [
      ...prevResponses,
      {
        id: uid,
        response,
        selfFlag
      },
    ]);
    return uid;
  }

  const updateResponse = (uid, updatedObject) => {
    setResponseList(prevResponses => {
      let updatedList = [...prevResponses]
      const index = prevResponses.findIndex((response) => response.id === uid);
      if (index > -1) {
        updatedList[index] = {
          ...updatedList[index],
          ...updatedObject
        }
      }
      return updatedList;
    });
  }

  const regenerateResponse = async () => {
    await getGPTResult(promptToRetry, uniqueIdToRetry);
  }

  const conversationId = uuidv4()

  const getGPTResult = async (_promptToRetry, _uniqueIdToRetry) => {

    const _prompt = _promptToRetry ?? htmlToText(prompt);

    if (isLoading || !_prompt) {
      return;
    }

    setIsLoading(true);

    setPrompt('');

    let uniqueId;
    if (_uniqueIdToRetry) {
      uniqueId = _uniqueIdToRetry;
    } else {
      addResponse(true, _prompt);
      uniqueId = addResponse(false);
    }

    const messages = [
      ...responseList.map(i => ({
        "role": i.selfFlag ? "user" : "assistant",
        "content": i.response
      }))
    ]

    try {
      const data = {
        "input": {
          messages,
          question: _prompt
        },
        "config": {
          "metadata": {
            "conversation_id": conversationId
          }
        },
        "include_names": []
      }

      let streamedResponse = {}
      let accumulatedMessage = ""

      await fetchEventSource("/admin/index.php?r=gpt/stream/pdf", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "text/event-stream",
        },
        body: JSON.stringify(data),
        openWhenHidden: true,
        onerror(err) {
          throw err;
        },
        onmessage(msg) {
          //console.log(msg)
          if (msg.event === "end") {
            console.log("end")
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

            updateResponse(uniqueId, {
              response: accumulatedMessage,
            });
          }
        },
      });

      console.log("after")

      /*updateResponse(uniqueId, {
        response: response.data.trim(),
      });*/

      //}

      setPromptToRetry(null);
      setUniqueIdToRetry(null);
    } catch (err) {
      setPromptToRetry(_prompt);
      setUniqueIdToRetry(uniqueId);
      updateResponse(uniqueId, {
        response: `Error: ${err.message}`,
        error: true
      });
    } finally {
      // Clear the loader interval
      clearInterval(loadInterval);
      setIsLoading(false);
    }
  }

  return (
    <div className="App">
      <div id="response-list">
        <PromptResponseList responseList={responseList} key="response-list"/>
      </div>
      {uniqueIdToRetry &&
        (<div id="regenerate-button-container">
            <button id="regenerate-response-button" className={isLoading ? 'loading' : ''}
                    onClick={() => regenerateResponse()}>
              Повторить
            </button>
          </div>
        )
      }
      <div id="input-container">
        <PromptInput
          prompt={prompt}
          onSubmit={() => getGPTResult()}
          key="prompt-input"
          updatePrompt={(prompt) => setPrompt(prompt)}
        />
        <button id="submit-button" className={isLoading ? 'loading' : ''} onClick={() => getGPTResult()}></button>
      </div>
    </div>
  );
}

export default App;
