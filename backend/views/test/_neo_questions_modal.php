<?php
/** @var $testId int */
?>
<div class="modal fade" id="neo-questions-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Список вопросов</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="neo-questions-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Вопрос</th>
                            <th>Правильные ответы</th>
                            <th>Неправильные ответы</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<<JS
$('#neo-questions-modal')
    .on('show.bs.modal', function(e) {
        var table = $('#neo-questions-table');
        table.find('tbody')
            .empty()
            .append('<tr><td colspan="4">Загрузка вопросов...</td></tr>');
        var href = $(e.relatedTarget).attr('href');
        $.getJSON(href)
            .done(function(response) {
                var body = table.find('tbody');
                body.empty();
                response.questions.forEach(function(question, i) {
                    var tr = $('<tr/>');
                    tr.append($('<td/>').text(++i));
                    tr.append($('<td/>').text(question.question));
                    
                    var correct = [];
                    var incorrect = [];
                    question.answers.forEach(function(answer) {
                        if (answer.correct) {
                            correct.push(answer.answer);
                        }
                        else {
                            incorrect.push(answer.answer);
                        }
                    });
                    
                    tr.append($('<td/>').text(correct.join(', ')));
                    tr.append($('<td/>').text(incorrect.join(', ')));
                    tr.appendTo(body);
                });
            });
    });
JS;
$this->registerJs($js);