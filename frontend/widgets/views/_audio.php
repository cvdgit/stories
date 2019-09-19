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
<div class="row">
    <div class="col-md-12">
        <div class="text-center" style="padding: 20px">
            <audio id="story-audio-player" controls></audio>
        </div>
    </div>
</div>
