<?php
use backend\widgets\SelectWordListWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\test\import\ImportFromWordList;
/** @var ImportFromWordList $model */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Импорт вопросов из списка слов</h4>
</div>
<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'options' => [
        'id' => 'import-from-word-list-form'
    ],
]); ?>
<div class="modal-body">
    <?= $form->field($model, 'word_list_id')->widget(SelectWordListWidget::class) ?>
    <?= $form->field($model, 'question_type')->dropDownList($model->getQuestionTypes()) ?>
    <?= $form->field($model, 'number_answers')->textInput() ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton('Импортировать', ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
$('#import-from-word-list-form')
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
            if (response && response.success) {
                location.reload();
            }
            else {
                toastr.error(response.message);
            }
        })
        .always(function() {
            $('#import-from-word-list-modal').modal('hide');
        });

        return false;
    })
    .on('submit', function(e) {
        e.preventDefault();
    });
JS;
$this->registerJs($js);
