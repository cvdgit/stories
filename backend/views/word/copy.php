<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model backend\models\UpdateWordForm */
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Копировать слово</h4>
    </div>
<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'options' => [
        'id' => 'update-test-word-form'
    ]]); ?>
    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'correct_answer')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="modal-footer">
        <?= Html::submitButton('Создать копию', ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
$('#update-test-word-form')
    .on('beforeSubmit', function(e) {
        e.preventDefault();
        var form_data = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: form_data, 
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(response) {
                if (response && response.success) {
                    fillTestWordsTable(response.params);
                }
                else {
                    toastr.error(response.errors);
                }
            })
        .always(function() {
            $('#update-test-word-modal').modal('hide');
        });
        return false;
    })
    .on('submit', function(e) {
        e.preventDefault();
    });
JS;
$this->registerJs($js);