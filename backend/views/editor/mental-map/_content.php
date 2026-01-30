<?php

declare(strict_types=1);

use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var array $contents
 * @var array $mapOrder
 */

$this->registerJs('window.contentItems = ' . Json::encode($contents));
$this->registerJs('window.mapOrder = ' . Json::encode($mapOrder));
?>
<div class="content-mm-container"></div>
