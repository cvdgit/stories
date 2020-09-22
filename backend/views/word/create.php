<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model backend\models\CreateWordForm */
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Новое слово</h4>
    </div>
<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'options' => [
        'id' => 'create-test-word-form'
    ]]); ?>
    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="modal-footer">
        <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
$('#create-test-word-modal')
    .on('shown.bs.modal', function () {
        $('input[type=text]:first', this).focus();
    })
    .on('show.bs.modal', function () {
        $('#create-test-word-form')[0].reset();
        
    });
$('#create-test-word-form')
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
            $('#create-test-word-modal').modal('hide');
        });
        return false;
    })
    .on('submit', function(e) {
        e.preventDefault();
    });
JS;
$this->registerJs($js);