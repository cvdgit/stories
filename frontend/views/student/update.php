<?php
use dosamigos\datepicker\DatePicker;
use frontend\models\UserStudentForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
/**
 * @var $model UserStudentForm
 * @var $updateRoute array
 * @var $this View
 */
$form = ActiveForm::begin([
    'action' => $updateRoute,
    'enableClientValidation' => true,
    'id' => 'update-child-form',
]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Изменить профиль ученика</h4>
</div>
<div class="modal-body">
    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
    <?= $form->field($model, 'birth_date')->widget(DatePicker::class, [
        'language' => 'ru',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ],
        'options' => [
            'autocomplete' => 'off',
        ],
    ]) ?>
    <?= $form->field($model, 'class')->dropDownList($model->getClassArray(), ['prompt' => 'Выберите класс']) ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton('Изменить', ['class' => 'btn btn-small']) ?>
    <button type="button" class="btn btn-small" data-dismiss="modal">Отмена</button>
</div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
$('#update-child-form').on('beforeSubmit', function (event) {
    event.preventDefault();
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
                fillUserStudentsTable(response.students);
                toastr.success('Данные успешно изменены');
            }
            else {
                toastr.error(response.errors.join(', '));
            }
        })
    .always(function() {
        $('#create-child-modal').modal('hide');
    });
    return false;
});
JS;
$this->registerJs($js);
