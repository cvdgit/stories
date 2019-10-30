<?php

use common\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model backend\models\video\UpdateVideoForm */

\frontend\assets\PlyrAsset::register($this);

$this->title = 'Изменить видео';
?>
<div class="row">
    <div class="col-md-6">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->textInput() ?>
    <?= $form->field($model, 'video_id')->textInput()->hint(Html::a('Истории', '#video-stories-modal', ['data-toggle' => 'modal', 'class' => 'btn btn-default'])) ?>
    <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Проверить', ['video/check', 'id' => $model->model_id], ['class' => 'btn btn-success', 'id' => 'check-video']) ?>
    <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-6">
        <div style="margin-top: 72px">
            <div class="plyr__video-embed" id="video-container" data-plyr-provider="youtube" data-plyr-embed-id="<?= $model->video_id ?>"></div>
        </div>
    </div>
</div>

<?php
$action = Url::to(['video/get-stories', 'video_id' => '_VIDEO_']);
$js = <<< JS

var player = new Plyr('#video-container', {});

$("#check-video").on("click", function(e) {
    e.preventDefault();
    $.get($(this).attr("href")).done(function(data) {
        if (data && data.success) {
            toastr.success("Успешно");
        }
        else {
            toastr.error("Видео не найдено");
        }
    });
});

var list = $("#video-stories-list");
$("#video-stories-modal")
    .on("show.bs.modal", function() {
        list.append('<div class="col-md-12"><h3>Загрузка...</h3></div>');
    })
    .on("shown.bs.modal", function() {
        list.empty();
        $.get("$action".replace('_VIDEO_', $("#updatevideoform-video_id").val())).done(function(data) {
            data.forEach(function(elem) {
                 var img = $("<img/>").attr("src", elem.cover);
                 list.append('<div class="col-xs-6 col-md-3"><a href="#" class="thumbnail">' + img.prop("outerHTML") + '<div class="caption"><h4>' + elem.title + '</h4></div></a></div>');
            });
        });
    })
    .on("hide.bs.modal", function() {
        list.empty();
    });
JS;
$this->registerJs($js);
?>

<div class="modal fade" id="video-stories-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Истории с этим видео</h4>
            </div>
            <div class="modal-body">
                <div id="video-stories-list" class="row"></div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
