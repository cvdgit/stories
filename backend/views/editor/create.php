<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model backend\models\editor\BaseForm */
/** @var $action array */
/** @var $widgetStoryModel common\models\Story */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Новый блок</h4>
</div>
<?php $form = ActiveForm::begin(['id' => 'block-form', 'action' => $action]); ?>
<div class="modal-body">
    <?= $this->render($model->view, ['form' => $form, 'new' => true, 'model' => $model, 'widgetStoryModel' => $widgetStoryModel]) ?>
</div>
<div class="modal-footer">
    <?= $form->field($model, 'story_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'slide_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'block_id', ['inputOptions' => ['class' => 'editor-block-id']])->hiddenInput()->label(false) ?>
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'style' => 'margin-right: 20px']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
$('#block-form')
    .on('beforeSubmit', function(e) {
        e.preventDefault();
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
                StoryEditor.createSlideBlock(response.html);
                toastr.success('Блок успешно создан');
            }
            else {
                toastr.error(response.errors);
            }
        })
        .always(function() {
            $('#create-block-modal').modal('hide');
        });
        return false;
    })
    .on('submit', function(e) {
        e.preventDefault();
    });
JS;
if (get_class($model) !== \backend\models\editor\MentalMapForm::class) {
    $this->registerJs($js);
}
