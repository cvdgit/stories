(function() {

  const modal = new SimpleModal({id: "gpt-fill-pass-test", title: "Генерация пропусков"});

  async function sendMessage(content, questions, answers, role) {

    const response = await fetch('/admin/index.php?r=gpt/stream/pass-test-chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        content,
        questions,
        answers,
        role
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
    json.map(q => {

      const reg = new RegExp(`${q.question}`, 'iu');

      content = content.replace(reg, (match, p1, p2, offset, s) => {
        const id = generateUUID();
        matches.set(id, {
          html: ` <span data-fragment-id="${id}" class="dropdown" contenteditable="false"><button class="btn btn-default dropdown-toggle highlight" data-toggle="dropdown">${q.question}</button><ul class="dropdown-menu">&nbsp;</ul></span> `,
          q
        });
        return ` {${id}} `;
      });
    });

    for (let entry of matches) {
      const [entryId, entryValue] = entry;

      const fragmentId = dataWrapper.createFragment(entryId);

      dataWrapper.createFragmentItem(fragmentId, {
        id: generateUUID(),
        title: entryValue.q.question,
        correct: true
      });

      entryValue.q.answers.map(a => {
        dataWrapper.createFragmentItem(fragmentId, {
          id: generateUUID(),
          title: a,
          correct: false
        });
      })

      const reg = new RegExp(`\\{${entryId.replace('-', '\-')}\\}`, 'igu');
      content = content.replace(reg, entryValue.html);
    }

    dataWrapper.setContent(content);
    const initContent = dataWrapper.initFragments();
    $('#content').html(initContent);

    console.log(content);
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
        <label for="gpt-questions">Количество пропусков:</label>
        <select name="" id="gpt-questions">
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="8">8</option>
          <option value="9">9</option>
          <option value="10" selected>10</option>
          <option value="11">11</option>
          <option value="12">12</option>
          <option value="13">13</option>
          <option value="14">14</option>
          <option value="15">15</option>
        </select>
        <label for="gpt-answers">Количество ответов:</label>
        <select name="" id="gpt-answers">
          <option value="2">2</option>
          <option value="3" selected>3</option>
          <option value="4">4</option>
        </select>
      </div>
    </div>
      <div class="row">
        <div class="col-md-6">
          <textarea id="gpt-message" style="width:100%; height: 500px; overflow-y: auto"></textarea>
        </div>
        <div class="col-md-6">
          <div id="gpt-result" style="height: 500px; overflow-y: auto; border: 1px solid #ccc; padding: 10px"></div>
         </div>
      </div>
      <div style="padding: 10px; display: flex; align-items: center; justify-content: space-between; flex-direction: row">
        <button id="gpt-send" type="button" class="btn btn-primary">Отправить запрос</button>
        <button id="gpt-create-questions" style="display: none" type="button" class="btn btn-success">Добавить пропуски</button>
        <img id="gpt-loader" style="display: none" src="/img/loading.gif" width="30" alt="">
      </div>
    `);

  $('#fill-with-gpt').on('click', function(e) {

    e.preventDefault();

    const content = $('#content').text();
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

        const readJson = async (r) => await r.json();

        const response = sendMessage(message, questions, answers, role);
        response.then(data => {
          if (data.ok) {
            $body.find("#gpt-loader").hide();
            $body.find("#gpt-create-questions").show();
            $body.find("#gpt-message").prop("disabled", false);

            const json = JSON.parse($body.find("#gpt-result").text());
            replaceFragments(json);
            modal.hide();
          }
          $btn.prop("disabled", false);
        });
      });

    modal.show({
      body: $body
    });



    /*
    $.ajax({
      url: '/admin/index.php?r=fragment-list/random',
      type: 'post',
      data: content,
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false
    })
      .done(response => {

        if (response && response.success) {

          const contentHtml = response.content;
          dataWrapper.setContent(contentHtml);

          response.fragments.map(f => {
            const fragmentId = dataWrapper.createFragment(f.id);
            f.items.map(i => {
              dataWrapper.createFragmentItem(fragmentId, i);
            })
          });

          let content = dataWrapper.initFragments();
          $('#content').html(content);
        }
      })
      .fail(response => toastr.error("Повторите запрос еще раз"))*/
  });
})();
