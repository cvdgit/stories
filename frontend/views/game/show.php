<?php

declare(strict_types=1);

use frontend\assets\GameAsset;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 */

GameAsset::register($this);

/*$data = Json::encode([
    "id":
]);*/

$this->registerJs($this->renderFile("@frontend/views/game/show.js"));
?>
<div id="unity-container" class="unity-desktop">
    <canvas id="unity-canvas" width=1280 height=720 tabindex="-1"></canvas>
    <div id="unity-loading-bar">
        <div id="unity-logo"></div>
        <div id="unity-progress-bar-empty">
            <div id="unity-progress-bar-full"></div>
        </div>
    </div>
    <div id="unity-warning"> </div>
    <div id="unity-footer">
        <div id="unity-webgl-logo"></div>
        <div id="unity-fullscreen-button"></div>
        <div id="unity-build-title">WikidsGame</div>
    </div>
</div>
