<?php

declare(strict_types=1);

use backend\modules\gpt\assets\GptAsset;
use yii\web\View;

/**
 * @var View $this
 */

GptAsset::register($this);
$this->title = "ChatGPT";
?>
<div id="app"></div>
