<?php

declare(strict_types=1);

use modules\edu\RequiredStory\repo\RequiredStoryItem;
use yii\helpers\Url;

/**
 * @var RequiredStoryItem $model
 */
?>
<div class="required-story-cell">
    <?= $model->getStatus()->label() ?>
</div>
<div class="required-story-cell">
    <?= $model->getStoryTitle() ?>
</div>
<div class="required-story-cell">
    <?= $model->getStudentName() ?>
</div>
<div class="required-story-cell">
    <?= $model->getStartedDate()->format('d.m.Y') ?>
</div>
<div class="required-story-cell">
    <?= $model->getCreatedDate()->format('d.m.Y H:i:s') ?>
</div>
<div class="required-story-cell">
    <div style="display: flex; flex-direction: row; gap: 10px">
        <a style="padding: 4px 8px; border-radius: 4px" class="required-story-edit btn-primary"
           href="<?= Url::to(['/edu/teacher/required-story/edit', 'id' => $model->getId()->toString()],
           ) ?>">Редактировать</a> |
        <a style="padding: 4px 8px; border-radius: 4px" class="required-story-delete btn-danger"
           href="<?= Url::to(['/edu/teacher/required-story/delete', 'id' => $model->getId()->toString()],
           ) ?>">Удалить</a>
    </div>
</div>
