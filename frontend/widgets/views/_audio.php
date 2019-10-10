<?php

use yii\helpers\Json;

/** @var $files array */
/** @var $path string */
$filesJSON = Json::htmlEncode($files);
$js = <<<JS
    var audioFiles = $filesJSON,
        path = "$path";
    var customSort = function (a, b) {
        return (Number(a.match(/(\d+)/g)[0]) - Number((b.match(/(\d+)/g)[0])));
    };
    audioFiles.sort(customSort);
    var audioPlayer = document.getElementById("story-audio-player");
    audioPlayer.src = path + audioFiles.shift();
    audioPlayer.onended = function() {
        var audioFilePath = audioFiles.shift();
        if (audioFilePath) {
            audioPlayer.src = path + audioFilePath;
            audioPlayer.load();
            audioPlayer.play();
        }
    };
JS;

/** @var $this yii\web\View */
$this->registerJs($js);
?>
<div class="row wikids-story-audio" style="margin: 10px auto">
    <div class="col-xs-12 col-sm-6 col-md-6">
        <div class="text-right wikids-story-audio-text">
            <span style="height: 50px; line-height: 50px">Воспроизвести аудио:</span>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6">
        <div class="wikids-story-audio-player">
            <audio id="story-audio-player" controls></audio>
        </div>
    </div>
</div>
