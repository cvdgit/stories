<?php
use backend\widgets\CreateAudioFileWidget;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Изменить';
/** @var backend\models\audio_file\UpdateAudioFileModel $model */
$this->params['breadcrumbs'] = [
    ['label' => 'Все аудио файлы', 'url' => ['audio-file/index']],
    $this->title,
];
?>
<div class="audio-file-update">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-8">
            <?php $form = ActiveForm::begin(['id' => 'audio-file-update-form']) ?>
            <?= $form->field($model, 'name')->textInput(['autocomplete' => 'off']) ?>
            <?= CreateAudioFileWidget::widget([
                'audioFileUrl' => $model->getAudioFileUrl(),
                'callback' => 'audioFileCallback',
                'updateMode' => true,
            ]) ?>
            <div class="hide">
                <?= $form->field($model, 'audio_file')->fileInput() ?>
            </div>
            <?= $form->field($model, 'audio_file_name')->hiddenInput()->label(false) ?>
            <div class="form-group form-group-controls">
                <?= Html::submitButton('Изменить', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<?php
$fieldId = Html::getInputId($model, 'audio_file');
$this->registerJs(<<<JS
function audioFileCallback(blob) {

    var container = new DataTransfer();
    var fileName = new Date().getTime() + '.wav';
    container.items.add(new File([blob], fileName));

    var form = document.getElementById('audio-file-update-form');
    var fieldId = '$fieldId';
    var field = form.querySelector('#' + fieldId);
    field.files = container.files;
}
JS
);
