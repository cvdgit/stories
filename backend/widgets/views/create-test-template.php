<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model backend\models\test_template\CreateTestTemplateForm */
?>
<?= Html::a('Сохранить как шаблон', '#create-test-template-modal', ['data-toggle' => 'modal', 'class' => 'btn btn-default']) ?>
<div class="modal fade" id="create-test-template-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin(['id' => 'create-test-template-form', 'action' => ['test-template/create']]) ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Создать шаблон</h4>
            </div>
            <div class="modal-body">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'test_id')->hiddenInput()->label(false) ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Создать шаблон', ['class' => 'btn btn-primary']) ?>
                <button class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<?php
$js = <<<JS
(function() {
    var form = $('#create-test-template-form');
    yiiModalFormInit(form, 
        function(response) {
            if (response && response.success) {
                location.href = response.url;
            }
            else {
                toastr.error(response['error'] || 'Неизвестная ошибка');
            }
        },
        function(response) {
            toastr.error(response.responseJSON.message);
        }
    );
})();
JS;
$this->registerJs($js);