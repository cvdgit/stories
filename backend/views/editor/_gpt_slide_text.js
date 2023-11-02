function GptSlideText() {

  const modal = new SimpleModal({id: "gpt-slide-text", title: "Создание теста на основе текста слайда"});

  async function sendMessage(content, questions, answers) {

    var response = await fetch('/admin/index.php?r=gpt/stream/chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        content,
        questions,
        answers
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
      const decoded = decoder.decode(value, {stream: true});
      document.getElementById("gpt-result").innerText += decoded;
      document.getElementById("gpt-result").scrollTop = document.getElementById("gpt-result").scrollHeight;
    }

    return response;
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
          <div id="gpt-result" style="height: 500px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; font-size: 14px"></div>
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

        const response = sendMessage(message, questions, answers);
        response.then(data => {
          if (data.ok) {
            $body.find("#gpt-loader").hide();
            $body.find("#gpt-create-questions").show();
            $body.find("#gpt-message").prop("disabled", false);
          }
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
