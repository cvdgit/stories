<?php

declare(strict_types=1);

use yii\web\View;

/**
 * @var View $this
 * @var string $title
 * @var string $content
 */

$this->registerCss(<<<CSS
.header-block {
    display: flex;
    padding-top: 1rem;
    padding-bottom: 0.5rem;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 20px;
}
CSS
);
?>
<div class="header-block">
    <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2" class="h2"><?= $title ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group">
            <?= $content ?>
        </div>
    </div>
</div>
