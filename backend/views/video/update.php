<?php

declare(strict_types=1);

use common\helpers\Url;
use frontend\assets\PlyrAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model backend\models\video\UpdateVideoForm */

PlyrAsset::register($this);

$this->title = 'Изменить видео';
$this->params['breadcrumbs'] = [
    ['label' => 'Видео', 'url' => ['video/index']],
    $this->title,
];
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
        <div>
            <div class="plyr__video-embed" id="video-container">
                <iframe
                    src="https://www.youtube.com/embed/<?= $model->video_id; ?>"
                    allowfullscreen
                    allowtransparency
                    allow="autoplay"
                ></iframe>
            </div>
        </div>
    </div>
</div>

<?php
$action = Url::to(['video/get-stories', 'video_id' => '_VIDEO_']);
$js = <<< JS

const player = Plyr.setup('#video-container', {
    captions: { active: true },
    youtube: {
        noCookie: false,
        rel: 0,
        showinfo: 0,
        iv_load_policy: 3,
        modestbranding: 1,
        cc_load_policy: 1,
        cc_lang_pref: 'en',
        enablejsapi: 1,
        playsinline: 1,
        fmt: 'vtt'
    }
});

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
        $.get("$action".replace('_VIDEO_', $("#updatevideoform-video_id").val())).done(function(data) {
            list.empty();
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
