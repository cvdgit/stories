<?php
use backend\models\audio_file\CreateAudioFileModel;
use backend\widgets\CreateAudioFileWidget;
use yii\widgets\ActiveForm;
/** @var $updateQuestionModel backend\models\question\UpdateQuestion */
$model = new CreateAudioFileModel();
$model->question_id = $updateQuestionModel->getModelID();
?>
<div class="modal fade" tabindex="-1" id="create-audio-file-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'action' => ['question/create-audio-file'],
                'id' => 'create-audio-file-form',
            ]) ?>
            <div class="modal-header">
                <h5 class="modal-title">Создать аудио файл</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?= $form->field($model, 'name')->textInput(['autocomplete' => 'off']) ?>
                <?= CreateAudioFileWidget::widget([
                    'questionId' => $updateQuestionModel->getModelID(),
                    'audioFileUrl' => $updateQuestionModel->getAudioFileUrl(),
                    'callback' => 'audioFileCallback',
                ]) ?>
                <?= $form->field($model, 'audio_file_name')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'question_id')->hiddenInput()->label(false)->error(false) ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Создать</button>
                <button class="btn btn-secondary" data-dismiss="modal">Отмена</button>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<?php
$this->registerJs(<<<JS
var audioFileData;
function audioFileCallback(blob) {
    audioFileData = blob;
    $('#createaudiofilemodel-audio_file_name').val(new Date().getTime() + '.wav').blur();
}
function getAudioFileData() {
    return audioFileData;
}
(function() {
    $('#create-audio-file-form')
        .on('beforeSubmit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            formData.append('CreateAudioFileModel[audio_file]', getAudioFileData(), $('#createaudiofilemodel-audio_file_name').val());

            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(response) {
                if (response) {
                    if (response.success) {
                        var selectize = $('#updatequestion-audio_file_id')[0].selectize;
                        selectize.addOption({id: response.audio_file_id, name: response.audio_file_name});
                        selectize.refreshOptions();
                        selectize.addItem(response.audio_file_id);
                    }
                    else {
                        toastr.error(response.message);
                    }
                } 
                else {
                    toastr.error('Неизвестная ошибка');
                }
                $('#create-audio-file-modal').modal('hide');
            });
            
            return false;
        })
        .on('submit', function(e) {
            e.preventDefault();
        });
})();
JS
);