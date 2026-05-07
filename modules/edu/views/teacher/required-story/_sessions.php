<?php

declare(strict_types=1);

use modules\edu\models\EduStory;
use modules\edu\models\EduStudent;
use modules\edu\RequiredStory\repo\RequiredStorySession;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var EduStory $story
 * @var EduStudent $student
 * @var RequiredStorySession[] $sessions
 */
?>
<div>
    <h3 style="margin-bottom: 20px"><?= Html::encode($story->title) ?> - <?= Html::encode($student->name) ?></h3>
    <table class="table table-sm table-hover table-bordered">
        <thead>
        <tr>
            <th>Дата</th>
            <th>План</th>
            <th>Факт</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (count($sessions) === 0): ?>
            <tr>
                <td colspan="4">Нет данных</td>
            </tr>
        <?php
        else: ?>
            <?php
            foreach ($sessions as $session): ?>
                <tr>
                    <td><?= $session->getDate() ?></td>
                    <td><?= $session->getPlan() ?></td>
                    <td><?= $session->getFact() ?></td>
                    <td>
                        <a class="remove-session" href="<?= Url::to(['/edu/teacher/required-story/remove-session', 'studentId' => $student->id, 'requiredStoryId' => $session->getRequiredStoryId()->toString(), 'date' => $session->getDate()]) ?>" style="color: #d43f3a" title="Удалить сессию">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" style="width: 24px; height: 24px; pointer-events: none">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </a>
                    </td>
                </tr>
            <?php
            endforeach; ?>
        <?php
        endif ?>
        </tbody>
    </table>
</div>
