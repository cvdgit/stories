import {_extends} from "../common";
import InnerDialog from "../components/Dialog";
import sendEventSourceMessage from "../../../app/sendEventSourceMessage";

function GptQuestion(test) {
  this.element = null;
  this.container = test.container;
  this.deck = test.getDeck();
}

GptQuestion.prototype.createWrapper = function (content) {
  const $wrapper = $('<div class="seq-question image-gaps-question"></div>');
  if (content) {
    $wrapper.append(content);
  }
  return $wrapper;
};

async function sendMessage(url, payload, onMessage, onError, onEnd) {
  let accumulatedMessage = ""

  return sendEventSourceMessage({
    url,
    headers: {
      Accept: "text/event-stream",
      "X-CSRF-Token": $("meta[name=csrf-token]").attr("content")
    },
    body: JSON.stringify(payload),
    onMessage: (streamedResponse) => {
      if (Array.isArray(streamedResponse?.streamed_output)) {
        accumulatedMessage = streamedResponse.streamed_output.join("");
      }
      onMessage(accumulatedMessage)
    },
    onError: (streamedResponse) => {
      accumulatedMessage = streamedResponse?.error_text
      onError(accumulatedMessage)
    },
    onEnd
  })
}

function processOutputAsJson(output) {
  let json = null
  try {
    json = JSON.parse(output.replace(/```json\n?|```/g, ''))
  } catch (ex) {

  }
  return json
}

GptQuestion.prototype.create = function (question, correctAnswerHandler, incorrectAnswerHandler) {

  const {payload} = question
  console.log(payload)
  //const {groups} = payload

  const wrap = document.createElement('div')
  wrap.style.display = 'flex'
  wrap.style.justifyContent = 'space-between'
  wrap.style.flexDirection = 'column'
  wrap.style.height = '100%'

  wrap.innerHTML = `<div style="display: flex; flex-direction: column; margin-bottom: 20px">
    <pre class="textarea job-text" style="max-height: 170px; overflow-y: auto"></pre>
</div>
<div class="gpt-answer-wrap">
    <div style="display: flex; flex-direction: column; flex: 1; margin-right: 10px; padding-bottom: 50px; height: 100%">
        <h5>Ответ пользователя:</h5>
        <textarea class="textarea gpt-user-response"></textarea>
    </div>
    <button class="btn-sm job-send" type="button">Проверить</button>
    <div class="job-send-loader">
        <img src="/img/loading.gif" width="30" alt="">
    </div>
</div>
  `

  wrap.querySelector('.job-text').innerHTML = payload.job

  wrap.querySelector('.job-send').addEventListener('click', e => {
    const userResponse = wrap.querySelector('.gpt-user-response').value
    if (!userResponse) {
      return
    }

    const content = `
<div style="height: 600px; min-width: 600px; max-width: 980px; display: flex; flex-direction: column;">
        <pre class="textarea gpt-response" style="text-wrap: auto"></pre>
        <div style="display: flex; margin-top: 10px; flex-direction: row; align-items: center; justify-content: center">
            <img class="gpt-response-loader" height="50px" src="/img/loading.gif" alt="">
            <button style="display: none" type="button" class="btn retry-answer">Повторить попытку</button>
            <button style="display: none" type="button" class="btn next-question-handler">Следующий вопрос</button>
        </div>
        </div>
    `

    const dialog = new InnerDialog(this.container, {title: 'Проверка ответа', content});
    dialog.show(wrapper => {

      const responseElem = wrapper[0].querySelector('.gpt-response')

      wrapper[0].querySelector('.retry-answer').addEventListener('click', e => {
        dialog.hide()
      })

      wrapper[0].querySelector('.next-question-handler').addEventListener('click', e => {
        dialog.hide()
        correctAnswerHandler(userResponse)
      })

      const response = sendMessage(`/admin/index.php?r=gpt/stream/question`, {
        questionId: question.id,
        userResponse,
      }, message => {
        responseElem.innerHTML = message
      }, message => {
        console.error(message)
      }, () => {

        const json = processOutputAsJson(wrapper[0].querySelector('.gpt-response').textContent)
        const correct = (Number(json?.points) || 1) > 2

        wrapper[0].querySelector('.gpt-response-loader').style.display = 'none'

        if (correct) {
          wrapper[0].querySelector('.next-question-handler').style.display = 'inline-block'
        } else {

          incorrectAnswerHandler(userResponse)

          wrapper[0].querySelector('.retry-answer').style.display = 'inline-block'
        }
      })
    })
  })

  this.element = wrap

  return this.element;
};

GptQuestion.prototype.getContent = function(question) {
  const {payload} = question

  /*
  const {groups} = payload

  const $groups = $("<div/>", {class: "grouping-groups"})
  $groups.addClass(getGroupsClassName(groups.length))

  groups.map(group => {

    const $group = $("<div/>", {class: "grouping-group"})
      .append(
        $("<p/>").text(group.title)
      )
      .append(
        $("<div/>", {class: "group-items-spot"})
      )

    group.items.map(item => {
      const $item = $('<button/>', {
        type: 'button',
        class: 'pass-test-btn highlight',
        text: item.title
      })
      $group.find(".group-items-spot").append($item)
    })

    $group.appendTo($groups)
  })

  const $wrap = $('<div/>', {class: "grouping-wrap"})

  return $wrap.append($groups)
  */

  const wrap = document.createElement('div')
  wrap.style.display = 'flex'
  wrap.style.justifyContent = 'space-between'
  wrap.style.flexDirection = 'column'

  wrap.innerHTML = `<div style="display: flex; flex-direction: column">
    <h5>Задание:</h5>
    <pre id="run-job-text" class="textarea"></pre>
</div>
<div id="gpt-message-list"
     style="display: flex; flex-direction: column; flex: 1; overflow-y: auto; margin-bottom: 20px">
</div>
<div style="display: flex; position: relative; flex-direction: row; justify-content: space-between; height: 100px; align-items: center">
    <div style="display: flex; flex-direction: column; flex: 1; margin-right: 10px">
        <h5>Ответ пользователя:</h5>
        <textarea id="gpt-user-response" class="textarea" style="flex: 1; min-height: 50px; height: 50px;"></textarea>
    </div>
    <button id="job-send" class="btn" type="button">Проверить</button>
    <div id="job-send-loader"
         style="display: none; cursor: wait; position: absolute; left: 0; top: 0; right: 0; bottom: 0; align-items: center; justify-content: center; background-color: #eee; border-radius: 8px">
        <img src="/img/loading.gif" width="30" alt="">
    </div>
</div>
  `

  return wrap
}

_extends(GptQuestion, {
  pluginName: 'gptQuestion'
});

export default GptQuestion
