<?php

declare(strict_types=1);

use modules\edu\RequiredStory\repo\RequiredStoryItem;
use yii\helpers\Url;

/**
 * @var array $model
 * @var RequiredStoryItem $requiredStoryItem
 * @var array $stat
 */
[$requiredStoryItem, $stat] = $model;
?>
<div class="required-story-cell">
    <?= $requiredStoryItem->getStatus()->label() ?>
</div>
<div class="required-story-cell">
    <?= $requiredStoryItem->getStoryTitle() ?>
</div>
<div class="required-story-cell">
    <div
        data-required-story-url="<?= Url::to(['/edu/teacher/required-story/sessions', 'id' => $requiredStoryItem->getId()->toString()]) ?>"
        class="required-story-stat"
        style="display: flex; flex-direction: column; gap: 10px"
    >
        <div style="display: flex; flex-direction: row; gap: 10px; align-items: center">Сегодня: <?= $stat['sessionFact'] ?> из <?= $stat['sessionPlan'] ?></div>
        <div>Всего: <?= $stat['fact'] ?> из <?= $stat['plan'] ?></div>
    </div>
</div>
<div class="required-story-cell">
    <?= $requiredStoryItem->getStartedDate()->format('d.m.Y') ?>
</div>
<div class="required-story-cell">
    <?= $requiredStoryItem->getCreatedDate()->format('d.m.Y H:i:s') ?>
</div>
<div class="required-story-cell">
    <div style="display: flex; flex-direction: row; gap: 10px">
        <a style="padding: 4px 8px; border-radius: 4px" class="required-story-edit btn-primary"
           href="<?= Url::to(['/edu/teacher/required-story/edit', 'id' => $requiredStoryItem->getId()->toString()],
           ) ?>">Редактировать</a> |
        <a style="padding: 4px 8px; border-radius: 4px" class="required-story-delete btn-danger"
           href="<?= Url::to(['/edu/teacher/required-story/delete', 'id' => $requiredStoryItem->getId()->toString()],
           ) ?>">Удалить</a>
    </div>
</div>
