<?php

use frontend\assets\RecorderAsset;

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
?>
<div class="row">
    <div class="col-md-6">
        <div class="recorder-controls" style="padding: 20px">
            <button class="btn btn-small" id="audioRecord">Записать</button>
            <button class="btn" id="audioPause">Пауза</button>
            <button class="btn" id="audioStop">Стоп</button>
        </div>
    </div>
    <div class="col-md-6">
        <div class="recorder-audio" style="padding: 20px">
            <button id="mergeAllSlideAudio" style="display: none; margin: 0 10px 10px 10px" onclick="WikidsPlayer.mergeAllAndSetSlideAudio()">Объединить все и применить</button>
            <ol class="list-unstyled" id="recordingsList"></ol>
        </div>
    </div>
</div>
