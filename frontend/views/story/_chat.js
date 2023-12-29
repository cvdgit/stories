(function() {

  async function sendMessage(element, question) {

    var response = await fetch('/admin/index.php?r=gpt/stream/wikids', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        question
      })
    });

    if (!response.ok) {
      const message = `Error: ${response.status}`;
      toastr.error(message);
      throw new Error(message);
    }

    var reader = response.body.getReader();
    var decoder = new TextDecoder('utf-8');

    while (true) {
      const { done, value } = await reader.read();
      if (done) {
        break;
      }

      let decoded = decoder.decode(value, {stream: true});

      try {
        const meta = JSON.parse(decoded)

        const exists = [];
        meta.map(item => {
          if (!exists.includes(item.source)) {
            exists.push(item.source)
            if (item.images) {
              const div = document.createElement("div")
              div.innerHTML = `
                <a target="_blank" href="${item.source}"><img width="300" src="${item.images}" /></a>
              `
              element.querySelector(".message-images").appendChild(div)
              container.scrollTop = container.scrollHeight
            }
          }
        })
        decoded = ""
      } catch (e) {
      }

      element.querySelector(".message-content").innerHTML += decoded
      container.scrollTop = container.scrollHeight
    }

    return response;
  }

  const textarea = document.getElementById("send-message")
  const sendBtn = document.getElementById("send-message-btn")
  const container = document.getElementById("message-container")

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
        textarea.removeAttribute("disabled")
        sendBtn.removeAttribute("disabled")
      })
    }
  })

  $("#message-container").on("click", ".offer-line-item", function(e) {

    const message = $(this).text()

    container.innerHTML = ""
    textarea.setAttribute("disabled", "true")

    container.prepend(createQuestionMessage(message))

    const answerItem = createAnswerMessage()
    container.prepend(answerItem)

    const response = sendMessage(answerItem, message)

    response.then(data => {
      answerItem.querySelector(".loading").style.display = "none"
      textarea.removeAttribute("disabled")
    })
  });

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
        <div class="message-images"></div>
        <div class="message-content"></div>
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
