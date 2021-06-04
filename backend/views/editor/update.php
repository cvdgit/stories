<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model backend\models\editor\BaseForm */
/** @var $action array */
$css = <<< CSS
.block-actions a {
    width: 32px;
    height: 32px;
    text-align: center;
    line-height: 32px;
    display: inline-block;
    color: #333;
}
CSS;
$this->registerCss($css);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Редактировать блок</h4>
</div>
<?php $form = ActiveForm::begin(['id' => 'block-form', 'action' => $action]); ?>
<div class="modal-body">
    <?= $this->render($model->view, ['form' => $form, 'model' => $model]) ?>
</div>
<div class="modal-footer">
    <?= $form->field($model, 'slide_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'block_id', ['inputOptions' => ['class' => 'editor-block-id']])->hiddenInput()->label(false) ?>
    <div class="text-left">
        <span class="block-actions">
            <?= Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['title' => 'Удалить блок', 'data-toggle' => 'tooltip', 'id' => 'delete-block']) ?>
            <?= Html::a('<i class="glyphicon glyphicon-duplicate"></i>', '#', ['title' => 'Копировать блок', 'data-toggle' => 'tooltip', 'id' => 'duplicate-block']) ?>
        </span>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary pull-right']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
var form = $('#block-form');
form
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
                StoryEditor.loadSlide(response.slide_id);
            }
            else {
                toastr.error(response.errors);
            }
        })
        .always(function() {
            $('#update-block-modal').modal('hide');
        });
 
        return false;
    })
    .on('submit', function(e) {
        e.preventDefault();
    });
$('#delete-block').on('click', function(e) {
    e.preventDefault();
    var blockID = form.find('.editor-block-id').val();
    StoryEditor.deleteBlock(blockID);
    $('#update-block-modal').modal('hide');
});
$('#duplicate-block').on('click', function(e) {
    e.preventDefault();
    
});
JS;
$this->registerJs($js);
