<?php

declare(strict_types=1);

use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var array $mapOrder
 */

$this->registerJs('window.mentalMapsOrder = ' . Json::encode($mapOrder));
$this->registerCss(
    $this->renderFile('@backend/views/editor/mental-map/_create_ai.css')
);
?>
<div>
    <div class="content-editor-wrap">
        <div id="content-editor" class="text-content"></div>
    </div>
    <div class="ai-mental-maps-order"></div>
</div>
<div style="margin-block: 20px; text-align: center">
    <button id="ai-maps-create" class="btn btn-primary" type="button">Создать ментальные карты</button>
</div>
