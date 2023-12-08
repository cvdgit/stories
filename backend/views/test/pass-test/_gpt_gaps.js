(function() {

  const modal = new SimpleModal({id: "gpt-gaps-modal", title: "Генерация пропусков"});

  async function sendMessage(content, role, fragments) {

    const response = await fetch('/admin/index.php?r=gpt/stream/pass-test-chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        content,
        role,
        fragments
      })
    });

    if (!response.ok) {
      const message = `Error: ${response.status}`;
      toastr.error(message);
      throw new Error(message);
    }

    const reader = response.body.getReader();
    const decoder = new TextDecoder('utf-8');

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

  function replaceFragments(json) {

    const el = $('<div>' + $('#content').html() + '</div>');
    el.find('span[data-fragment-id]').replaceWith(function() {
      return '{' + $(this).attr('data-fragment-id') + '}';
    });

    let content = el[0].outerHTML;

    if (!json.length) {
      return;
    }

    const matches = new Map();
    json.map(question => {
      const reg = new RegExp(`${question}`, 'iu');
      content = content.replace(reg, (match, p1, p2, offset, s) => {
        const id = generateUUID();
        matches.set(id, {
          html: ` <span data-fragment-id="${id}" class="dropdown" contenteditable="false"><button class="btn btn-default dropdown-toggle highlight" data-toggle="dropdown">${question}</button><ul class="dropdown-menu">&nbsp;</ul></span> `,
          question
        });
        return ` {${id}} `;
      });
    });

    for (let entry of matches) {
      const [entryId, entryValue] = entry;
      const fragmentId = dataWrapper.createFragment(entryId);
      dataWrapper.createFragmentItem(fragmentId, {
        id: generateUUID(),
        title: entryValue.question,
        correct: true
      });
      const reg = new RegExp(`\\{${entryId.replace('-', '\-')}\\}`, 'igu');
      content = content.replace(reg, entryValue.html);
    }

    dataWrapper.setContent(content);
    const initContent = dataWrapper.initFragments();
    $('#content').html(initContent);
  }

  const $body = $(`
<div class="row">
      <div class="col-md-12">
        <label for="gpt-role">Роль:</label>
        <select name="" id="gpt-role">
          <option value="">Без роли</option>
          <option value="business_rx">Бизнес аналитик RX</option>
          <option value="systems_rx">Системный аналитик RX</option>
          <option value="marketer">Маркетолог</option>
          <option value="history_teacher">Школьный учитель истории</option>
          <option value="english_teacher">Школьный учитель английского</option>
          <option value="biology_teacher">Школьный учитель биологии</option>
        </select>
      </div>
    </div>
      <div class="row">
        <div class="col-md-12">
          <div id="gpt-result" style="height: 500px; overflow-y: auto; border: 1px solid #ccc; padding: 10px"></div>
         </div>
      </div>
      <div style="padding: 10px; display: flex; align-items: center; justify-content: space-between; flex-direction: row">
        <button id="gpt-send" type="button" class="btn btn-primary">Отправить запрос</button>
        <button id="gpt-create-questions" style="display: none" type="button" class="btn btn-success">Добавить пропуски</button>
        <img id="gpt-loader" style="display: none" src="/img/loading.gif" width="30" alt="">
      </div>
    `);

  $('#gpt-generate-gaps').on('click', function(e) {

    e.preventDefault();

    const content = $('#content').text().replace(/\s+/g, " ");

    //$body.find("#gpt-message").text(content.toString().trim());
    $body.find("#gpt-result").text("");
    $body.find("#gpt-create-questions").css("display", "none");
    $body.find("#gpt-loader").css("display", "none");

    modal.getElement()
      .off("click", "#gpt-send")
      .on("click", "#gpt-send", function () {

        const message = content.toString().trim();
        if (message.length < 50) {
          toastr.warning("Слишком короткий текст");
          return;
        }

        const $btn = $(this);
        $btn.prop("disabled", true);
        //$body.find("#gpt-message").prop("disabled", true);

        $body.find("#gpt-create-questions").hide();
        $body.find("#gpt-result").empty();
        $body.find("#gpt-loader").show();

        const role = $body.find("#gpt-role").val();

        const fragments = window.dataWrapper.getFragments().map(f => f.items
          .filter(i => i.correct)
          .map(i => i.title.replace(/\s+/g, " "))
          .join(" ")
        );

        const response = sendMessage(message, role, fragments);
        response.then(data => {
          if (data.ok) {
            $body.find("#gpt-loader").hide();
            //$body.find("#gpt-create-questions").show();
            //$body.find("#gpt-message").prop("disabled", false);

            try {
              const json = JSON.parse($body.find("#gpt-result").text());
              replaceFragments(json);
              modal.hide();
            } catch (ex) {

            }
          }
          $btn.prop("disabled", false);
        });
      });

    modal.show({
      body: $body
    });
  });
})();

