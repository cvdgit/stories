function GptSlideText() {

  const modal = new SimpleModal({id: "gpt-slide-text", title: "Создание теста на основе текста слайда"});

  async function sendMessage(content, questions, answers, role) {

    let accumulatedMessage = ""
    return sendEventSourceMessage({
      url: "/admin/index.php?r=gpt/stream/chat",
      headers: {
        "Content-Type": "application/json",
        Accept: "text/event-stream",
        "X-CSRF-Token": $("meta[name=csrf-token]").attr("content")
      },
      body: JSON.stringify({
        content,
        questions,
        answers,
        role
      }),
      onMessage: (streamedResponse) => {

        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }

        document.getElementById("gpt-result").innerText = accumulatedMessage;
        document.getElementById("gpt-result").scrollTop = document.getElementById("gpt-result").scrollHeight;
      }
    })
  }

  async function createQuestions(storyId, slideId, jsonString) {
    const response = await fetch(`/admin/index.php?r=editor/gpt-quiz&current_slide_id=${slideId}&story_id=${storyId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': yii.getCsrfToken(),
      },
      body: JSON.stringify({content: jsonString})
    });

    if (!response.ok) {
      const message = `Error: ${response.status}`;
      toastr.error(message);
      throw new Error(message);
    }

    return await response.json();
  }

  const $body = $(`
    <div class="row">
      <div class="col-md-12">
        <label for="gpt-role">Роль:</label>
        <select name="" id="gpt-role">
          <option value="business_rx">Бизнес аналитик RX</option>
          <option value="systems_rx">Системный аналитик RX</option>
          <option value="marketer">Маркетолог</option>
          <option value="history_teacher">Школьный учитель истории</option>
          <option value="english_teacher">Школьный учитель английского</option>
          <option value="biology_teacher">Школьный учитель биологии</option>
        </select>
        <label for="gpt-questions">Количество вопросов:</label>
        <select name="" id="gpt-questions">
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5" selected>5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="8">8</option>
          <option value="9">9</option>
          <option value="10">10</option>
        </select>
        <label for="gpt-answers">Количество ответов:</label>
        <select name="" id="gpt-answers">
          <option value="3">3</option>
          <option value="4" selected>4</option>
          <option value="5">5</option>
        </select>
      </div>
    </div>
      <div class="row">
        <div class="col-md-6">
            <label for="">Текст</label>
          <textarea id="gpt-message" style="width:100%; height: 500px; overflow-y: auto"></textarea>
        </div>
        <div class="col-md-6">
            <label for="">Результат</label>
          <div contenteditable="plaintext-only" id="gpt-result" style="height: 500px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; font-size: 14px"></div>
         </div>
      </div>
      <div style="padding: 10px; display: flex; align-items: center; justify-content: space-between; flex-direction: row">
        <button id="gpt-send" type="button" class="btn btn-primary">Отправить запрос</button>
        <button id="gpt-create-questions" style="display: none" type="button" class="btn btn-success">Создать тест</button>
        <img id="gpt-loader" style="display: none" src="/img/loading.gif" width="30" alt="">
      </div>
    `);

  this.showModal = ({content, slideId, storyId, processCallback = () => {}}) => {

    $body.find("#gpt-message").text(content.toString().trim());
    $body.find("#gpt-result").text("");
    $body.find("#gpt-create-questions").css("display", "none");
    $body.find("#gpt-loader").css("display", "none");

    modal.getElement()
      .off("click", "#gpt-send")
      .on("click", "#gpt-send", function () {

        const message = $body.find("#gpt-message").val();
        if (message.length < 50) {
          toastr.warning("Слишком короткий текст");
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true);
        $body.find("#gpt-message").prop("disabled", true);

        $body.find("#gpt-create-questions").hide();
        $body.find("#gpt-result").empty();
        $body.find("#gpt-loader").show();

        const questions = Number($body.find("#gpt-questions").val()) || 5;
        const answers = Number($body.find("#gpt-answers").val()) || 4;
        const role = $body.find("#gpt-role").val();

        const response = sendMessage(message, questions, answers, role);
        response.then(data => {
          $body.find("#gpt-loader").hide();
          $body.find("#gpt-create-questions").show();
          $body.find("#gpt-message").prop("disabled", false);
          $btn.prop("disabled", false);
        });
      });

    modal.getElement()
      .off("click", "#gpt-create-questions")
      .on("click", "#gpt-create-questions", function () {
        const result = JSON.parse($body.find("#gpt-result").text());
        const responseJson = createQuestions(storyId, slideId, result);
        responseJson.then(json => {
          if (json) {
            if (json.success) {
              if (typeof processCallback === "function") {
                processCallback();
              }
              toastr.success("Успешно");
              modal.hide();
            } else {
              toastr.error(json.message || "Ошибка");
            }
          } else {
            toastr.error("Неизвестная ошибка");
          }
        });
      });

    modal.show({
      body: $body
    });
  }
}
