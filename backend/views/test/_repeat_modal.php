<?php
use yii\bootstrap\ActiveForm;
/** @var common\models\StoryTest $model */
/** @var backend\models\test\ChangeRepeatForm $repeatChangeModel */
/** @var string $inputId */
?>
<div class="modal fade" tabindex="-1" id="change-repeat-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'action' => ['test/change-repeat', 'test_id' => $model->id],
                'id' => 'change-repeat-form',
            ]) ?>
            <div class="modal-header">
                <h5 class="modal-title">Редактировать</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?= $form->field($repeatChangeModel, 'repeat')->dropDownList($repeatChangeModel->getDropdownItems()) ?>
            </div>
            <div class="modal-footer">
                <div class="alert alert-info text-left" role="alert">После изменения будет очищена история прохождения у всех пользователей по этому тесту</div>
                <button class="btn btn-primary" type="submit">Изменить</button>
                <button class="btn btn-secondary" data-dismiss="modal">Отмена</button>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<?php
$this->registerJs(<<<JS
(function() {
    $('#change-repeat-form')
        .on('beforeSubmit', function(e) {
            e.preventDefault();
            var btn = $(this).find('button[type=submit]');
            btn.button('loading');
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: new FormData(this),
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false
            })
            .fail(function(response) {
                toastr.error(response.responseText);
                $('#change-repeat-modal').modal('hide');
            })
            .always(function() {
                btn.button('reset');
            })
            .done(function(response) {
                if (response) {
                    if (response.success) {
                        $('#$inputId').val(response.repeat);
                        toastr.success(response.message);
                    }
                    else {
                        toastr.error(response.message);
                    }
                } 
                else {
                    toastr.error('Неизвестная ошибка');
                }
                $('#change-repeat-modal').modal('hide');
            });
            return false;
        })
        .on('submit', function(e) {
            e.preventDefault();
        });
})();
JS
);