<?php

declare(strict_types=1);

use common\helpers\SmartDate;
use yii\web\View;

/**
 * @var View $this
 * @var array $rows
 */
?>
<div class="table-responsive">
    <table class="table table-bordered mental-map-table">
        <thead>
        <tr>
            <th>User ID</th>
            <th>Content</th>
            <th>All</th>
            <th>Hiding</th>
            <th>Target</th>
            <th>Threshold</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= $row['userName'] ?></td>
                <td><?= $row['content'] ?></td>
                <td><?= $row['overall_similarity'] ?></td>
                <td><?= $row['text_hiding_percentage'] ?></td>
                <td><?= $row['text_target_percentage'] ?></td>
                <td><?= $row['threshold'] ?></td>
                <td><?= SmartDate::dateSmart($row['created_at'], true) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
