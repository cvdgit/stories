<?php

declare(strict_types=1);

use yii\web\View;

/**
 * @var View $this
 * @var int $sessionFact
 * @var int $sessionPlan
 * @var bool $sessionIsCompleted
 * @var int $fact
 * @var int $plan
 */
?>
<div id="required-story-stat" style="display: flex; flex-direction: column; gap: 10px">
    <div style="display: flex; flex-direction: row; gap: 10px; align-items: center">Ответов сегодня: <?= $sessionFact ?> из <?= $sessionPlan ?> <?= $sessionIsCompleted ? '<span class="label label-success">Обязательные задания на сегодня выполнены</span>' : '' ?></div>
    <div>Ответов всего за историю: <?= $fact ?> из <?= $plan ?></div>
</div>
