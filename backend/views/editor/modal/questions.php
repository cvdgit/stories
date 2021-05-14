<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model backend\models\editor\RemoteTestForm */
?>
<div class="modal fade" id="slide-new-question-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Слайд с тестом</h4>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin(); ?>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'test_id', ['inputOptions' => ['class' => 'form-control input-sm']])
                            ->widget(\backend\widgets\SelectRemoteTestWidget::class) ?>
                    </div>
                </div>
                <!--div class="row" style="margin-top: 20px">
                    <div class="col-md-12">
                        <?php // Html::label('Параметры:', 'question-params') ?>
                        <?php // Html::textInput('', null, ['id' => 'question-params', 'class' => 'form-control']) ?>
                    </div>
                </div>
                <?php // Html::button('Показать пример вопросов', ['id' => 'show-questions', 'class' => 'btn btn-success btn-sm', 'style' => 'margin: 10px 0']) ?>
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
                </table-->
                <?php ActiveForm::end(); ?>
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
    
/*    function resetSelect(select, emptyText) {
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
    }*/
    
    var modal = $("#slide-new-question-modal");
    
    var questionList = $('#remotetestform-test_id');
    //var paramsInput = $('#question-params');
    
    /*$('#show-questions', modal).on('click', function() {
        var questionID = questionList.val();
        if (!questionID) {
            return false;
        }
        Neo.questions(questionID, paramsInput.val()).done(function(data) {
            var list = $("table#show-question-list tbody", modal);
            list.empty();
            data.questions.forEach(function(elem) {
                $('<tr><td>' + elem.question + '</td></tr>').appendTo(list);
            });
        });
    });*/
    
    $('#create-questions', modal).on('click', function() {
        var questionID = questionList.val();
        if (!questionID) {
            return false;
        }
        var params = {
            'id': questionID,
            'question_params': '' //paramsInput.val()
        };
        StoryEditor.createQuestions(params, function() {
            modal.modal('hide');
        });
    });    
})();
JS;
/** @var $this yii\web\View  */
$this->registerJs($js);
