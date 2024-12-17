<?php

declare(strict_types=1);

use frontend\assets\MentalMapAsset;
use frontend\assets\TestAsset;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var int $getCourseUserId
 * @var string $mentalMapId
 */

TestAsset::register($this);
MentalMapAsset::register($this);

$val = Json::htmlEncode($getCourseUserId);
$this->registerJs("
window.getCourseUserId = $val;
window.mentalMapId = '$mentalMapId';
");

$this->registerCss($this->renderFile('@frontend/views/getcourse/mental-map.css'));
$this->registerJs($this->renderFile('@frontend/views/getcourse/loader.js'));
?>
<div style="height: 100%; display: flex; flex-direction: column">
    <div id="wrap" style="height: 100%; background-color: #eee; border: 1px #808080 solid; display: flex; align-items: center; flex-direction: column; justify-content: center">
        <h2 class="h3" style="margin-bottom: 40px">Загрузка карты знаний...</h2>
        <img id="loader" width="60" src="/img/loading.gif" alt="">
    </div>
</div>
