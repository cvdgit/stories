<?php
use yii\helpers\Html;
?>
<div class="modal fade" id="slide-new-question-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Слайд с тестом</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= Html::label('Тест:', 'question-list') ?>
                        <?= Html::dropDownList('', null, \common\models\StoryTest::getTestArray(), ['prompt' => 'Выберите тест', 'class' => 'form-control', 'id' => 'question-list']) ?>
                    </div>
                </div>
                <?= Html::button('Показать пример вопросов', ['id' => 'show-questions', 'class' => 'btn btn-success btn-sm', 'style' => 'margin: 10px 0']) ?>
                <table class="table table-bordered" id="show-question-list">
                    <thead>
                    <tr>
                        <th>Вопросы</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Пусто</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="create-questions">Создать слайд с тестом</button>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
(function() {
    
    function resetSelect(select, emptyText) {
        select.empty();
        $('<option/>').text(emptyText).val('').appendTo(select);
    }
    
    function fillSelect(data, select, selected, valueKey) {
        valueKey = valueKey || 'id';
        data.forEach(function(row) {
            var item = $('<option/>')
                .text(row.name)
                .val(row[valueKey]);
            if (selected) {
                item.attr('selected', parseInt(row.id) === parseInt(selected))
            }
            item.appendTo(select);
        });
    }
    
    var modal = $("#slide-new-question-modal");
    
    var questionList = $('#question-list');
    
/*    modal.on("show.bs.modal", function() {
        resetSelect(questionList, 'Выберите вопрос');
        Neo.getQuestionList().done(function(data) {
            fillSelect(data, questionList, null, 'id');
        });
    });*/
    
    $('#show-questions', modal).on('click', function() {
        var questionID = questionList.val();
        if (!questionID) {
            return false;
        }
        Neo.questions(questionID).done(function(data) {
            var list = $("table#show-question-list tbody", modal);
            list.empty();
            data.questions.forEach(function(elem) {
                $('<tr><td>' + elem.question + '</td></tr>').appendTo(list);
            });
        });
    });
    
    $('#create-questions', modal).on('click', function() {
        var questionID = questionList.val();
        if (!questionID) {
            return false;
        }
        var params = {
            'id': questionID
        };
        StoryEditor.createQuestions(params, function() {
            modal.modal('hide');
        });
    });    
})();
JS;
/** @var $this yii\web\View  */
$this->registerJs($js);
