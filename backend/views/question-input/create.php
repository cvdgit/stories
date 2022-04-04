<?php
use yii\bootstrap\ActiveForm;
/** @var backend\models\question\input\CreateInputQuestionForm $model */
?>
<?php $form = ActiveForm::begin(['id' => 'create-test-question-form']) ?>
<div class="modal-header">
    <h5 class="modal-title">Создать вопрос с полем для ввода ответа</h5>
    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
</div>
<div class="modal-body">
    <?= $form->field($model, 'question_name')->textInput() ?>
    <?= $form->field($model, 'correct_answer_name')->textInput() ?>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" type="submit">Создать вопрос</button>
    <button class="btn btn-secondary" data-dismiss="modal">Отмена</button>
</div>
<?php ActiveForm::end() ?>
<?php
$this->registerJs(<<<JS
(function() {
$('#create-test-question-form')
    .on('beforeSubmit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(response) {
            if (response) {
                if (response.success) {
                    location.reload();
                }
                else {
                    toastr.error(response.message);
                }
            } 
            else {
                toastr.error('Неизвестная ошибка');
            }
            $('#create-test-question-modal').modal('hide');
        });
        return false;
    })
    .on('submit', function(e) {
        e.preventDefault();
    });
})();
JS
);