<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model backend\models\test\UpdateForm */
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Изменить вариант теста</h4>
    </div>
<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'options' => [
        'id' => 'update-test-variant-form'
    ]]); ?>
    <div class="modal-body">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'header')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'description_text')->textarea(['rows' => 4]) ?>
        <?= $form->field($model, 'incorrect_answer_text')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'question_params')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'taxonName')->dropDownList([], ['data-value' => $model->taxonName]) ?>
        <?= $form->field($model, 'taxonValue')->dropDownList([], ['data-value' => $model->taxonValue]) ?>
    </div>
    <div class="modal-footer">
        <?= Html::submitButton('Изменить вариант теста', ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
$('#update-test-variant-form')
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
                    fillTestVariantsTable(response.params);
                }
                else {
                    toastr.error(response.errors);
                }
            })
        .always(function() {
            $('#update-test-variant-modal').modal('hide');
        });
        return false;
    })
    .on('submit', function(e) {
        e.preventDefault();
    });
JS;
$this->registerJs($js);
