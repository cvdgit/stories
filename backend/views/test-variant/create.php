<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model backend\models\test\CreateForm */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Создать вариант теста</h4>
</div>
<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'options' => [
        'id' => 'test-variant-form',
        'class' => 'test-variant-form',
]]); ?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'header')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'description_text')->textarea(['rows' => 4]) ?>
            <?= $form->field($model, 'incorrect_answer_text')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'question_params')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'neo_question_id')->hiddenInput()->label(false) ?>
            <div class="question-config">
                <p>Загрузка параметров вопроса...</p>
            </div>
        </div>
        <div class="col-md-6">
            <?= $this->render('_wrong_answers', ['form' => $form, 'model' => $model]) ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::submitButton('Создать вариант теста', ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
$('#test-variant-form')
    .on('beforeSubmit', function(e) {
        e.preventDefault();
        
        fillTestVariantConfig(this, 'createform-question_params');

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
            $('#test-variant-modal').modal('hide');
        });
        return false;
    })
    .on('submit', function(e) {
        e.preventDefault();
    });
JS;
$this->registerJs($js);