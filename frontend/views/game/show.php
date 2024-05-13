<?php

declare(strict_types=1);

use yii\web\View;

/**
 * @var View $this
 * @var string $config
 * @var string $loaderConfig
 * @var string $folder
 */

$this->registerJs(<<<JS
(function() {
    window.gameLoaderConfig = $loaderConfig
    window.gameConfig = $config
})();
JS
);

$this->title = "Wikids Game";

$this->registerCss(<<<CSS
body { padding: 0; margin: 0 }
#unity-container { position: absolute }
#unity-container.unity-desktop { left: 50%; top: 50%; transform: translate(-50%, -50%) }
#unity-container.unity-mobile { position: fixed; width: 100%; height: 100% }
#unity-canvas { background: #231F20 }
.unity-mobile #unity-canvas { width: 100%; height: 100% }
#unity-loading-bar { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); display: none }
#unity-logo { width: 154px; height: 130px; background: url('$folder/TemplateData/unity-logo-dark.png') no-repeat center }
#unity-progress-bar-empty { width: 141px; height: 18px; margin-top: 10px; margin-left: 6.5px; background: url('$folder/TemplateData/progress-bar-empty-dark.png') no-repeat center }
#unity-progress-bar-full { width: 0%; height: 18px; margin-top: 10px; background: url('$folder/TemplateData/progress-bar-full-dark.png') no-repeat center }
#unity-footer { position: relative }
.unity-mobile #unity-footer { display: none }
#unity-webgl-logo { float:left; width: 204px; height: 38px; background: url('$folder/TemplateData/webgl-logo.png') no-repeat center }
#unity-build-title { float: right; margin-right: 10px; line-height: 38px; font-family: arial; font-size: 18px }
#unity-fullscreen-button { cursor:pointer; float: right; width: 38px; height: 38px; background: url('$folder/TemplateData/fullscreen-button.png') no-repeat center }
#unity-warning { position: absolute; left: 50%; top: 5%; transform: translate(-50%); background: white; padding: 10px; display: none }
CSS
);

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