(function() {
  const modal = new SimpleModal({
    id: "gpt-gaps-incorrect-modal",
    title: "Генерация неправильных ответов для пропусков"
  });

  async function sendMessage(content, role, fragments) {

    const response = await fetch('/admin/index.php?r=gpt/stream/pass-test-incorrect-chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        content,
        role,
        fragments
      })
    });

    if (!response.ok) {
      const message = `Error: ${response.status}`;
      toastr.error(message);
      throw new Error(message);
    }

    const reader = response.body.getReader();
    const decoder = new TextDecoder('utf-8');

    while (true) {
      const { done, value } = await reader.read();
      if (done) {
        break;
      }
      const decoded = decoder.decode(value, {stream: true});
      document.getElementById("gpt-incorrect-result").innerText += decoded;
      document.getElementById("gpt-incorrect-result").scrollTop = document.getElementById("gpt-incorrect-result").scrollHeight;
    }

    return response;
  }

  const $body = $(`
      <div class="row">
      <div class="col-md-12">
      <div>Пропуски:</div>
      <div id="to-gpt-fragments" style="margin-top: 10px"></div>
      <p style="margin-top: 10px; font-size: 12px;"><i>Учитываются пропуски у которых только один правильные ответ и нет неправильных</i></p>
</div>
</div>
<div class="row">
      <div class="col-md-12">
        <label for="gpt-role">Роль:</label>
        <select name="" id="gpt-role">
          <option value="">Без роли</option>
          <option value="business_rx">Бизнес аналитик RX</option>
          <option value="systems_rx">Системный аналитик RX</option>
          <option value="marketer">Маркетолог</option>
          <option value="history_teacher">Школьный учитель истории</option>
          <option value="english_teacher">Школьный учитель английского</option>
          <option value="biology_teacher">Школьный учитель биологии</option>
        </select>
      </div>
    </div>
      <div class="row">
        <div class="col-md-12">
            <div>Ответ нейросети:</div>
          <div id="gpt-incorrect-result" style="height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px"></div>
         </div>
      </div>
      <div style="padding: 10px; display: flex; align-items: center; justify-content: space-between; flex-direction: row">
        <button id="gpt-send-incorrect" type="button" class="btn btn-primary">Отправить запрос</button>
        <img id="gpt-loader" style="display: none" src="/img/loading.gif" width="30" alt="">
      </div>
    `);

  $("#gpt-add-incorrect").on("click", function(e) {
    e.preventDefault();

    $body.find("#gpt-incorrect-result").text("");
    $body.find("#gpt-loader").css("display", "none");

    const fragments = window.dataWrapper.getFragments().filter(f => f.items.length === 1);

    $body.find("#to-gpt-fragments").empty();

    if (fragments.length === 0) {
      $body.find("#gpt-send-incorrect").prop("disabled", true);
      $body.find("#to-gpt-fragments").text("Нет подходящих пропусков");
    } else {

      fragments.map(f => {
        const elem = $("<span/>", {class: "label label-primary"}).text(
          f.items
            .filter(i => i.correct)
            .map(i => i.title.replace(/\s+/g, " "))
            .join(" ")
        )
        $body.find("#to-gpt-fragments").append(elem);
      })

      const content = $('#content').text().replace(/\s+/g, " ");

      modal.getElement()
        .off("click", "#gpt-send-incorrect")
        .on("click", "#gpt-send-incorrect", function () {

          const message = content.toString().trim();

          const $btn = $(this);
          $btn.prop("disabled", true);

          $body.find("#gpt-incorrect-result").empty();
          $body.find("#gpt-loader").show();

          const role = $body.find("#gpt-role").val();

          const fragmentList = fragments.map(f => f.items
            .filter(i => i.correct)
            .map(i => i.title)
            .join(" ")
          );

          const response = sendMessage(message, role, fragmentList);
          response.then(data => {
            if (data.ok) {
              $body.find("#gpt-loader").hide();

              try {
                const json = JSON.parse($body.find("#gpt-incorrect-result").text());
                json.map(q => {
                  const foundFragments = dataWrapper.findFragmentByCorrectItemTitle(q.question);
                  foundFragments.map(f => {
                    q.answers.map(answerName => dataWrapper.createFragmentItem(f.id, {
                      id: generateUUID(),
                      title: answerName,
                      correct: false
                    }))
                  });
                });
              } catch (ex) {

              }

              modal.hide();
            }
            $btn.prop("disabled", false);
          });

        });
    }

    modal.show({
      body: $body
    });
  });
})();
