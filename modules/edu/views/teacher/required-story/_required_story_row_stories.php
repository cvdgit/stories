<?php

declare(strict_types=1);

use modules\edu\RequiredStory\repo\ByStoriesItem;

/** @var array{0: ByStoriesItem, 1: array} $model */
[$item, $stat] = $model;
?>
<div class="required-story-cell">
    <?= $item->getStoryTitle() ?>
</div>
<div class="required-story-cell">
    <?php for ($i = 0; $i < count($item->getStudentNames()); $i++): ?>
    <div>
        <span><?= $item->getStudentNames()[$i] ?></span>
        <?php $studentStat = $stat[$item->getStudentIds()[$i]] ?? null ?>
    <?php if ($studentStat === true): ?>
    <span> (Пройдена)</span>
    <?php endif ?>
    </div>
    <?php endfor ?>

</div>
