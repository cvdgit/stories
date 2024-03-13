<?php

declare(strict_types=1);

use frontend\assets\GameAsset;
use yii\web\View;

/**
 * @var View $this
 */

GameAsset::register($this);

$this->registerJs($this->renderFile("@frontend/views/game/show.js"));
?>
<div id="unity-container" class="unity-desktop">
    <canvas id="unity-canvas" width=1280 height=720 tabindex="-1"></canvas>
</div>
