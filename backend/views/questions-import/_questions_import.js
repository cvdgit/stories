function onTestChange(testId) {
  if (!testId) {
    return;
  }
  sendQuestionsRequest(testId);
}

function sendQuestionsRequest(testId) {
  const $importList = $('#import-questions-list');
  $('#import-questions-count').text('0');
  $importList.empty();
  $.getJSON(`/admin/index.php?r=questions-import/questions&test_id=${testId}`)
    .done(questions => {
      (questions || []).forEach(question => {
        const $questionElem = $(drawQuestion(question));
        $importList.append($questionElem);
      });
    });
}

function drawQuestion(question) {
  return `
    <div class="import-question-row" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center">
        <div><a href="${question.url}" target="_blank">${question.name}</a></div>
        <div class="slides-row-actions">
            <div class="checkbox">
                <label style="padding-left: 0; padding-right: 20px">
                    Выбрать <input style="margin-left: 10px" name="QuestionsImportForm[questions][]" type="checkbox" value="${question.id}">
                </label>
            </div>
        </div>
    </div>
  `;
}

attachBeforeSubmit(document.getElementById('import-questions-form'), form => {

  sendForm('/admin/index.php?r=questions-import/import', 'post', new FormData(form))
    .done(response => {
      if (response && response.success) {
        location.reload();
      }
      if (response && response.success === false) {
        toastr.error(response.message || 'Произошла ошибка');
      }
    });
});

$('#import-questions-list').on('click', '.slides-row-actions input[type=checkbox]', function() {
  $('#import-questions-count').text($('#import-questions-list .slides-row-actions input[type=checkbox]:checked').length);
});
