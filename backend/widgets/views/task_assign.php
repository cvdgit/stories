<?php
use backend\widgets\WikidsDatePicker;
use common\models\StudyGroup;
use common\models\StudyTask;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model backend\models\study_task\StudyTaskAssignForm */
?>
<button class="btn btn-primary" type="button" id="assign-task">Назначить задание</button>

<div class="modal fade" id="task-assign-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin(['id' => 'assign-task-form', 'action' => ['study-task/assign']]) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Назначить задание</h4>
            </div>
            <div class="modal-body">
                <?= $form->field($model, 'study_task_id')->dropDownList(StudyTask::asArray(), ['prompt' => 'Выберите задание']) ?>
                <?= $form->field($model, 'study_group_id')->dropDownList(StudyGroup::asArray(), ['prompt' => 'Выберите группу']) ?>
                <?= $form->field($model, 'expired_at')->widget(WikidsDatePicker::class) ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Назначить задание', ['class' => 'btn btn-primary']) ?>
                <button class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<?php
$js = <<<JS
(function() {
    'use strict';
    
    var modal = $('#task-assign-modal');
    $('#assign-task').on('click', function() {
        modal.modal('show');
    });
    
    $('#assign-task-form').on('beforeSubmit', function(e) {
        e.preventDefault();
        var btn = $(this).find('button[type=submit]').button('loading');
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
                    toastr.error(response.errors);
                }
            })
            .fail(function(response) {
                toastr.error(response.responseJSON.message);
            })
            .always(function() {
                btn.button('reset');
            });
            return false;
        })
        .on('submit', function(e) {
            e.preventDefault();
        });
})();
JS;
$this->registerJs($js);
