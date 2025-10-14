<?php

declare(strict_types=1);

use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var array $contents
 */

$this->registerJs('window.contentItems = ' . Json::encode($contents));
?>
<div class="content-mm-container"></div>
