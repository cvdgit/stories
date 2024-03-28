<?php

declare(strict_types=1);

use backend\modules\gpt\assets\PdfChatAsset;
use yii\web\View;

/**
 * @var View $this
 */

PdfChatAsset::register($this);
$this->title = "Pdf Chat";
?>
<div id="app"></div>
