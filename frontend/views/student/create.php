<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
/* @var $model frontend\models\CreateStudentForm */
$form = ActiveForm::begin([
    'action' => ['student/create'],
    'enableClientValidation' => true,
    'id' => 'create-child-form',
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Добавить ребенка</h4>
    </div>
    <div class="modal-body">
        <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?php echo $form->field($model, 'age_year')->textInput() ?>
    </div>
    <div class="modal-footer">
        <?php echo Html::submitButton('Добавить', ['class' => 'btn btn-small']) ?>
        <button type="button" class="btn btn-small" data-dismiss="modal">Отмена</button>
    </div>
<?php ActiveForm::end(); ?>

<?php
$js = <<< JS
$('#create-child-form').on('beforeSubmit', function (event) {
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
            }
        })
    .always(function() {
        $('#create-child-modal').modal('hide');
    });
    return false;
});
JS;
/** @var $this yii\web\View */
$this->registerJs($js);