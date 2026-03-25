<?php

declare(strict_types=1);

use modules\edu\RequiredStory\repo\RequiredStorySession;

/**
 * @var RequiredStorySession[] $sessions
 */
?>
<div>
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
        foreach ($sessions as $session): ?>
            <tr>
                <td><?= $session->getDate() ?></td>
                <td><?= $session->getPlan() ?></td>
                <td><?= $session->getFact() ?></td>
            </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table>
</div>
