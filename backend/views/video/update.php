<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model backend\models\video\UpdateVideoForm */

\frontend\assets\PlyrAsset::register($this);

$this->title = 'Изменить видео';
?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->textInput() ?>
    <?= $form->field($model, 'video_id')->textInput() ?>
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-offset-3 col-md-6">
            <div class="plyr__video-embed" id="video-container" data-plyr-provider="youtube" data-plyr-embed-id="<?= $model->video_id ?>"></div>
        </div>
    </div>
    <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Проверить', ['video/check', 'id' => $model->model_id], ['class' => 'btn btn-success', 'id' => 'check-video']) ?>
    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<< JS

var player = new Plyr('#video-container', {});

$("#check-video").on("click", function(e) {
    e.preventDefault();
    $.get($(this).attr("href")).done(function(data) {
        if (data && data.success) {
            toastr.success("Успешно");
        }
        else {
            toastr.error("Ошибка");
        }
    });
});
JS;

$this->registerJs($js);
