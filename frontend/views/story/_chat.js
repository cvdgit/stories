(function() {

  function generateUUID() {
    var d = new Date().getTime();
    var d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now() * 1000)) || 0;
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      var r = Math.random() * 16;
      if (d > 0) {
        r = (d + r) % 16 | 0;
        d = Math.floor(d / 16);
      }
      else {
        r = (d2 + r) % 16 | 0;
        d2 = Math.floor(d2 / 16);
      }
      return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
    });
  }

  async function sendMessage(element, question) {

    const response = await fetch('/admin/index.php?r=gpt/stream/wikids', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        "X-CSRF-Token": $("meta[name=csrf-token]").attr("content")
      },
      body: JSON.stringify({
        input: {
          question,
          chat_history: [],
        },
        config: {
          metadata: {
            conversation_id: generateUUID(),
          },
        },
        include_names: ["FindDocs"],
      })
    });

    if (!response.ok) {
      const message = `Error: ${response.status}`;
      toastr.error(message);
      throw new Error(message);
    }

    const reader = response.body.getReader();
    const decoder = new TextDecoder('utf-8');

    let streamedResponse = {}
    let errorResponse = {}
    let foundSources = false;
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

            streamedResponse = jsonpatch.applyPatch(
              streamedResponse,
              chunk.ops,
            ).newDocument;

            if (Array.isArray(streamedResponse?.logs?.["FindDocs"]?.final_output?.output) && !foundSources) {
              foundSources = true
              if (element.querySelector(".message-images").innerHTML === "") {
                const exists = [];
                streamedResponse.logs["FindDocs"].final_output.output.map((doc) => {
                  if (!exists.includes(doc.metadata.source)) {
                    exists.push(doc.metadata.source)
                    const div = document.createElement("div")
                    div.innerHTML = `
                    <a target="_blank" href="${doc.metadata.source}"><img width="300" src="${doc.metadata.images}" /></a>
                  `
                    element.querySelector(".message-images").appendChild(div)
                    container.scrollTop = container.scrollHeight
                  }
                });
              }
            }

            if (streamedResponse.id !== undefined) {
              element.setAttribute("data-run-id", streamedResponse.id)
            }

            if (Array.isArray(streamedResponse?.streamed_output)) {
              element.querySelector(".message-content").innerHTML = streamedResponse.streamed_output.join("");
              container.scrollTop = container.scrollHeight
            }
          }
        }

        if (event && event.trim() === "error") {
          const data = secondRow.toString().replace(/^data: /, "")
          const chunk = JSON.parse(data);
          if (data) {
            if (chunk?.ops) {
              errorResponse = jsonpatch.applyPatch(
                errorResponse,
                chunk.ops,
              ).newDocument;
            } else {
              errorResponse.error_text = chunk.message
            }
            element.querySelector(".message-content").innerHTML = errorResponse.error_text
          }
        }
      })
    }

    return response;
  }

  async function sendFeedback({score, key, runId, value, comment, feedbackId, isExplicit = true,}) {
    const feedback_id = feedbackId ?? generateUUID();
    const response = await fetch("/admin/index.php?r=gpt/feedback", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $("meta[name=csrf-token]").attr("content")
      },
      body: JSON.stringify({
        score,
        run_id: runId,
        key,
        value,
        feedback_id,
        comment,
        source_info: {
          is_explicit: isExplicit,
        },
      }),
    });
    const data = await response.json();
    return {
      ...data,
      feedbackId: feedback_id,
    }
  }

  const textarea = document.getElementById("send-message")
  const sendBtn = document.getElementById("send-message-btn")
  const container = document.getElementById("message-container")

  const feedbacks = []

  textarea.addEventListener("keydown", e => {
    if (e.code === "Enter" && !e.shiftKey) {
      e.preventDefault()

      const message = e.target.value;
      if (!message) {
        return;
      }

      if ($(container).find("#message-offers").length) {
        container.innerHTML = "";
      }

      textarea.setAttribute("disabled", "true")
      sendBtn.setAttribute("disabled", "true")

      container.prepend(createQuestionMessage(message))

      const answerItem = createAnswerMessage()
      container.prepend(answerItem)

      const response = sendMessage(answerItem, message)

      response.then(data => {
        answerItem.querySelector(".loading").style.display = "none"
        answerItem.querySelector(".message-feedback").style.display = "block"
        textarea.removeAttribute("disabled")
        sendBtn.removeAttribute("disabled")
      })
    }
  })

  $("#message-container")
    .on("click", ".offer-line-item", function(e) {

    const message = $(this).text()

    container.innerHTML = ""
    textarea.setAttribute("disabled", "true")

    container.prepend(createQuestionMessage(message))

    const answerItem = createAnswerMessage()
    container.prepend(answerItem)

    const response = sendMessage(answerItem, message)

    response.then(data => {
      answerItem.querySelector(".loading").style.display = "none"
      answerItem.querySelector(".message-feedback").style.display = "block"
      textarea.removeAttribute("disabled")
    })
  })
    .on("click", "[data-run-id] .feedback-button", async function() {

      const runId = $(this).parents("[data-run-id]:eq(0)").attr("data-run-id")
      if (!runId) {
        return
      }

      if (feedbacks.includes(runId)) {
        toastr.info("Вы уже отправили свой отзыв.")
        return
      }

      const score = $(this).hasClass("feedback-like") ? 1 : 0
      const data = await sendFeedback({
        score,
        runId,
        key: "user_score",
        isExplicit: true,
      })

      if (data.success) {
        feedbacks.push(runId)
        toastr.success("Успешно")
      }
    })

  sendBtn.addEventListener("click", function() {

    const message = textarea.value
    if (!message) {
      return;
    }

    if ($(container).find("#message-offers").length) {
      container.innerHTML = "";
    }

    textarea.setAttribute("disabled", "true")
    sendBtn.setAttribute("disabled", "true")

    container.prepend(createQuestionMessage(message))

    const answerItem = createAnswerMessage()
    container.prepend(answerItem)

    const response = sendMessage(answerItem, message)

    response.then(data => {
      answerItem.querySelector(".loading").style.display = "none"
      answerItem.querySelector(".message-feedback").style.display = "block"
      textarea.removeAttribute("disabled")
      sendBtn.removeAttribute("disabled")
    })
  })

  function createMessageItem() {
    const item = document.createElement("div")
    item.classList.add("message-item")
    return item;
  }

  function createQuestionMessage(message) {
    const item = createMessageItem()
    item.innerHTML = `
      <h2>${message}</h2>
    `
    return item;
  }

  function createAnswerMessage() {
    const item = createMessageItem()
    item.innerHTML = `
        <h3 style="margin-bottom: 0">Слайды:</h3>
        <div class="message-images"></div>
        <h3 style="margin-bottom: 0">Ответ:</h3>
        <div class="message-content"></div>
        <div class="message-feedback" style="display: none">
            <button type="button" class="feedback-button feedback-like">👍</button>
            <button type="button" class="feedback-button feedback-dislike">👎</button>
        </div>
        <div class="loading">
          <div class="loading-inner circle">
            <div class="loading-line">
              <div class="loading-bar bar-1" style="background-color: black"></div>
              <div class="loading-bar bar-2" style="background-color: black"></div>
              <div class="loading-bar bar-3" style="background-color: black"></div>
              <div class="loading-bar bar-4" style="background-color: black"></div>
            </div>
          </div>
        </div>
      `
    return item;
  }
})();
