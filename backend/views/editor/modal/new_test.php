<?php
use yii\widgets\ActiveForm;
/** @var $model backend\models\editor\LocalTestForm */
?>
<div class="modal fade" id="new-test-modal">
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
                            ->widget(\backend\widgets\SelectLocalTestWidget::class) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="create-test">Создать слайд с тестом</button>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
(function() {
    
    /*function resetSelect(select, emptyText) {
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
    
    var modal = $("#new-test-modal");
    var questionList = $('#localtestform-test_id');

    $('#create-test', modal).on('click', function() {
        var questionID = questionList.val();
        if (!questionID) {
            return false;
        }
        var params = {
            'id': questionID,
            'question_params': ''
        };
        StoryEditor.createQuestions(params, function() {
            modal.modal('hide');
        });
    });    
})();
JS;
/** @var $this yii\web\View  */
$this->registerJs($js);