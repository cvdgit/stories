<?php

declare(strict_types=1);

use yii\web\View;

/**
 * @var View $this
 * @var array $historyItem
 */
?>
<div class="slide-wrap">
    <div><?= $historyItem['slideNumber'] ?></div>
    <div class="slide-inner" data-view="slide">
        <div class="reveal">
            <div class="slides">
                <?= $historyItem['slide'] ?>
            </div>
        </div>
        <?php if ($historyItem['previouslyViewed']): ?>
        <div class="previously-viewed">Просмотрено ранее</div>
        <?php endif; ?>
    </div>
</div>
