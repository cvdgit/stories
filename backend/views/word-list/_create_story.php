<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model backend\models\test\CreateStoryForm */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Тест и история</h4>
</div>
<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'options' => [
        'id' => 'create-test-and-story-form'
    ]]); ?>
<div class="modal-body">
    <?= $form->field($model, 'test_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'test_answer_type')->dropDownList(\common\models\test\AnswerType::asArray()) ?>
    <?= $form->field($model, 'story_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'word_list_id')->hiddenInput()->label(false) ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton('Создать тест и историю', ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
$('#create-test-and-story-form')
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
                    toastr.success('История успшено создана');
                }
                else {
                    toastr.error(response.message);
                }
            })
        .fail(function(response) {
            toastr.error(response.responseText);
        })
        .always(function() {
            $('#create-test-and-story-modal').modal('hide');
        });
        return false;
    })
    .on('submit', function(e) {
        e.preventDefault();
    });
JS;
$this->registerJs($js);