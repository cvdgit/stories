<?php

use common\helpers\StoryHelper;
use common\helpers\Url;
use frontend\assets\RecorderAsset;
use yii\helpers\Html;

/** @var $this yii\web\View */
RecorderAsset::register($this);

$css = <<<CSS
.recorder-audio {
    text-align: center;
}
.recorder-audio li {
    display: table;
}
.recorder-audio li a {
    display: table-cell;
    vertical-align: middle;
    height: 54px;
    padding-left: 20px;
}
CSS;
$this->registerCss($css);

/** @var $model common\models\Story */
$createTrackAction = Url::to(['player/create-audio-track', 'story_id' => $model->id]);
$getTrackAction = Url::to(['player/get-track']);

/** @var $currentTrack common\models\StoryAudioTrack */
$track = \yii\helpers\Json::htmlEncode($currentTrack);

$js = <<<JS

$("#create-audio-track").on("click", function(e) {
    e.preventDefault();
    var promise = $.get("$createTrackAction");
    promise.done(function(data) {
        if (data && data.success) {
            $("<option/>")
                .val(data.track.id)
                .text(data.track.name)
                .attr("selected", "selected")
                .appendTo("#audio-track-list");
            $("#audio-track-list").change();
        }
    });
});

$("#audio-track-list").on("change", function() {
    
    var trackID = $(this).val();
    location.href = location.origin + location.pathname + '?track_id=' + trackID + location.hash;
    
    /*
    var trackID = $(this).val();
    var promise = $.get("$getTrackAction", {"track_id": trackID});
    promise.done(function(data) {
        
        if (data && data.success) {
            
            WikidsPlayer.setCurrentTrack(data.track);
            
            var recorderControls = $(".recorder-controls");
            if (data.track.type === 1) {
                recorderControls.show();
            }
            else {
                recorderControls.hide();
            }
        }
    });
    */
    
});
JS;
$this->registerJs($js);
?>
<div class="row">
    <div class="col-md-3">
        <div style="padding: 20px; text-align: center">
            <?= Html::dropDownList('audio_track', ($currentTrack !== null ? $currentTrack->id : ''), StoryHelper::getStoryAudioTrackArray($model), ['id' => 'audio-track-list']) ?>
            <?= Html::a('Новая дорожка', '#', ['id' => 'create-audio-track', 'style' => 'font-weight: bold; margin-top: 10px; display: block']) ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="recorder-controls" style="padding: 20px; display: <?= $currentTrack && $currentTrack->isOriginal() ? 'none' : 'block' ?>">
            <button id="audioRecord">Записать</button>
            <button id="audioPause">Пауза</button>
            <button id="audioStop">Стоп</button>
        </div>
    </div>
    <div class="col-md-5">
        <div class="recorder-audio" style="padding: 20px">
            <button id="mergeAllSlideAudio" style="display: none; margin: 0 10px 10px 10px" onclick="WikidsPlayer.mergeAllAndSetSlideAudio()">Объединить все и применить</button>
            <ol class="list-unstyled" id="recordingsList"></ol>
        </div>
    </div>
</div>
