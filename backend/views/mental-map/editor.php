<?php

declare(strict_types=1);

use backend\assets\MentalMapAsset;
use yii\web\View;

/**
 * @var View $this
 * @var string $id
 * @var string $name
 * @var string|null $returnUrl
 */

$this->registerJs("window.mentalMapId = '$id'", View::POS_HEAD);
$this->registerJs("window.mentalMapReturnUrl = '$returnUrl'", View::POS_HEAD);
MentalMapAsset::register($this);

$this->title = 'Редактор ментальный карт - ' . $name;
?>
<div id="app"></div>
