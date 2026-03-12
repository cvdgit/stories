export default function RetellingResponse(retellingReRunHandler) {

  const elem = document.createElement('div');
  elem.classList.add('retelling-area-inner');
  elem.innerHTML = `
<div class="retelling-handle-loader">Проверка ответа... <img width="30" src="/img/loading.gif" alt="loading..."></div>
<div id="retelling_retelling-response" style="display: none"></div>
<div style="display: flex; flex-direction: row; gap: 20px; align-items: center">
<div class="retelling-handle-result"></div>
<div class="retelling-handle-controls"><button class="retelling-rerun" type="button">Повторить</button></div>
</div>
<div class="retelling-handle-result-detail">
<a class="retelling-detail-handler" href="">Подробнее</a>
<div class="retelling-handle-result-detail-list">
</div>
`;

  const retellingResponseElem = elem.querySelector('#retelling_retelling-response');

  elem.querySelector('.retelling-detail-handler').addEventListener('click', e => {
    e.preventDefault();
    elem.querySelector('.retelling-handle-result-detail-list').innerHTML = '';
    const json = processOutputAsJson(
      elem.querySelector('#retelling_retelling-response').innerText
    );
    (json?.sentences_similarity || []).map(({original, rewrite, similarity}) => {
      const row = document.createElement('div');
      row.style.display = 'flex';
      row.style.flexDirection = 'row';
      row.style.gap = '20px';
      row.style.width = '100%';
      row.innerHTML = `
<div style="flex: 1">
<div>${original}</div>
<div>${rewrite}</div>
</div>
<div>${similarity}%</div>
`;
      elem.querySelector('.retelling-handle-result-detail-list').appendChild(row);
    });
  });

  elem.querySelector('.retelling-rerun').addEventListener('click', e => {
    retellingReRunHandler();
  });

  return {
    render() {
      return elem;
    },
    send(userResponse, slideTexts, resultCallback) {
      elem.classList.add('loading');
      return sendStreamMessage(
        '/admin/index.php?r=gpt/stream/retelling',
        {userResponse, slideTexts},
        message => retellingResponseElem.innerText = message,
        message => {
          elem.classList.remove('loading');
          const json = processOutputAsJson(message);
          elem.querySelector('.retelling-handle-result').innerHTML = `Результат: <b>${json?.overall_similarity}%</b>`;
          resultCallback(json);
        }
      );
    }
  }
}
