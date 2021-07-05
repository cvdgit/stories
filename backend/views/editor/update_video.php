<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model backend\models\editor\BaseForm */
/** @var $action array */
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Редактировать блок</h4>
    </div>
<?php $form = ActiveForm::begin(['id' => 'block-form', 'action' => $action]); ?>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-4">
                <?= $this->render($model->view, ['form' => $form, 'model' => $model]) ?>
            </div>
            <div class="col-md-8">
                <div id="video-preview" style="width: 700px; height: 500px"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <?= $form->field($model, 'slide_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'block_id', ['inputOptions' => ['class' => 'editor-block-id']])->hiddenInput()->label(false) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton('Закрыть', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
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
                toastr.success('Изменения успешно сохранены');
                
                WikidsVideo.destroyPlayers();
                WikidsVideo.reset();
                $('#update-block-modal').find('#video-preview').html($(response.html).find('div.wikids-video-player'));
                WikidsVideo.createPlayer($('#update-block-modal').find('#video-preview'));
                
                StoryEditor.updateSlideBlock(response.block_id, response.html);
            }
            else {
                toastr.error(response.errors);
            }
        })
        //.always(function() {
        //    $('#update-block-modal').modal('hide');
        //});
        return false;
    })
    .on('submit', function(e) {
        e.preventDefault();
    });
JS;
$this->registerJs($js);
