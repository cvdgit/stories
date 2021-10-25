<?php
use backend\models\study_group\ImportUsersFromTextForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $groupModel common\models\StudyGroup */
$model = new ImportUsersFromTextForm();
$model->study_group_id = $groupModel->id;
?>
<div class="modal fade" id="import-users-from-text-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Импорт пользователей</h4>
            </div>
            <?php $form = ActiveForm::begin([
                'action' => ['study-group/import-users-from-text'],
                'id' => 'import-users-from-text-form'
            ]); ?>
            <div class="modal-body">
                <div class="alert alert-info text-left">Формат строки: email|Фамилия|Имя</div>
                <?= $form->field($model, 'text')->textarea(['cols' => 30, 'rows' => 18])->label(false) ?>
                <?= $form->field($model, 'study_group_id')->hiddenInput()->label(false) ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Импортировать', ['class' => 'btn btn-primary']) ?>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$js = <<<JS
(function() {
    var modal = $('#import-users-from-text-modal');
    var form = $('#import-users-from-text-form', modal);
    modal
        .on('show.bs.modal', function() {
            form[0].reset();
        });
    var doneCallback = function(response) {
        if (response && response.success) {
            location.reload();
        }
        else {
            toastr.error(response['error'] || 'Неизвестная ошибка');
        }
    };
    var failCallback = function(response) {
        toastr.error(response.responseJSON.message);
    }
    var alwaysCallback = function() {
        modal.modal('hide');
    }
    yiiModalFormInit(form, doneCallback, failCallback, alwaysCallback);
})();
JS;
$this->registerJs($js);