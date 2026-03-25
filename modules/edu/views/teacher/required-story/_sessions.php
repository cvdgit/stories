<?php

declare(strict_types=1);

use modules\edu\models\EduStory;
use modules\edu\models\EduStudent;
use modules\edu\RequiredStory\repo\RequiredStorySession;
use yii\helpers\Html;

/**
 * @var EduStory $story
 * @var EduStudent $student
 * @var RequiredStorySession[] $sessions
 */
?>
<div>
    <h3><?= Html::encode($story->title) ?> - <?= Html::encode($student->name) ?></h3>
    <table class="table table-sm table-hover table-bordered">
        <thead>
        <tr>
            <th>Дата</th>
            <th>План</th>
            <th>Факт</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (count($sessions) === 0): ?>
            <tr>
                <td colspan="3">Нет данных</td>
            </tr>
        <?php
        else: ?>
            <?php
            foreach ($sessions as $session): ?>
                <tr>
                    <td><?= $session->getDate() ?></td>
                    <td><?= $session->getPlan() ?></td>
                    <td><?= $session->getFact() ?></td>
                </tr>
            <?php
            endforeach; ?>
        <?php
        endif ?>
        </tbody>
    </table>
</div>
