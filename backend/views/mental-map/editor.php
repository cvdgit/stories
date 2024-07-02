<?php

declare(strict_types=1);

use backend\assets\MentalMapAsset;
use yii\web\View;

/**
 * @var View $this
 * @var string $id
 * @var string|null $returnUrl
 */

$this->registerJs("window.mentalMapId = '$id'", View::POS_HEAD);
$this->registerJs("window.mentalMapReturnUrl = '$returnUrl'", View::POS_HEAD);
MentalMapAsset::register($this);
?>
<div id="app"></div>
